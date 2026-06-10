<?php
/**
 * api/solicitante_chamados.php
 * API para o perfil 'solicitante' gerenciar seus próprios chamados.
 * - Aceita/retorna JSON
 * - Usa $_SERVER['REQUEST_METHOD'] para roteamento (GET, POST, PUT, DELETE)
 * Regras principais:
 * - POST: cria chamado com status = 'aberto' e id_solicitante = usuário da sessão
 * - GET: lista e visualiza apenas chamados do solicitante
 * - PUT: permite editar a descrição enquanto o chamado estiver 'aberto'
 * - DELETE: permite excluir apenas se ainda estiver em estado que permita remoção
 */
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Autorização: apenas solicitante
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'solicitante') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Acesso negado. Apenas solicitantes podem usar este endpoint."]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Se houver ?id= retorna o chamado desse id apenas se for do solicitante
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $userId = $_SESSION['user_id'];

        if ($id > 0) {
            $stmt = $conn->prepare("SELECT 
                c.*, 
                a.nome as ambiente_nome, 
                b.nome as bloco_nome,
                b.id_bloco,
                u_tec.nome as tecnico_nome, 
                ts.nome as tipo_servico_nome
            FROM chamados c 
            JOIN ambientes a ON c.id_ambiente = a.id_ambiente 
            JOIN blocos b ON a.id_bloco = b.id_bloco
            LEFT JOIN usuarios u_tec ON c.id_tecnico = u_tec.id_usuario 
            LEFT JOIN tipos_servico ts ON c.id_tipo_servico = ts.id_tipo
            WHERE c.id_chamado = ? AND c.id_solicitante = ?");
            
            $stmt->bind_param("ii", $id, $userId);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                echo json_encode($row);
            } else {
                http_response_code(404);
                echo json_encode(["success" => false, "message" => "Chamado não encontrado ou sem permissão."]);
            }
        } else {
            // Lista todos os chamados do solicitante
            $stmt = $conn->prepare("SELECT 
                c.id_chamado, 
                c.descricao_problema, 
                c.status, 
                c.prioridade, 
                c.data_abertura, 
                c.id_ambiente,
                c.id_tipo_servico,
                a.nome as ambiente_nome, 
                b.nome as bloco_nome,
                b.id_bloco
            FROM chamados c 
            JOIN ambientes a ON c.id_ambiente = a.id_ambiente 
            JOIN blocos b ON a.id_bloco = b.id_bloco
            WHERE c.id_solicitante = ? 
            ORDER BY c.data_abertura DESC");
            
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
        }
        break;

    case 'POST':
        // Cria chamado: exige descricao_problema e id_ambiente no JSON
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
        $status = 'aberto'; // regra: solicitante abre como 'aberto'

        $stmt = $conn->prepare("INSERT INTO chamados (id_solicitante, id_ambiente, id_tipo_servico, descricao_problema, status, prioridade) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisss", $id_solicitante, $id_ambiente, $id_tipo_servico, $descricao, $status, $prioridade);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "id" => $stmt->insert_id]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao criar chamado: " . $stmt->error]);
        }
        break;

    case 'PUT':
        // Atualização parcial: solicitante pode alterar descrição, ambiente e tipo de serviço enquanto status for 'aberto'
        $data = json_decode(file_get_contents("php://input"));
        if (!$data || !isset($data->id_chamado)) {
            echo json_encode(["success" => false, "message" => "ID do chamado necessário."]);
            exit;
        }

        $id = (int)$data->id_chamado;
        $userId = $_SESSION['user_id'];

        // Verifica propriedade e status atual
        $stmt = $conn->prepare("SELECT status FROM chamados WHERE id_chamado = ? AND id_solicitante = ?");
        $stmt->bind_param("ii", $id, $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        if (!$row = $res->fetch_assoc()) {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Chamado não encontrado ou sem permissão."]);
            exit;
        }

        if ($row['status'] !== 'aberto') {
            echo json_encode(["success" => false, "message" => "Somente chamados com status 'aberto' podem ser editados pelo solicitante."]);
            exit;
        }

        $descricao = isset($data->descricao_problema) ? trim($data->descricao_problema) : '';
        $id_ambiente = isset($data->id_ambiente) ? (int)$data->id_ambiente : 0;
        $id_tipo_servico = isset($data->id_tipo_servico) ? (int)$data->id_tipo_servico : 0;

        if (empty($descricao) || $id_ambiente <= 0 || $id_tipo_servico <= 0) {
            echo json_encode(["success" => false, "message" => "Preencha todos os campos obrigatórios."]);
            exit;
        }

        $stmt = $conn->prepare("UPDATE chamados SET descricao_problema = ?, id_ambiente = ?, id_tipo_servico = ? WHERE id_chamado = ? AND id_solicitante = ?");
        $stmt->bind_param("siiii", $descricao, $id_ambiente, $id_tipo_servico, $id, $userId);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao atualizar: " . $stmt->error]);
        }
        break;

    case 'DELETE':
        // Exclusão: permite remover chamado se for do solicitante e ainda estiver em estado removível (ex: 'aberto')
        $data = json_decode(file_get_contents("php://input"));
        $id = isset($data->id_chamado) ? (int)$data->id_chamado : 0;
        $userId = $_SESSION['user_id'];

        if ($id <= 0) {
            echo json_encode(["success" => false, "message" => "ID inválido."]);
            exit;
        }

        // Verifica propriedade e status
        $stmt = $conn->prepare("SELECT status FROM chamados WHERE id_chamado = ? AND id_solicitante = ?");
        $stmt->bind_param("ii", $id, $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        if (!$row = $res->fetch_assoc()) {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Chamado não encontrado ou sem permissão."]);
            exit;
        }

        if ($row['status'] !== 'aberto') {
            echo json_encode(["success" => false, "message" => "Somente chamados com status 'aberto' podem ser excluídos pelo solicitante."]);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM chamados WHERE id_chamado = ? AND id_solicitante = ?");
        $stmt->bind_param("ii", $id, $userId);
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
