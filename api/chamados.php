<?php
// api/chamados.php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Sessão expirada ou não autorizada."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$perfil = $_SESSION['user_perfil'];
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Filtro dinâmico por perfil (Visibilidade Restrita)
        $where = "";
        if ($perfil === 'solicitante') {
            $where = " AND c.id_solicitante = ?";
        } else if ($perfil === 'tecnico') {
            $where = " AND c.id_tecnico = ?";
        } else if ($perfil === 'gestor') {
            // Gestores devem preferencialmente usar api/gestor_chamados.php, 
            // mas mantemos compatibilidade caso necessário para visualização simples.
            $where = " AND 1=1"; 
        } else {
            echo json_encode([]); exit;
        }

        if ($id > 0) {
            $sql = "SELECT c.*, a.nome as ambiente_nome, b.nome as bloco_nome, u.nome as solicitante_nome
                    FROM chamados c
                    JOIN ambientes a ON c.id_ambiente = a.id_ambiente
                    JOIN blocos b ON a.id_bloco = b.id_bloco
                    JOIN usuarios u ON c.id_solicitante = u.id_usuario
                    WHERE c.id_chamado = ?" . $where;
            
            $stmt = $conn->prepare($sql);
            if ($perfil === 'gestor') {
                $stmt->bind_param("i", $id);
            } else {
                $stmt->bind_param("ii", $id, $user_id);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                echo json_encode($row);
            } else {
                http_response_code(404);
                echo json_encode(["success" => false, "message" => "Chamado não encontrado ou sem permissão."]);
            }
        } else {
            $status = isset($_GET['status']) ? $_GET['status'] : '';
            $sql = "SELECT c.id_chamado, c.descricao_problema, c.status, c.prioridade, c.data_abertura,
                           a.nome as ambiente_nome, b.nome as bloco_nome, u.nome as solicitante_nome
                    FROM chamados c
                    JOIN ambientes a ON c.id_ambiente = a.id_ambiente
                    JOIN blocos b ON a.id_bloco = b.id_bloco
                    JOIN usuarios u ON c.id_solicitante = u.id_usuario
                    WHERE 1=1" . $where;
            
            if ($status) {
                $sql .= " AND c.status = ?";
                $stmt = $conn->prepare($sql . " ORDER BY c.data_abertura DESC");
                if ($perfil === 'gestor') {
                    $stmt->bind_param("s", $status);
                } else {
                    $stmt->bind_param("is", $user_id, $status);
                }
            } else {
                $stmt = $conn->prepare($sql . " ORDER BY c.data_abertura DESC");
                if ($perfil !== 'gestor') {
                    $stmt->bind_param("i", $user_id);
                }
            }
            
            $stmt->execute();
            echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
        }
        break;

    case 'POST':
        // Apenas Solicitantes criam chamados aqui -> Status "Aberto"
        if ($perfil !== 'solicitante') {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Apenas solicitantes podem abrir chamados aqui."]);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"));
        if (!$data || !isset($data->descricao_problema, $data->id_ambiente)) {
            echo json_encode(["success" => false, "message" => "Dados incompletos."]);
            exit;
        }

        $id_ambiente = (int)$data->id_ambiente;
        $id_tipo_servico = isset($data->id_tipo_servico) ? (int)$data->id_tipo_servico : null;
        $descricao = $data->descricao_problema;
        $status = "aberto"; // Regra de negócio: Solicitante cria como Aberto

        $stmt = $conn->prepare("INSERT INTO chamados (id_solicitante, id_ambiente, id_tipo_servico, descricao_problema, status) 
                                VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiss", $user_id, $id_ambiente, $id_tipo_servico, $descricao, $status);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "id" => $stmt->insert_id]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao criar chamado: " . $stmt->error]);
        }
        break;

    case 'PUT':
        // Técnicos podem atualizar status (em_execucao/concluido) e solucao_tecnica
        if ($perfil !== 'tecnico') {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Apenas técnicos podem atualizar chamados aqui."]);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"));
        if (!$data || !isset($data->id_chamado)) {
            echo json_encode(["success" => false, "message" => "ID do chamado ausente."]);
            exit;
        }

        $id = (int)$data->id_chamado;
        $status = isset($data->status) ? $data->status : null;
        $solucao = isset($data->solucao_tecnica) ? $data->solucao_tecnica : null;

        if (!$status && !$solucao) {
            echo json_encode(["success" => false, "message" => "Nada para atualizar."]);
            exit;
        }

        // Garante que o técnico só edite o que lhe foi atribuído
        $sql = "UPDATE chamados SET status = COALESCE(?, status), solucao_tecnica = COALESCE(?, solucao_tecnica) 
                WHERE id_chamado = ? AND id_tecnico = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $status, $solucao, $id, $user_id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Não foi possível atualizar (verifique se o chamado está atribuído a você)."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Método não permitido."]);
        break;
}