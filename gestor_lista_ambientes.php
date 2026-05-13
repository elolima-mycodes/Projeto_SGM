<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}
?>
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'SGM - Gestão de Ambientes';
$activePage = 'infraestrutura';
$pageHeading = 'Todos os Ambientes';
$pageSubheading = 'Gerencie ambientes por bloco e edite seus dados.';
$pageActionLabel = 'Adicionar Ambiente';
$pageActionLink = 'gestor_adicionar_ambiente.php';
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
                    <th>Bloco</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="tabelaAmbientes"></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalExcluir" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white justify-content-center bg-primary">
                <h5 class="modal-title">Excluir Ambiente</h5>
            </div>
            <div class="modal-body text-center py-5">
                <i class="bi bi-exclamation-circle text-danger display-3 mb-3"></i>
                <p class="fs-5 text-secondary">Deseja excluir esse ambiente?</p>
                <input type="hidden" id="idParaExcluir">
            </div>
            <div class="modal-footer justify-content-center border-0 pb-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger px-4" onclick="confirmarExclusao()">Excluir</button>
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
    async function carregarAmbientes() {
        const res = await fetch('api/ambientes.php');
        const json = await res.json();
        const data = json.data || [];
        const tabela = document.getElementById('tabelaAmbientes');
        tabela.innerHTML = data.map(a => `
            <tr>
                <td>${a.id_ambiente}</td>
                <td>${a.nome}</td>
                <td>${a.nome_bloco}</td>
                <td class="text-nowrap">
                    <a href="gestor_editar_ambiente.php?id=${a.id_ambiente}" class="btn btn-sm btn-primary me-2">
                        <i class="bi bi-pencil me-1"></i> Editar
                    </a>
                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalExcluir" onclick="setIDExclusao(${a.id_ambiente})">
                        <i class="bi bi-trash me-1"></i>Excluir
                    </button>
                </td>
            </tr>
        `).join('');
    }

    carregarAmbientes();

    function setIDExclusao(id) {
        document.getElementById('idParaExcluir').value = id;
    }

    async function confirmarExclusao() {
        const id = document.getElementById('idParaExcluir').value;
        try {
            const res = await fetch('api/ambientes.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_ambiente: id })
            });
            const dados = await res.json();
            if (dados.success) {
                const modalElement = document.getElementById('modalExcluir');
                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                modalInstance.hide();
                carregarAmbientes();
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