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
$pageActionLabel = 'Novo Chamado';
$pageActionLink = 'gestor_abrir_chamado.php';
require_once 'includes/gestor_layout.php';
?>

<div class="content-panel">
    <div class="mb-4 d-flex gap-2 flex-wrap justify-content-center">
        <button class="btn btn-sm btn-outline-secondary px-3 rounded-pill" onclick="carregarChamados('')">Todos</button>
        <button class="btn btn-sm btn-outline-primary px-3 rounded-pill" onclick="carregarChamados('aberto')">Abertos</button>
        <button class="btn btn-sm btn-outline-warning px-3 rounded-pill" onclick="carregarChamados('em_execucao')">Em Execução</button>
        <button class="btn btn-sm btn-outline-success px-3 rounded-pill" onclick="carregarChamados('concluido')">Concluídos</button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">ID</th>
                    <th>Solicitante</th>
                    <th>Local / Tipo</th>
                    <th>Prioridade</th>
                    <th>Técnico</th>
                    <th>Anexos</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Ações</th>
                </tr>
            </thead>
            <tbody id="tabelaGeral">
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <div class="spinner-border text-primary spinner-border-sm me-2"></div>
                        Carregando chamados...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para ver foto (opcional, se quiser manter) -->
<div class="modal fade" id="modalFoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body p-0 text-center bg-dark rounded overflow-hidden">
                <img src="" id="imgModal" class="img-fluid">
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
    function verFoto(url) {
        document.getElementById('imgModal').src = url;
        new bootstrap.Modal(document.getElementById('modalFoto')).show();
    }

    const coresPrioridade = { 
        'urgente': 'text-dark', 
        'alta': 'text-danger', 
        'media': 'text-warning', 
        'baixa': 'text-success' 
    };
    
    const coresStatus = { 
        'agendado': 'bg-info', 
        'aberto': 'bg-primary', 
        'em_execucao': 'bg-warning', 
        'concluido': 'bg-success', 
        'fechado': 'bg-dark' 
    };

    async function carregarChamados(status = '') {
        try {
            const res = await fetch(`api/gestor_chamados.php?status=${status}`);
            const chamados = await res.json();
            const body = document.getElementById('tabelaGeral');

            if (chamados.length === 0) {
                body.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">Nenhum chamado encontrado.</td></tr>';
                return;
            }

            body.innerHTML = chamados.map(c => `
                <tr>
                    <td class="ps-4 fw-bold">#${c.id_chamado}</td>
                    <td><span class="fw-semibold">${c.solicitante_nome}</span></td>
                    <td>
                        <div class="small">
                            <span class="text-muted">${c.bloco_nome}</span><br>
                            <strong>${c.ambiente_nome}</strong>
                        </div>
                    </td>
                    <td><span class="small fw-bold ${coresPrioridade[c.prioridade.toLowerCase()] || ''}"><i class="bi bi-circle-fill me-1" style="font-size: 0.6rem;"></i>${c.prioridade.toUpperCase()}</span></td>
                    <td><span class="text-muted small">${c.tecnico_nome || '<em>Não atribuído</em>'}</span></td>
                    <td class="text-center" id="anexos-${c.id_chamado}"><span class="text-muted">Carregando...</span></td>
                    <td><span class="badge ${coresStatus[c.status] || 'bg-light'} rounded-pill px-2">${c.status.replace('_', ' ').toUpperCase()}</span></td>
                    <td class="text-end pe-4">
                        <a href="gestor_detalhes.php?id=${c.id_chamado}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-eye me-1"></i> Gerenciar
                        </a>
                    </td>
                </tr>
            `).join('');
            chamados.forEach(c => carregarAnexosLista(c.id_chamado));
        } catch (error) {
            console.error('Erro ao carregar chamados:', error);
                document.getElementById('tabelaGeral').innerHTML = '<tr><td colspan="8" class="text-center py-4 text-danger">Erro ao carregar chamados.</td></tr>';
        }
    }

    async function carregarAnexosLista(idChamado) {
        try {
            const res = await fetch(`api/anexos.php?id_chamado=${idChamado}`);
            const anexos = await res.json();
            const celula = document.getElementById(`anexos-${idChamado}`);
            if (!celula) return;
            if (anexos && anexos.length > 0) {
                celula.innerHTML = anexos.map(arq => `<img src="${arq.caminho_arquivo}" class="thumb-img" style="width:40px;height:40px;" onclick="verFoto('${arq.caminho_arquivo}')" title="Visualizar anexo">`).join('');
            } else {
                celula.innerHTML = '<span class="text-muted">-</span>';
            }
        } catch (error) {
            console.error('Erro ao carregar anexos da lista:', error);
        }
    }

    carregarChamados();
</script>

<?php require_once 'includes/gestor_footer.php'; ?>