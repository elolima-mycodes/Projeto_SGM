<?php
session_start();
require_once'../config/database.php';
header('Content-Type: application/json');

$id_chamado = $_GET['id_chamado'];

$sql = "SELECT
  ca.caminho_arquivo,
  ca.tipo_anexo
  from chamados_anexos ca
  where ca.id_chamado = $id_chamado";



$res = $conn->query($sql);
if ($res) {
    $dados = [];
    while ($linha = $res->fetch_assoc()) {
        $dados[] = $linha;
    }
    echo json_encode($dados);
} else {
    // Caso a query falhe (erro de coluna, etc)
    echo json_encode(["success" => false, "message" => "Erro no SQL: " . $conn->error]);
}