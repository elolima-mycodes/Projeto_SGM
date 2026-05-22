<?php
/**
 * api/tecnico_chamados.php
 * API para o perfil 'tecnico' manipular chamados atribuídos a ele.
 * - Aceita/retorna JSON
 * - Usa $_SERVER['REQUEST_METHOD'] para roteamento (GET, PUT)
 * Regras principais:
 * - GET: lista e visualiza apenas chamados atribuídos ao técnico
 * - PUT: técnico pode atualizar 'status', 'data_previsao_conclusao' e 'solucao_tecnica'
 * - Quando o técnico fecha um chamado (status = 'fechado') o servidor define 'data_fechamento'
 *   e calcula uma estimativa simples de 'tempo_gasto' em minutos (TIMESTAMPDIFF)
 */
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Autorização: apenas técnico
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'tecnico') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Acesso negado. Apenas técnicos podem usar este endpoint."]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Lista chamados atribuídos ao técnico ou visualiza um chamado específico
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $tecId = $_SESSION['user_id'];

        if ($id > 0) {
            $stmt = $conn->prepare("SELECT c.*, a.nome as ambiente_nome, u.nome as solicitante_nome FROM chamados c JOIN ambientes a ON c.id_ambiente = a.id_ambiente JOIN usuarios u ON c.id_solicitante = u.id_usuario WHERE c.id_chamado = ? AND c.id_tecnico = ?");
            $stmt->bind_param("ii", $id, $tecId);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                echo json_encode($row);
            } else {
                http_response_code(404);
                echo json_encode(["success" => false, "message" => "Chamado não encontrado ou não atribuído a este técnico."]);
            }
        } else {
            $stmt = $conn->prepare("SELECT id_chamado, descricao_problema, status, prioridade, data_abertura, data_previsao_conclusao FROM chamados WHERE id_tecnico = ? ORDER BY data_abertura DESC");
            $stmt->bind_param("i", $tecId);
            $stmt->execute();
            echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
        }
        break;

    case 'PUT':
        // Atualização pelo técnico: somente campos permitidos
        $data = json_decode(file_get_contents("php://input"));
        if (!$data || !isset($data->id_chamado)) {
            echo json_encode(["success" => false, "message" => "ID do chamado necessário."]);
            exit;
        }

        $id = (int)$data->id_chamado;
        $tecId = $_SESSION['user_id'];

        // Verifica que o técnico é o atribuído a este chamado
        $stmt = $conn->prepare("SELECT status, data_abertura FROM chamados WHERE id_chamado = ? AND id_tecnico = ?");
        $stmt->bind_param("ii", $id, $tecId);
        $stmt->execute();
        $res = $stmt->get_result();
        if (!$row = $res->fetch_assoc()) {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Chamado não encontrado ou não atribuído a este técnico."]);
            exit;
        }

        $allowed = ['status', 'data_previsao_conclusao', 'solucao_tecnica'];
        $fields = [];
        $params = [];
        $types = "";

        foreach ($allowed as $f) {
            if (isset($data->$f)) {
                $fields[] = "{$f} = ?";
                $params[] = ($data->$f === "") ? null : $data->$f;
                $types .= (is_int($params[count($params)-1])) ? "i" : "s";
            }
        }

        if (empty($fields)) {
            echo json_encode(["success" => false, "message" => "Nenhum campo permitido para atualização pelo técnico."]);
            exit;
        }

        // Se for fechar o chamado, setamos data_fechamento e calculamos tempo_gasto (minutos)
        $closing = (isset($data->status) && $data->status === 'fechado');

        if ($closing) {
            // Adiciona campos de fechamento
            $fields[] = "data_fechamento = NOW()";
            // tempo_gasto calculado em minutos via SQL usando TIMESTAMPDIFF
            $fields[] = "tempo_gasto = TIMESTAMPDIFF(MINUTE, data_abertura, NOW())";
        }

        // Monta e executa update
        $sql = "UPDATE chamados SET " . implode(", ", $fields) . " WHERE id_chamado = ? AND id_tecnico = ?";
        $params[] = $id;
        $params[] = $tecId;
        $types .= "ii";

        $stmt = $conn->prepare($sql);
        // bind_param exige variables, usamos splat operator
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao atualizar: " . $stmt->error]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Método não permitido."]);
        break;
}
