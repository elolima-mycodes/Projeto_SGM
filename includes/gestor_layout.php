<?php
if (!isset($pageTitle)) {
    $pageTitle = 'SGM';
}
if (!isset($activePage)) {
    $activePage = '';
}
if (!isset($pageHeading)) {
    $pageHeading = '';
}
if (!isset($pageSubheading)) {
    $pageSubheading = '';
}
if (!isset($pageActionLabel)) {
    $pageActionLabel = '';
}
if (!isset($pageActionLink)) {
    $pageActionLink = '';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/sgm-style.css">
</head>
<body>
    <nav class="sidebar">
        <h4 class="fw-bold mb-5"><i class="bi bi-shield-check"></i> SGM</h4>
        <div class="nav flex-column">
            <a href="gestor_dashboard.php" class="nav-link <?= $activePage === 'dashboard' ? 'active' : '' ?>"><i class="bi bi-grid-1x2"></i> Dashboard</a>
            <a href="gestor_chamados.php" class="nav-link <?= $activePage === 'chamados' ? 'active' : '' ?>"><i class="bi bi-ticket-perforated"></i> Chamados</a>
            <a href="gestor_lista_blocos.php" class="nav-link <?= $activePage === 'infraestrutura' ? 'active' : '' ?>"><i class="bi bi-building"></i> Infraestrutura</a>
            <a href="gestor_lista_usuarios.php" class="nav-link <?= $activePage === 'usuarios' ? 'active' : '' ?>"><i class="bi bi-people"></i> Utilizadores</a>
            <hr class="text-secondary mx-0">
            <a href="api/logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-left"></i> Sair</a>
        </div>
    </nav>

    <main class="main-content">
        <?php if ($pageHeading !== ''): ?>
        <header class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold m-0"><?= htmlspecialchars($pageHeading) ?></h2>
                <?php if ($pageSubheading !== ''): ?>
                    <p class="text-muted mb-0"><?= htmlspecialchars($pageSubheading) ?></p>
                <?php endif; ?>
            </div>
            <?php if ($pageActionLabel !== '' && $pageActionLink !== ''): ?>
                <a href="<?= htmlspecialchars($pageActionLink) ?>" class="btn btn-primary px-4 rounded-pill">
                    <i class="bi bi-plus-lg"></i> <?= htmlspecialchars($pageActionLabel) ?>
                </a>
            <?php endif; ?>
        </header>
        <?php endif; ?>
