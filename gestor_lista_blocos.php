<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'SGM - Gestão de Blocos';
$activePage = 'blocos';
$pageHeading = 'Todos os Blocos';
$pageSubheading = 'Gerencie sua infraestrutura física e edite blocos cadastrados.';
$pageActionLabel = 'Novo Bloco';
$pageActionLink = 'gestor_adicionar_bloco.php';
require_once 'includes/gestor_layout.php';
?>

<div class="content-panel">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">ID</th>
                    <th>Nome do Bloco</th>
                    <th class="text-end pe-4">Ações</th>
                </tr>
            </thead>
            <tbody id="tabelaBlocos">
                <tr>
                    <td colspan="4" class="text-center py-4">
                        <div class="spinner-border text-primary spinner-border-sm me-2"></div>
                        Carregando blocos...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ambientes -->
<div class="modal fade" id="modalAmbientes" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold" id="tituloModalAmbientes">Ambientes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="listaAmbientesModal"></div>
        </div>
    </div>
</div>

<!-- Modal Exclusão -->
<div class="modal fade" id="modalExcluir" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center p-5">
                <div class="mb-4 text-danger">
                    <i class="bi bi-exclamation-triangle" style="font-size: 4rem;"></i>
                </div>
                <h4 class="fw-bold mb-3">Excluir Bloco</h4>
                <p class="text-muted mb-4">Deseja remover este bloco? Todos os ambientes associados também poderão ser afetados.</p>
                <input type="hidden" id="idParaExcluir">
                <div class="d-flex gap-3 justify-content-center">
                    <button type="button" class="btn btn-light px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-danger px-4 rounded-pill fw-bold" onclick="confirmarExclusao()">Confirmar Exclusão</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    async function carregarBlocos() {
        try {
            const res = await fetch('api/blocos.php');
            const json = await res.json();
            const data = json.data || [];
            const tabela = document.getElementById('tabelaBlocos');
            
            if (data.length === 0) {
                tabela.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-muted">Nenhum bloco encontrado.</td></tr>';
                return;
            }

            tabela.innerHTML = data.map(b => `
                <tr>
                    <td class="ps-4 fw-bold">#${b.id_bloco}</td>
                    <td><span><i class="bi bi-building me-2"></i>${b.nome}</span></td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-outline-info rounded-pill me-2 px-3" onclick="abrirModalAmbientes(${b.id_bloco}, '${b.nome}')">
                            <i class="bi bi-eye me-1"></i> Ver Ambientes
                        </button>
                        <a href="gestor_editar_bloco.php?id=${b.id_bloco}" class="btn btn-sm btn-outline-primary rounded-pill me-2 px-3">
                            <i class="bi bi-pencil me-1"></i> Editar
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="setIDExclusao(${b.id_bloco})">
                            <i class="bi bi-trash me-1"></i> Excluir
                        </button>
                    </td>
                </tr>
            `).join('');
        } catch (error) {
            console.error('Erro:', error);
            document.getElementById('tabelaBlocos').innerHTML = '<tr><td colspan="3" class="text-center py-4 text-danger">Erro ao conectar com o servidor.</td></tr>';
        }
    }

    carregarBlocos();

    async function abrirModalAmbientes(idBloco, nomeBloco) {
        const listaContainer = document.getElementById('listaAmbientesModal');
        const titulo = document.getElementById('tituloModalAmbientes');
        titulo.innerText = `Ambientes do ${nomeBloco}`;
        listaContainer.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>';
        
        new bootstrap.Modal(document.getElementById('modalAmbientes')).show();

        try {
            const res = await fetch(`api/blocos.php?id=${idBloco}`);
            const json = await res.json();
            const bloco = json.data && json.data[0];
            if (bloco && bloco.nomes_ambientes) {
                const ambientes = bloco.nomes_ambientes.split(', ');
                listaContainer.innerHTML = '<div class="list-group list-group-flush">' + ambientes.map(nome => `
                    <div class="list-group-item d-flex align-items-center border-0 py-2">
                        <div class="bg-light rounded-circle p-2 me-3">
                            <i class="bi bi-door-open text-primary"></i>
                        </div>
                        <span>${nome}</span>
                    </div>
                `).join('') + '</div>';
            } else {
                listaContainer.innerHTML = '<div class="text-center py-4 text-muted"><i class="bi bi-info-circle me-2"></i>Nenhum ambiente cadastrado para este bloco.</div>';
            }
        } catch (error) {
            console.error('Erro:', error);
            listaContainer.innerHTML = '<div class="alert alert-danger">Erro ao carregar ambientes.</div>';
        }
    }

    function setIDExclusao(id) {
        document.getElementById('idParaExcluir').value = id;
        new bootstrap.Modal(document.getElementById('modalExcluir')).show();
    }

    async function confirmarExclusao() {
        const id = document.getElementById('idParaExcluir').value;
        try {
            const res = await fetch('api/blocos.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_bloco: id })
            });
            const dados = await res.json();
            if (dados.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalExcluir')).hide();
                carregarBlocos();
            } else {
                alert('Erro ao excluir: ' + dados.message);
            }
        } catch (erro) {
            console.error('Erro:', erro);
            alert('Erro de conexão ao tentar excluir.');
        }
    }
</script>

<?php require_once 'includes/gestor_footer.php'; ?>