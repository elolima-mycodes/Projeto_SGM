<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'SGM - Gestão de Usuários';
$activePage = 'usuarios';
$pageHeading = 'Todos os Usuários';
$pageSubheading = 'Gerencie os utilizadores e seus níveis de acesso.';
$pageActionLabel = 'Novo Usuário';
$pageActionLink = 'gestor_adicionar_usuario.php';
require_once 'includes/gestor_layout.php';
?>

<div class="content-panel">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Perfil</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Ações</th>
                </tr>
            </thead>
            <tbody id="tabelaUsuarios">
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <div class="spinner-border text-primary spinner-border-sm me-2"></div>
                        Carregando usuários...
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
                <h4 class="fw-bold mb-3">Excluir Usuário</h4>
                <p class="text-muted mb-4">Tem certeza que deseja excluir este usuário? Ele perderá o acesso ao sistema imediatamente.</p>
                <input type="hidden" id="idParaExcluir">
                <div class="d-flex gap-3 justify-content-center">
                    <button type="button" class="btn btn-light px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-danger px-4 rounded-pill fw-bold" onclick="confirmarExclusao()">Sim, Remover</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    async function carregarUsuarios() {
        try {
            const res = await fetch('api/usuarios.php');
            const data = await res.json();
            const tabela = document.getElementById('tabelaUsuarios');
            
            if (data.length === 0) {
                tabela.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">Nenhum usuário cadastrado.</td></tr>';
                return;
            }

            const perfilBadges = {
                'gestor': 'bg-primary',
                'tecnico': 'bg-info',
                'solicitante': 'bg-secondary'
            };

            tabela.innerHTML = data.map(u => `
                <tr>
                    <td class="ps-4 fw-bold">#${u.id_usuario}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                <i class="bi bi-person text-primary"></i>
                            </div>
                            <span class="fw-semibold">${u.nome}</span>
                        </div>
                    </td>
                    <td><span class="text-muted">${u.email}</span></td>
                    <td><span class="badge ${perfilBadges[u.perfil] || 'bg-dark'} rounded-pill px-3">${u.perfil.toUpperCase()}</span></td>
                    <td>
                        <span class="badge ${u.ativo == 1 ? 'bg-success' : 'bg-danger'} p-1 rounded-circle me-1" style="width: 8px; height: 8px; display: inline-block;"></span>
                        <span class="small">${u.ativo == 1 ? 'Ativo' : 'Inativo'}</span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="btn-group">
                            <a href="gestor_editar_usuario.php?id=${u.id_usuario}" class="btn btn-sm btn-outline-primary rounded-pill me-2 px-3">
                                <i class="bi bi-pencil me-1"></i> Editar
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="setIDExclusao(${u.id_usuario})">
                                <i class="bi bi-trash me-1"></i> Excluir
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        } catch (error) {
            console.error('Erro ao carregar:', error);
            document.getElementById('tabelaUsuarios').innerHTML = '<tr><td colspan="6" class="text-center py-4 text-danger">Erro ao carregar usuários.</td></tr>';
        }
    }

    function setIDExclusao(id) {
        document.getElementById('idParaExcluir').value = id;
        new bootstrap.Modal(document.getElementById('modalExcluir')).show();
    }

    async function confirmarExclusao() {
        const id = document.getElementById('idParaExcluir').value;
        try {
            const res = await fetch('api/usuarios.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_usuario: id })
            });
            const result = await res.json();
            if (result.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalExcluir')).hide();
                carregarUsuarios();
            } else {
                alert('Erro: ' + result.message);
            }
        } catch (error) {
            console.error(error);
            alert('Erro de conexão.');
        }
    }

    carregarUsuarios();
</script>

<?php require_once 'includes/gestor_footer.php'; ?>