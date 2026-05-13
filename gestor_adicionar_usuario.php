<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'Gestor - Adicionar Usuário';
$activePage = 'usuarios';
$pageHeading = 'Adicionar Usuário';
$pageSubheading = 'Registre um novo utilizador no sistema.';
$pageActionLabel = '';
$pageActionLink = '';
require_once 'includes/gestor_layout.php';
?>

<div class="content-panel col-lg-6 px-0">
    <form class="card-soft p-4">
        <h2 class="fw-bold text-center mb-4">Adicionar Usuário</h2>
        <div class="mb-3">
            <label class="form-label fw-bold">Nome</label>
            <input type="text" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Email</label>
            <input type="email" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Perfil</label>
            <select class="form-select">
                <option value="">Selecione o perfil...</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Ativo</label>
            <select class="form-select">
                <option value="">Selecione</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Senha</label>
            <input type="password" class="form-control">
        </div>
        <div class="d-flex gap-2 justify-content-center">
            <a href="gestor_lista_usuarios.php" class="btn btn-outline-secondary py-2 px-4">Cancelar</a>
            <button type="submit" class="btn btn-primary py-2 px-4">Adicionar</button>
        </div>
    </form>
</div>

<?php require_once 'includes/gestor_footer.php'; ?>