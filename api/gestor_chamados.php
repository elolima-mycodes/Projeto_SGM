<?php
// api/gestor_chamados.php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Middleware: Apenas Gestor
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Acesso negado. Apenas gestores podem acessar este recurso."]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            // Visualização detalhada de um chamado
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
            // Listagem geral para gestor com filtros
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
        // Gestor cria chamado direto -> Status "Agendado"
        $data = json_decode(file_get_contents("php://input"));
        if (!$data || !isset($data->descricao_problema, $data->id_ambiente)) {
            echo json_encode(["success" => false, "message" => "Dados obrigatórios ausentes."]);
            exit;
        }

        $id_solicitante = $_SESSION['user_id'];
        $id_ambiente = (int)$data->id_ambiente;
        $id_tipo_servico = isset($data->id_tipo_servico) ? (int)$data->id_tipo_servico : null;
        $descricao = $data->descricao_problema;
        $prioridade = isset($data->prioridade) ? $data->prioridade : 'baixa';
        $status = "agendado"; // Regra de negócio: Gestor cria como Agendado

        $stmt = $conn->prepare("INSERT INTO chamados (id_solicitante, id_ambiente, id_tipo_servico, descricao_problema, status, prioridade) 
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisss", $id_solicitante, $id_ambiente, $id_tipo_servico, $descricao, $status, $prioridade);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "id" => $stmt->insert_id]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao criar chamado: " . $stmt->error]);
        }
        break;

    case 'PUT':
        // Gestor edita/aprova/delega chamados
        $data = json_decode(file_get_contents("php://input"));
        if (!$data || !isset($data->id_chamado)) {
            echo json_encode(["success" => false, "message" => "ID do chamado necessário."]);
            exit;
        }

        $id = (int)$data->id_chamado;
        $fields = [];
        $params = [];
        $types = "";

        $allowedFields = ['id_tecnico', 'prioridade', 'data_prevista', 'status', 'descricao_problema', 'id_tipo_servico'];
        foreach ($allowedFields as $field) {
            if (isset($data->$field)) {
                $fields[] = "$field = ?";
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
