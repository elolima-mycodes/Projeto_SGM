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
        $blocos = [];
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            $sql = "SELECT b.id_bloco, b.nome, 
                GROUP_CONCAT(a.nome SEPARATOR ', ') as nomes_ambientes
                FROM blocos b 
                LEFT JOIN ambientes a ON a.id_bloco = b.id_bloco
                WHERE b.id_bloco = $id
                GROUP BY b.id_bloco";
        } else {
            $sql = "SELECT b.id_bloco, b.nome, 
                GROUP_CONCAT(a.nome SEPARATOR ', ') as nomes_ambientes
                FROM blocos b 
                LEFT JOIN ambientes a ON a.id_bloco = b.id_bloco
                GROUP BY b.id_bloco
                ORDER BY b.nome ASC";
        }

        $result = $conn->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $blocos[] = $row;
            }
        }
        
        echo json_encode(["success" => true, "data" => $blocos]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->nome)) {
            echo json_encode(["success" => false, "message" => "Dados incompletos. Informe o nome do bloco"]);
            exit;
        }

        $nome = $conn->real_escape_string(trim($data->nome));
        $descricao = isset($data->descricao) ? $conn->real_escape_string(trim($data->descricao)) : '';

        $sql = "INSERT INTO blocos (nome, descricao) VALUES ('$nome', '$descricao')";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Bloco criado com sucesso!", "id_bloco" => $conn->insert_id]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao criar bloco: " . $conn->error]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->nome) || !isset($data->id_bloco)) {
            echo json_encode(["success" => "false", "message" => "Dados incompletos para atualização."]);
            exit;
        }

        $id_bloco = (int)$data->id_bloco;
        $nome = $conn->real_escape_string(trim($data->nome));

        $sql = "UPDATE blocos SET nome = '$nome' WHERE id_bloco = $id_bloco";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Bloco atualizado com sucesso!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao atualizar bloco: " . $conn->error]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->id_bloco)) {
            echo json_encode(["success" => "false", "message" => "ID do bloco não informado."]);
            exit;
        }

        $id_bloco = (int)$data->id_bloco;

        $sql = "DELETE FROM blocos WHERE id_bloco = $id_bloco";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Bloco excluído com sucesso!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao excluir: Pode haver dados vinculados a este bloco. " . $conn->error]);
        }
        break;

    default:
        echo json_encode(["success" => false, "message" => "Método HTTP não suportado."]);
        break;

}

?>