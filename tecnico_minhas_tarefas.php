<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] != 'tecnico') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'SGM - Minhas Tarefas';
$activePage = 'minhas_tarefas';
$pageHeading = 'Minha Fila de Trabalho';
$pageSubheading = 'Gerencie suas tarefas designadas para hoje.';

require_once 'includes/user_layout.php';
?>

<div class="content-panel">
    <div id="listaTarefas">
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-check2-circle text-success opacity-25" style="font-size: 5rem;"></i>
            </div>
            <h4 class="fw-bold text-muted">Nenhuma tarefa pendente!</h4>
            <p class="text-muted">Relaxe um pouco ou aguarde novas atribuições do gestor.</p>
        </div>
    </div>
</div>

<?php require_once 'includes/user_footer.php'; ?>