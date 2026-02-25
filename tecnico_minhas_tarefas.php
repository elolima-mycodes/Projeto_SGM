<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Técnico - Minhas tarefas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

</head>
<body class="bg-light">
    <header>
        <nav class="navbar navbar-dark bg-dark shadow-sm mb-4">
            <div class="container-fluid px-4">
            <span class="navbar-brand mb-0 h1">SGM | Técnico</span>
            
            <div class="d-flex align-items-center">
                <span class="text-white me-3 d-none d-md-inline">Olá, Técnico     |</span>
                <button type="button" class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalLogout">Sair
                </button>
            </div>
        </div>
        </nav>
    </header>
    <main>
        <div>
            <div class="d-flex gap-3 justify-content-center">
                <h4 class="fw-bolder">Minha fila de trabalho</h4>
            </div>
            <div class="d-flex gap-3 justify-content-center">
                <h6>Nenhuma tarefa pendente!</h6>
            </div>
        </div>

        <div class="modal fade" id="modalLogout" tabindex="-1" aria-labelledby="modalLogoutLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white justify-content-center">
                    <h5 class="modal-title" id="modalLogoutLabel">Encerrar Sessão</h5>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="bi bi-exclamation-triangle text-warning display-4 mb-3"></i>
                    <p class="fs-5">Sua sessão será encerrada. Tem certeza disso?</p>
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Voltar</button>
                    <a href="api/logout.php" class="btn btn-danger px-4">Sair</a>
                </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>