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
        $sql = "SELECT a.id_ambiente, a.nome, a.id_bloco, b.nome as nome_bloco FROM ambientes a left join blocos b on a.id_bloco = b.id_bloco order by a.id_ambiente asc";

        $result = $conn->query($sql);
        $ambientes = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $ambientes[] = $row;
            }
        }
        echo json_encode(["success" => true, "data" => $ambientes]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if (!isset($data->nome) || !isset($data->id_bloco)) {
            echo json_encode(["success" => "false", "message" => "Dados incompletos. Informe nome e id_bloco."]);
            exit;
        }

        $nome = $conn->real_escape_string(trim($data->nome));
        $id_bloco = (int)$data->id_bloco;


        $sql = "INSERT INTO ambientes (nome, id_bloco) VALUES ('$nome', $id_bloco)";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Ambiente criado com sucesso!", "id_ambiente" => $conn->insert_id]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao criar ambiente: " . $conn->error]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));

        if(!isset($data->id_ambiente) || !isset($data->nome) || !isset($data->id_bloco)) {
            echo json_encode(["success" => false, "message" => "Dados incompletos para atualização."]);
            exit;
        }

        $id_ambiente = (int)$data->id_ambiente;
        $nome = $conn->real_escape_string(trim($data->nome));
        $id_bloco = (int)$data->id_bloco;

        $sql = "UPDATE ambientes SET nome = '$nome', id_bloco = $id_bloco WHERE id_ambiente = $id_ambiente";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Ambiente atualizado com sucesso!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao atualizar ambiente: " . $conn->error]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));

        if(!isset($data->id_ambiente)) {
            echo json_encode(["success" => false, "message" => "ID do ambiente não fornecido."]);
            exit;
        }

        $id_ambiente = (int)$data->id_ambiente;
        
        $sql = "DELETE FROM ambientes WHERE id_ambiente = $id_ambiente";

        if($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Ambiente excluído com sucesso!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao excluir: Pode haver dados vinculados a este ambiente."]);
        }
        break;

    default:
        echo json_encode(["success" => false, "message" => "Método HTTP não suportado."]);
        break;

}

?>