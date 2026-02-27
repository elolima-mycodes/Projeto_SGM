<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGM - Gestão de Ambientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
            :root {
        --azul-marinho: #1B263B;
        --azul-royal: #415A77;
        --gelo: #F8F9FA;
    }

    body { background-color: var(--gelo); }

    .navbar-custom {
        background-color: var(--azul-marinho);
        padding: 0.8rem 2rem;
    }

    .titulo-secao {
        color: var(--azul-marinho);
        font-weight: 800;
        text-align: center;
        margin-top: 2rem;
        margin-bottom: 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
        .table thead {
        background-color: var(--azul-marinho);
        color: white;
    }

    .table thead th {
        font-weight: 500;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 1px;
        padding: 15px;
        border: none;
    }

    .table tbody tr {
        transition: 0.2s;
    }

    .table tbody tr:hover {
        background-color: rgba(65, 90, 119, 0.05);
    }
        .btn-adicionar-ambiente {
        background-color: var(--azul-marinho);
        color: white;
        border-radius: 50px;
        padding: 12px 30px;
        font-weight: 600;
        transition: 0.3s;
        border: none;
    }

    .btn-adicionar-ambiente:hover {
        background-color: var(--azul-royal);
        color: white;
        transform: translateY(-2px);
        shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    </style>
</head>
<body class="bg-light">
        <nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="gestor_dashboard.php">
                <i class="bi bi-shield-check me-2"></i>SGM GESTÃO
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto align-items-center">
                    <button type="button" class="btn btn-outline-light btn-sm ms-lg-3" data-bs-toggle="modal" data-bs-target="#modalLogout">
                        <i class="bi bi-box-arrow-right me-1"></i> Sair
                    </button>
                </div>
            </div>
        </div>
    </nav>
    <div class="container">
            <a href="gestor_dashboard.php" class="btn btn-outline-secondary mb-4">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        <h2 class="mb-4">Todos os Ambientes</h2>
        <div class="text-center mb-5">
            <a href="gestor_adicionar_ambiente.php" class="btn btn-adicionar-ambiente shadow">
                <i class="bi bi-plus-lg me-2"></i> Adicionar Ambiente
            </a>
        </div>
        <div class="card shadow">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Bloco</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>#1</th>
                            <th>Recepção</th>
                            <th><i class="bi bi-building"></i>Bloco Administrativo</th>
                            <th><a href="gestor_editar_ambiente.php" class="btn btn-warning mb-3 mt-2"><i class="bi bi-pencil me-2"></i>Editar</a></th>
                            <th><button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalExcluir">
                            </i> <i class="bi bi-trash me-2"></i>Excluir</button></th>
                        </tr>
                        <tr>
                            <th>#3</th>
                            <th>Linha 1</th>
                            <th><i class="bi bi-building"></i>Bloco de Produção</th>
                            <th><a href="gestor_editar_ambiente.php"  class="btn btn-warning mb-3 mt-2"><i class="bi bi-pencil me-2"></i>Editar</a></th>
                            <th><button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalExcluir">
                            </i> <i class="bi bi-trash me-2"></i>Excluir</button></th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal fade" id="modalExcluir" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white justify-content-center" style="background-color: var(--azul-marinho)">
                    <h5 class="modal-title">Excluir Ambiente</h5>
                </div>
                <div class="modal-body text-center py-5">
                    <i class="bi bi-exclamation-circle text-danger display-3 mb-3"></i>
                    <p class="fs-5 text-secondary">Deseja excluir esse ambiente?</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-danger px-4">Excluir</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalLogout" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white justify-content-center" style="background-color: var(--azul-marinho)">
                    <h5 class="modal-title">Encerrar Sessão</h5>
                </div>
                <div class="modal-body text-center py-5">
                    <i class="bi bi-exclamation-circle text-danger display-3 mb-3"></i>
                    <p class="fs-5 text-secondary">Sua sessão será encerrada!<br>Deseja continuar?</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                    <a href="api/logout.php" class="btn btn-danger px-4">Sair</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>