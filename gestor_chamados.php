<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'SGM - Gestão de Chamados';
$activePage = 'chamados';
$pageHeading = 'Todos os Chamados';
$pageSubheading = 'Gerencie e atribua as solicitações de serviço aos técnicos.';
$pageActionLabel = '';
$pageActionLink = '';
require_once 'includes/gestor_layout.php';
?>

<div class="content-panel">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div></div>
        <a href="gestor_dashboard.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Voltar
        </a>
    </div>

    <div class="mb-3 d-flex gap-2 flex-wrap">
        <button class="btn btn-sm btn-outline-secondary" onclick="carregarChamados('')">Todos</button>
        <button class="btn btn-sm btn-outline-primary" onclick="carregarChamados('aberto')">Abertos</button>
        <button class="btn btn-sm btn-outline-warning" onclick="carregarChamados('em_execucao')">Em Execução</button>
        <button class="btn btn-sm btn-outline-success" onclick="carregarChamados('concluido')">Concluídos</button>
    </div>

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
            <tbody id="tabelaGeral"></tbody>
        </table>
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

<div class="modal fade" id="modalLogout" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white justify-content-center bg-primary">
                <h5 class="modal-title">Encerrar Sessão</h5>
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

<script>
    function verFoto(url) {
        document.getElementById('imgModal').src = url;
        new bootstrap.Modal(document.getElementById('modalFoto')).show();
    }

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

<?php require_once 'includes/gestor_footer.php'; ?>