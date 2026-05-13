<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'Gestor - Adicionar Serviço';
$activePage = 'infraestrutura';
$pageHeading = 'Adicionar Serviço';
$pageSubheading = 'Crie um novo tipo de serviço para o sistema.';
$pageActionLabel = '';
$pageActionLink = '';
require_once 'includes/gestor_layout.php';
?>

<div class="content-panel col-lg-6 px-0">
    <form id="formAdicionarAmbiente" class="card-soft p-4">
        <h2 class="fw-bold text-center mb-4">Adicionar Serviço</h2>
        <div class="mb-3">
            <label class="form-label fw-bold">Nome</label>
            <textarea id="nomeAmbiente" class="form-control" rows="1"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Descrição</label>
            <textarea id="nomeAmbiente" class="form-control" rows="3"></textarea>
        </div>
        <div class="d-flex gap-2 justify-content-center">
            <a href="gestor_lista_tipos_de_servico.php" class="btn btn-outline-secondary py-2 px-4">Cancelar</a>
            <button type="submit" class="btn btn-primary py-2 px-4">Adicionar</button>
        </div>
    </form>
</div>

<?php require_once 'includes/gestor_footer.php'; ?>