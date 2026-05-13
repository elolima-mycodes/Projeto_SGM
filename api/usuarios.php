<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Proteção de acesso
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Buscar um usuário específico
            $id = (int)$_GET['id'];
            $sql = "SELECT id_usuario, nome, email, perfil, ativo FROM usuarios WHERE id_usuario = $id";
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
                echo json_encode(["success" => true, "data" => $result->fetch_assoc()]);
            } else {
                echo json_encode(["success" => false, "message" => "Usuário não encontrado."]);
            }
        } else {
            // Listar usuários (com filtros opcionais)
            $where = "1=1";
            if (isset($_GET['perfil'])) {
                $perfil = $conn->real_escape_string($_GET['perfil']);
                $where .= " AND perfil = '$perfil'";
            }
            if (isset($_GET['ativo'])) {
                $ativo = (int)$_GET['ativo'];
                $where .= " AND ativo = $ativo";
            }

            $sql = "SELECT id_usuario, nome, email, perfil, ativo FROM usuarios WHERE $where ORDER BY nome ASC";
            $result = $conn->query($sql);
            $usuarios = [];
            while ($row = $result->fetch_assoc()) {
                $usuarios[] = $row;
            }
            echo json_encode([
    "success" => true, 
    "data" => $usuarios
]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!$data || !isset($data->nome, $data->email, $data->senha, $data->perfil)) {
            echo json_encode(["success" => false, "message" => "Dados incompletos."]);
            exit;
        }

        $nome = $conn->real_escape_string($data->nome);
        $email = $conn->real_escape_string($data->email);
        $perfil = $conn->real_escape_string($data->perfil);
        $ativo = isset($data->ativo) ? (int)$data->ativo : 1;
        $senha_hash = password_hash($data->senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nome, email, senha_hash, perfil, ativo) 
                VALUES ('$nome', '$email', '$senha_hash', '$perfil', $ativo)";

        if ($conn->query($sql)) {
            echo json_encode(["success" => true, "id" => $conn->insert_id]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao inserir: " . $conn->error]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (!$data || !isset($data->id_usuario)) {
            echo json_encode(["success" => false, "message" => "ID não fornecido."]);
            exit;
        }

        $id = (int)$data->id_usuario;
        $nome = $conn->real_escape_string($data->nome);
        $email = $conn->real_escape_string($data->email);
        $perfil = $conn->real_escape_string($data->perfil);
        $ativo = (int)$data->ativo;

        $updateSenha = "";
        if (isset($data->senha) && !empty($data->senha)) {
            $senha_hash = password_hash($data->senha, PASSWORD_DEFAULT);
            $updateSenha = ", senha_hash = '$senha_hash'";
        }

        $sql = "UPDATE usuarios SET 
                nome = '$nome', 
                email = '$email', 
                perfil = '$perfil', 
                ativo = $ativo 
                $updateSenha
                WHERE id_usuario = $id";

        if ($conn->query($sql)) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao atualizar: " . $conn->error]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        if (!$data || !isset($data->id_usuario)) {
            echo json_encode(["success" => false, "message" => "ID não fornecido."]);
            exit;
        }

        $id = (int)$data->id_usuario;
        $sql = "DELETE FROM usuarios WHERE id_usuario = $id";

        if ($conn->query($sql)) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao excluir: " . $conn->error]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Método não permitido."]);
        break;
}
