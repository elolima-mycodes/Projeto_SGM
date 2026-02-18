<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

// 1. Forçar o ID a ser um inteiro para segurança
$id_chamado = isset($_GET['id_chamado']) ? (int)$_GET['id_chamado'] : 0;

if ($id_chamado > 0) {
    // 2. Query simplificada
    $sql = "SELECT caminho_arquivo, tipo_anexo 
            FROM chamados_anexos 
            WHERE id_chamado = $id_chamado";

    $res = $conn->query($sql);

    if ($res) {
        // fetch_all é mais rápido para transformar tudo em array de uma vez
        $dados = $res->fetch_all(MYSQLI_ASSOC);
        echo json_encode($dados);
    } else {
        // Em caso de erro no SQL, retornamos um array vazio para não travar o JS
        echo json_encode([]);
    }
} else {
    // Se não for passado um ID válido, retorna array vazio
    echo json_encode([]);
}