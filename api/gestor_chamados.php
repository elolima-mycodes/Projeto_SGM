<?php
/**
 * api/gestor_chamados.php
 * Endpoint para operações CRUD de chamados acessíveis apenas ao perfil 'gestor'.
 * Mantém o método HTTP via $_SERVER['REQUEST_METHOD'] e corpo em JSON.
 * Comentários em cada seção explicam a lógica para facilitar manutenção.
 */
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// ----------------------
// Middleware: apenas Gestor
// ----------------------
// Verifica sessão e perfil do usuário. Se não for gestor, retorna 403.
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Acesso negado. Apenas gestores podem acessar este recurso."]);
    exit;
}

// Método HTTP (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];

// Switch principal que roteia por método HTTP.
switch ($method) {
    case 'GET':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            // --- Visualização detalhada de um chamado por ID (Gestor vê tudo) ---
            // Recupera informações do chamado e joins para obter nomes relacionados.
            $stmt = $conn->prepare("SELECT c.*, a.nome as ambiente_nome, b.nome as bloco_nome, u.nome as solicitante_nome, t.nome as tecnico_nome
                                    FROM chamados c
                                    JOIN ambientes a ON c.id_ambiente = a.id_ambiente
                                    JOIN blocos b ON a.id_bloco = b.id_bloco
                                    JOIN usuarios u ON c.id_solicitante = u.id_usuario
                                    LEFT JOIN usuarios t ON c.id_tecnico = t.id_usuario
                                    WHERE c.id_chamado = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                echo json_encode($row);
            } else {
                http_response_code(404);
                echo json_encode(["success" => false, "message" => "Chamado não encontrado."]);
            }
        } else {
            // --- Listagem geral para gestor com filtros opcionais ---
            // Ex.: ?status=aberto
            $status = isset($_GET['status']) ? $_GET['status'] : '';
            $sql = "SELECT c.id_chamado, c.descricao_problema, c.status, c.prioridade, c.data_abertura,
                           a.nome as ambiente_nome, b.nome as bloco_nome, u.nome as solicitante_nome,
                           t.nome as tecnico_nome
                    FROM chamados c
                    JOIN ambientes a ON c.id_ambiente = a.id_ambiente
                    JOIN blocos b ON a.id_bloco = b.id_bloco
                    JOIN usuarios u ON c.id_solicitante = u.id_usuario
                    LEFT JOIN usuarios t ON c.id_tecnico = t.id_usuario
                    WHERE 1=1";
            
            if ($status) {
                // Filtra por status quando informado
                $sql .= " AND c.status = ?";
                $stmt = $conn->prepare($sql . " ORDER BY c.data_abertura DESC");
                $stmt->bind_param("s", $status);
            } else {
                $stmt = $conn->prepare($sql . " ORDER BY c.data_abertura DESC");
            }
            
            $stmt->execute();
            echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
        }
        break;

    case 'POST':
        // --- Criação de chamado pelo gestor ---
        // O gestor pode criar chamados que já ficam com status = 'agendado'.
        $data = json_decode(file_get_contents("php://input"));
        if (!$data || !isset($data->descricao_problema, $data->id_ambiente)) {
            // Rejeita quando campos obrigatórios não são enviados no JSON.
            echo json_encode(["success" => false, "message" => "Dados obrigatórios ausentes."]);
            exit;
        }

        $id_solicitante = $_SESSION['user_id'];
        $id_tecnico = isset($data->id_tecnico) ? (int)$data->id_tecnico : null; // O gestor pode atribuir um técnico na criação, mas é opcional.
        $id_ambiente = (int)$data->id_ambiente;
        $id_tipo_servico = isset($data->id_tipo_servico) ? (int)$data->id_tipo_servico : null;
        $descricao = $data->descricao_problema;
        $prioridade = isset($data->prioridade) ? $data->prioridade : 'baixa';
        $status = "agendado"; // Regra de negócio: Gestor cria como Agendado

        // Insere chamado na tabela 'chamados'. Se o campo id_tipo_servico for null, o bind aceita null.
        $stmt = $conn->prepare("INSERT INTO chamados (id_solicitante, id_ambiente, id_tipo_servico, descricao_problema, id_tecnico, status, prioridade) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisiss", $id_solicitante, $id_ambiente, $id_tipo_servico, $descricao, $id_tecnico, $status, $prioridade);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "id" => $stmt->insert_id]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao criar chamado: " . $stmt->error]);
        }
        break;

    case 'PUT':
        // --- Atualização de chamado pelo gestor ---
        // O gestor possui permissão para atualizar vários campos de um chamado.
        $data = json_decode(file_get_contents("php://input"));
        if (!$data || (!isset($data->id_chamado) && !isset($data->id))) {
            echo json_encode(["success" => false, "message" => "ID do chamado necessário. Use id_chamado ou id no JSON."]);
            exit;
        }

        // Aceita tanto id_chamado quanto id para compatibilidade com diferentes requisições.
        $id = isset($data->id_chamado) ? (int)$data->id_chamado : (int)$data->id;
        $fields = [];
        $params = [];
        $types = "";

        // Lista branca de campos que o gestor pode alterar. Evita updates acidentais em colunas sensíveis.
        $allowedFields = ['id_tecnico', 'prioridade', 'data_prevista', 'status', 'descricao_problema', 'id_tipo_servico'];
        foreach ($allowedFields as $field) {
            if (isset($data->$field)) {
                $fields[] = "$field = ?";
                // Normaliza valor vazio para NULL quando apropriado.
                $params[] = ($data->$field === "") ? null : $data->$field;
                $types .= (is_int($params[count($params)-1])) ? "i" : "s";
            }
        }

        if (empty($fields)) {
            echo json_encode(["success" => false, "message" => "Nenhum campo para atualizar."]);
            exit;
        }

        $params[] = $id;
        $types .= "i";

        $sql = "UPDATE chamados SET " . implode(", ", $fields) . " WHERE id_chamado = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao atualizar: " . $stmt->error]);
        }
        break;

    case 'DELETE':
        // --- Exclusão de chamado (Gestor apenas) ---
        // Recebe JSON com { "id_chamado": 123 }
        $data = json_decode(file_get_contents("php://input"));
        $id = isset($data->id_chamado) ? (int)$data->id_chamado : 0;
        
        if ($id <= 0) {
            echo json_encode(["success" => false, "message" => "ID inválido."]);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM chamados WHERE id_chamado = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao excluir: " . $stmt->error]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Método não permitido."]);
        break;
}
