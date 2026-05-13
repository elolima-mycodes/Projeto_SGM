<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'SGM - Gestão de Blocos';
$activePage = 'infraestrutura';
$pageHeading = 'Todos os Blocos';
$pageSubheading = 'Gerencie sua infraestrutura e edite blocos físicos.';
$pageActionLabel = 'Adicionar Bloco';
$pageActionLink = 'gestor_adicionar_bloco.php';
require_once 'includes/gestor_layout.php';
?>

<div class="content-panel">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div></div>
        <a href="gestor_dashboard.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Voltar
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="tabelaBlocos"></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalExcluir" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white justify-content-center bg-primary">
                <h5 class="modal-title">Excluir Bloco</h5>
            </div>
            <div class="modal-body text-center py-5">
                <i class="bi bi-exclamation-circle text-danger display-3 mb-3"></i>
                <p class="fs-5 text-secondary">Deseja excluir esse bloco?</p>
                <input type="hidden" id="idParaExcluir">
            </div>
            <div class="modal-footer justify-content-center border-0 pb-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger px-4" onclick="confirmarExclusao()">Excluir</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAmbientes" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white bg-primary">
                <h5 class="modal-title" id="tituloModalAmbientes">Ambientes</h5>
            </div>
            <div class="modal-body text-center py-4" id="listaAmbientesModal"></div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Fechar</button>
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

<script>
    async function carregarBlocos() {
        const res = await fetch('api/blocos.php');
        const json = await res.json();
        const data = json.data || [];
        const tabela = document.getElementById('tabelaBlocos');
        tabela.innerHTML = data.map(b => `
            <tr>
                <td>${b.id_bloco}</td>
                <td>${b.nome}</td>
                <td class="text-nowrap">
                    <button class="btn btn-sm btn-outline-primary me-2" onclick="abrirModalAmbientes(${b.id_bloco}, '${b.nome}')" data-bs-toggle="modal" data-bs-target="#modalAmbientes">
                        <i class="bi bi-building me-1"></i> Ver Ambientes
                    </button>
                    <a href="gestor_editar_bloco.php?id=${b.id_bloco}" class="btn btn-sm btn-primary me-2">
                        <i class="bi bi-pencil me-1"></i> Editar
                    </a>
                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalExcluir" onclick="setIDExclusao(${b.id_bloco})">
                        <i class="bi bi-trash me-1"></i>Excluir
                    </button>
                </td>
            </tr>
        `).join('');
    }

    carregarBlocos();

    async function abrirModalAmbientes(idBloco, nomeBloco) {
        const listaContainer = document.getElementById('listaAmbientesModal');
        const titulo = document.getElementById('tituloModalAmbientes');
        titulo.innerText = `Ambientes: ${nomeBloco}`;
        listaContainer.innerHTML = '<div class="spinner-border text-primary"></div>';
        try {
            const res = await fetch(`api/blocos.php?id=${idBloco}`);
            const json = await res.json();
            const bloco = json.data && json.data[0];
            if (bloco && bloco.nomes_ambientes) {
                const ambientes = bloco.nomes_ambientes.split(', ');
                listaContainer.innerHTML = '<ul class="list-group list-group-flush text-start">' + ambientes.map(nome => `
                    <li class="list-group-item d-flex align-items-center">
                        <i class="bi bi-door-closed me-2 text-primary"></i> ${nome}
                    </li>
                `).join('') + '</ul>';
            } else {
                listaContainer.innerHTML = '<div class="alert alert-light m-0">Nenhum ambiente cadastrado.</div>';
            }
        } catch (error) {
            console.error('Erro:', error);
            listaContainer.innerHTML = '<div class="text-danger">Erro ao carregar. Verifique o console.</div>';
        }
    }

    function setIDExclusao(id) {
        document.getElementById('idParaExcluir').value = id;
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
                const modalElement = document.getElementById('modalExcluir');
                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                modalInstance.hide();
                carregarBlocos();
            } else {
                alert('Erro ao excluir: ' + dados.message);
            }
        } catch (erro) {
            console.error('Erro na comunicação com a API:', erro);
            alert('Erro de conexão ao tentar excluir.');
        }
    }
</script>

<?php require_once 'includes/gestor_footer.php'; ?>