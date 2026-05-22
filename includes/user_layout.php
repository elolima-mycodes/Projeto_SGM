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
    <style>
        /* Ajustes para layout sem sidebar */
        .main-content-user {
            padding: 2rem;
            min-height: calc(100vh - 70px);
            background-color: var(--bg-light);
        }
        .navbar-user {
            background-color: var(--primary-dark);
            padding: 0.8rem 2rem;
        }
        .navbar-user .nav-link {
            color: #94a3b8;
            margin-bottom: 0;
            padding: 0.5rem 1rem;
        }
        .navbar-user .nav-link:hover,
        .navbar-user .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
        }
        .mini-thumb {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.2s;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .mini-thumb:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-user sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="bi bi-shield-check text-primary"></i> SGM</a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <?php if ($_SESSION['user_perfil'] === 'solicitante'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $activePage === 'dashboard' ? 'active' : '' ?>" href="solicitante_dashboard.php">Minhas Solicitações</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $activePage === 'abrir_chamado' ? 'active' : '' ?>" href="solicitante_abrir_chamado.php">Novo Chamado</a>
                        </li>
                    <?php elseif ($_SESSION['user_perfil'] === 'tecnico'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $activePage === 'minhas_tarefas' ? 'active' : '' ?>" href="tecnico_minhas_tarefas.php">Minhas Tarefas</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item ms-lg-3">
                        <div class="d-flex align-items-center">
                            <span class="text-white-50 me-3 small d-none d-md-inline">Olá, <?= explode(' ', $_SESSION['user_nome'])[0] ?></span>
                            <a href="api/logout.php" class="btn btn-sm btn-outline-danger rounded-pill px-3">Sair</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="main-content-user">
        <div class="container">
            <?php if ($pageHeading !== ''): ?>
            <header class="mb-4">
                <h2 class="fw-bold m-0"><?= htmlspecialchars($pageHeading) ?></h2>
                <?php if ($pageSubheading !== ''): ?>
                    <p class="text-muted mb-0"><?= htmlspecialchars($pageSubheading) ?></p>
                <?php endif; ?>
            </header>
            <?php endif; ?>
