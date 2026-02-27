<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>SGM - Gestão de Chamados</title>
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
    .titulo-secao::after {
        content: "";
        width: 80px;
        height: 4px;
        background: var(--azul-royal);
        display: block;
        margin-top: 10px;
        border-radius: 2px;
    }

    .filtros-container {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .btn-filtro {
        border-radius: 20px;
        padding: 6px 20px;
        font-weight: 600;
        transition: 0.3s;
    }

    .card-tabela {
        border: none;
        border-radius: 15px;
        overflow: hidden;
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

    .badge-status {
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.75rem;
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
        <h2 class="mb-4">Todos os Chamados</h2>

        <div class="mb-3 d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary" onclick="carregarChamados('')">Todos</button>
            <button class="btn btn-sm btn-outline-primary" onclick="carregarChamados('aberto')">Abertos</button>
            <button class="btn btn-sm btn-outline-warning" onclick="carregarChamados('em_execucao')">Em Execução</button>
            <button class="btn btn-sm btn-outline-success" onclick="carregarChamados('concluido')">Concluídos</button>
        </div>

        <div class="card shadow">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Solicitante</th>
                            <th>Local / Tipo</th>
                            <th>Prioridade</th>
                            <th>Técnico</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaGeral">
                        </tbody>
                </table>
            </div>
        </div>
    </div>
<div class="modal fade" id="modalFoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-0 text-center bg-dark">
                <img src="" id="imgModal" class="img-fluid">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
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


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script&gt;
<script>
    function verFoto(url) {
        document.getElementById('imgModal').src = url;
        new bootstrap.Modal(document.getElementById('modalFoto')).show();
    }
</script>
    <script>
        const coresPrioridade = { 'urgente': 'text-danger', 'alta': 'text-warning', 'media': 'text-primary', 'baixa': 'text-secondary' };
        const coresStatus = { 'aberto': 'bg-secondary', 'em_execucao': 'bg-warning', 'concluido': 'bg-success', 'fechado': 'bg-dark' };

        async function carregarChamados(status = '') {
            const res = await fetch(`api/gestor_chamados.php?status=${status}`);
            const chamados = await res.json();
            const body = document.getElementById('tabelaGeral');

            body.innerHTML = chamados.map(c => `
                <tr>
                    <td>#${c.id_chamado}</td>
                    <td>${c.solicitante_nome}</td>
                    <td>
                        <small class="text-muted">${c.bloco_nome}</small><br>
                        <strong>${c.ambiente_nome}</strong>
                    </td>
                    <td><i class="bi bi-circle-fill ${coresPrioridade[c.prioridade]} me-1"></i> ${c.prioridade.toUpperCase()}</td>
                    <td>${c.tecnico_nome || '<em class="text-muted">Não atribuído</em>'}</td>
                    <td><span class="badge ${coresStatus[c.status]}">${c.status.replace('_', ' ').toUpperCase()}</span></td>
                    <td>
                        <a href="gestor_detalhes.php?id=${c.id_chamado}" class="btn btn-sm btn-primary">
                            <i class="bi bi-eye"></i> Gerenciar
                        </a>
                    </td>
                </tr>
            `).join('');
        }

        carregarChamados();
    </script>
</body>
</html>