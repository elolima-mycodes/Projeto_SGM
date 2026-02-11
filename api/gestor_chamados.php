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
             u_soli.nome as solicitante,
             a.nome as local,
             t.nome as serviço,
             c.prioridade,
             u_tec.nome as tecnico,
             c.status
             FROM chamados c
             LEFT JOIN usuarios u_soli ON c.id_solicitante = u_soli.id_usuario
             left join usuarios u_tec on c.id_tecnico = u_tec.id_usuario 
             LEFT JOIN ambientes a on c.id_ambiente = a.id_ambiente
             left join tipos_servico t on c.id_tipo_servico = t.id_tipo
             ORDER BY c.data_abertura DESc";



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