<?php
session_start();
require_once'../config/database.php';
header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    echo json_encode(["success" => false, "message" => "Acesso negado"]);
    exit;
}

$sql = "SELECT
             c.id_chamado,
             a.nome as local,
             c.descricao_problema,
             c.data_abertura,
             c.status
			 from chamados c 
             left join ambientes a on c.id_ambiente = a.id_ambiente
             ORDER BY c.data_abertura DESC";


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