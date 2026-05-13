<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'SGM - Gestão de Serviços';
$activePage = 'servicos';
$pageHeading = 'Todos os Serviços';
$pageSubheading = 'Gerencie os tipos de serviços disponíveis no sistema.';
$pageActionLabel = 'Novo Serviço';
$pageActionLink = 'gestor_adicionar_servico.php';
require_once 'includes/gestor_layout.php';
?>

<div class="content-panel">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">ID</th>
                    <th>Nome do Serviço</th>
                    <th>Descrição</th>
                    <th class="text-end pe-4">Ações</th>
                </tr>
            </thead>
            <tbody id="tabelaServicos">
                <tr>
                    <td colspan="4" class="text-center py-4">
                        <div class="spinner-border text-primary spinner-border-sm me-2"></div>
                        Carregando serviços...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de Exclusão -->
<div class="modal fade" id="modalExcluir" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center p-5">
                <div class="mb-4 text-danger">
                    <i class="bi bi-exclamation-triangle" style="font-size: 4rem;"></i>
                </div>
                <h4 class="fw-bold mb-3">Excluir Serviço</h4>
                <p class="text-muted mb-4">Tem certeza que deseja remover este tipo de serviço? Esta ação é irreversível.</p>
                <input type="hidden" id="idParaExcluir">
                <div class="d-flex gap-3 justify-content-center">
                    <button type="button" class="btn btn-light px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-danger px-4 rounded-pill fw-bold" onclick="confirmarExclusao()">Sim, Excluir</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    async function carregarServicos() {
        try {
            const res = await fetch('api/servicos.php');
            const json = await res.json();
            const data = json.data || [];
            const tabela = document.getElementById('tabelaServicos');
            
            if (data.length === 0) {
                tabela.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted">Nenhum serviço cadastrado.</td></tr>';
                return;
            }

            tabela.innerHTML = data.map(s => `
                <tr>
                    <td class="ps-4 fw-bold">#${s.id_servico}</td>
                    <td><span class="fw-semibold text-primary">${s.nome}</span></td>
                    <td class="text-muted small" style="max-width: 300px;">${s.descricao || '<span class="fst-italic">Sem descrição</span>'}</td>
                    <td class="text-end pe-4">
                        <div class="btn-group">
                            <a href="gestor_editar_servico.php?id=${s.id_servico}" class="btn btn-sm btn-outline-primary rounded-pill me-2 px-3">
                                <i class="bi bi-pencil me-1"></i> Editar
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="setIDExclusao(${s.id_servico})">
                                <i class="bi bi-trash me-1"></i> Excluir
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        } catch (error) {
            console.error('Erro ao carregar:', error);
            document.getElementById('tabelaServicos').innerHTML = '<tr><td colspan="4" class="text-center py-4 text-danger">Erro ao conectar com a API.</td></tr>';
        }
    }

    function setIDExclusao(id) {
        document.getElementById('idParaExcluir').value = id;
        new bootstrap.Modal(document.getElementById('modalExcluir')).show();
    }

    async function confirmarExclusao() {
        const id = document.getElementById('idParaExcluir').value;
        try {
            const res = await fetch('api/servicos.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_servico: id })
            });
            const result = await res.json();
            if (result.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalExcluir')).hide();
                carregarServicos();
            } else {
                alert('Erro: ' + result.message);
            }
        } catch (error) {
            console.error(error);
            alert('Erro de conexão.');
        }
    }

    carregarServicos();
</script>

<?php require_once 'includes/gestor_footer.php'; ?>