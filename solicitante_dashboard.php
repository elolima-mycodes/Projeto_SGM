<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] != 'solicitante') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'SGM - Minhas Solicitações';
$activePage = 'dashboard';
$pageHeading = 'Minhas Solicitações';
$pageSubheading = 'Acompanhe o status dos seus chamados abertos.';

require_once 'includes/user_layout.php';
?>

<div class="row mb-4">
    <div class="col-12 text-end">
        <a href="solicitante_abrir_chamado.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-plus-lg me-2"></i> Abrir Novo Chamado
        </a>
    </div>
</div>

<div class="content-panel">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 80px;">ID</th>
                    <th style="width: 100px;">Foto</th>
                    <th>Local</th>
                    <th>Descrição</th>
                    <th style="width: 130px;">Data</th>
                    <th style="width: 150px;">Status</th>
                </tr>
            </thead>
            <tbody id="tabelaChamados">
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        Carregando solicitações...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Foto -->
<div class="modal fade" id="modalFoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body p-0">
                <img id="imgModal" src="" class="img-fluid rounded-top" style="width: 100%;">
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
    function verFoto(url) {
        document.getElementById('imgModal').src = url;
        new bootstrap.Modal(document.getElementById('modalFoto')).show();
    }

    async function carregarChamados() {
        try {
            const response = await fetch('api/chamados.php');
            const chamados = await response.json();
            const lista = document.getElementById('tabelaChamados');
            
            const cores = { 
                'aberto': 'bg-secondary bg-opacity-10 text-secondary', 
                'agendado': 'bg-info bg-opacity-10 text-info', 
                'em_execucao': 'bg-warning bg-opacity-10 text-warning', 
                'concluido': 'bg-success bg-opacity-10 text-success', 
                'fechado': 'bg-dark bg-opacity-10 text-dark' 
            };

            if (!chamados || chamados.length === 0) {
                lista.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted">Nenhum chamado encontrado.</td></tr>';
                return;
            }

            const rows = await Promise.all(chamados.map(async c => {
                const resAnexos = await fetch(`api/anexos.php?id_chamado=${c.id_chamado}`);
                const anexos = await resAnexos.json();
                
                const thumbHtml = (anexos && anexos.length > 0) ?
                    `<img src="${anexos[0].caminho_arquivo}" class="mini-thumb" onclick="verFoto('${anexos[0].caminho_arquivo}')">` :
                    '<div class="rounded bg-light d-flex align-items-center justify-content-center" style="width:45px; height:45px;"><i class="bi bi-image text-muted opacity-50"></i></div>';

                return `<tr>
                    <td class="fw-bold text-primary">#${c.id_chamado}</td>
                    <td>${thumbHtml}</td>
                    <td>
                        <div class="fw-bold">${c.bloco_nome}</div>
                        <div class="small text-muted">${c.ambiente_nome}</div>
                    </td>
                    <td>
                        <div class="text-truncate" style="max-width: 300px;" title="${c.descricao_problema}">
                            ${c.descricao_problema}
                        </div>
                    </td>
                    <td><span class="small text-muted">${new Date(c.data_abertura).toLocaleDateString('pt-BR')}</span></td>
                    <td><span class="badge rounded-pill ${cores[c.status] || 'bg-secondary'}">${c.status.toUpperCase().replace('_', ' ')}</span></td>
                </tr>`;
            }));

            lista.innerHTML = rows.join('');
        } catch (err) {
            console.error("Erro ao carregar:", err);
            document.getElementById('tabelaChamados').innerHTML = '<tr><td colspan="6" class="text-center py-5 text-danger">Erro ao carregar dados.</td></tr>';
        }
    }
    
    carregarChamados();
</script>

<?php require_once 'includes/user_footer.php'; ?>