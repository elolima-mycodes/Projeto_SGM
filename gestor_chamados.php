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
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="gestor_dashboard.php">SGM Admin</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link active" href="gestor_chamados.php">Chamados</a>
                <a class="nav-link" href="gestor_locais.php">Locais</a>
                <a class="nav-link" href="api/logout.php">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container">
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