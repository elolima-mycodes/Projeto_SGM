<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $sql = "SELECT t.id_tipo, t.nome FROM tipos_servico t ORDER BY t.nome ASC";

        $result = $conn->query($sql);
        $servicos = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $servicos[] = $row;
            }
        }
        echo json_encode(["success" => true, "data" => $servicos]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->nome)) {
            echo json_encode(["success" => "false", "message" => "Dados incompletos. Informe o nome do serviço."]);
            exit;
        }

        $nome = $conn->real_escape_string(trim($data->nome));

        $sql = "INSERT INTO tipos_servico (nome) VALUES ('$nome')";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Serviço criado com sucesso!", "id_tipo" => $conn->insert_id]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao criar serviço." . $conn->error]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->nome) || !isset($data->id_tipo)) {
            echo json_encode(["success" => "false", "message" => "Dados incompletos para atualização."]);
            exit;
        }

        $id_tipo = (int)$data->id_tipo;
        $nome = $conn->real_escape_string(trim($data->nome));

        $sql = "UPDATE tipos_servico SET nome = '$nome' WHERE id_tipo = $id_tipo";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Serviço atualizado com sucesso!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao atualizar serviço: " . $conn->error]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->id_tipo)) {
            echo json_encode(["success" => "false", "message" => "ID do serviço não informado."]);
            exit;
        }

        $id_tipo = (int)$data->id_tipo;

        $sql = "DELETE FROM tipos_servico WHERE id_tipo = $id_tipo";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Serviço excluído com sucesso!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao excluir: Pode haver dados vinculados a este serviço. " . $conn->error]);
        }
        break;

    default:
        echo json_encode(["success" => false, "message" => "Método HTTP não suportado."]);
        break;

}

?>