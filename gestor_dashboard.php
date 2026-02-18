<?php
session_start();
// Segurança: Se não for gestor, volta para o login
if (!isset($_SESSION['user_perfil']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Gestor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
    <header>
      <nav class="navbar navbar-dark bg-dark shadow-sm mb-4">
        <div class="container-fluid px-4">
            <span class="navbar-brand mb-0 h1">SGM | Gestão</span>
            
            <div class="d-flex align-items-center">
                <span class="text-white me-3 d-none d-md-inline">Olá, Gestor     |</span>
                <a href="api/logout.php" class="btn btn-outline-light btn-sm">Sair</a>
            </div>
        </div>
      </nav>
    </header>
    <main>
        <div class="container">
        
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm text-center p-3 border-bottom border-primary border-5">
                        <div class="card-body">
                            <h6 class="text-muted fw-bold">Novas solicitações</h6>
                            <h2 class="display-4 fw-bold text-primary">0</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm text-center p-3 border-bottom border-warning border-5">
                        <div class="card-body">
                            <h6 class="text-muted fw-bold">Em Andamento</h6>
                            <h2 class="display-4 fw-bold text-warning">0</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm text-center p-3 border-bottom border-danger border-5">
                        <div class="card-body">
                            <h6 class="text-muted fw-bold">Crítico</h6>
                            <h2 class="display-4 fw-bold text-danger">0</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-3 justify-content-center">
                <button class="btn btn-primary px-4 py-2">
                    <i class="bi bi-list-check me-2"></i> Gerenciar Chamados
                </button>
                <button class="btn btn-secondary px-4 py-2">
                    <i class="bi bi-geo-alt"></i> Configurar Ambientes
                </button>
            </div>

        </div>
    </main>

    






    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>