<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$sql = "SELECT id_usuario, nome FROM usuarios WHERE perfil = 'tecnico' and ativo = 1 order by nome asc";
$result = $conn->query($sql);
$tecnicos = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($tecnicos);


