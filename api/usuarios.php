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

        // Atualiza apenas os campos que foram enviados no JSON.
        $fields = [];
        $values = [];
        $types = "";

        if (isset($data->nome)) {
            $fields[] = "nome = ?";
            $values[] = $conn->real_escape_string($data->nome);
            $types .= "s";
        }
        if (isset($data->email)) {
            $fields[] = "email = ?";
            $values[] = $conn->real_escape_string($data->email);
            $types .= "s";
        }
        if (isset($data->perfil)) {
            $fields[] = "perfil = ?";
            $values[] = $conn->real_escape_string($data->perfil);
            $types .= "s";
        }
        if (isset($data->ativo)) {
            $fields[] = "ativo = ?";
            $values[] = (int)$data->ativo;
            $types .= "i";
        }

        $updateSenha = "";
        if (isset($data->senha) && $data->senha !== "") {
            $senha_hash = password_hash($data->senha, PASSWORD_DEFAULT);
            $fields[] = "senha_hash = ?";
            $values[] = $senha_hash;
            $types .= "s";
        }

        if (empty($fields)) {
            echo json_encode(["success" => false, "message" => "Nenhum campo para atualizar."]);
            exit;
        }

        $sql = "UPDATE usuarios SET " . implode(", ", $fields) . " WHERE id_usuario = ?";
        $values[] = $id;
        $types .= "i";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao atualizar: " . $stmt->error]);
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
