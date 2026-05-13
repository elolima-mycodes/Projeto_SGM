<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'SGM - Gestão de Usuários';
$activePage = 'usuarios';
$pageHeading = 'Todos os Usuários';
$pageSubheading = 'Gerencie os utilizadores do sistema.';
$pageActionLabel = 'Adicionar Usuário';
$pageActionLink = 'gestor_adicionar_usuario.php';
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
                    <th>Email</th>
                    <th>Perfil</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#1</td>
                    <td>João Silva</td>
                    <td>joao@exemplo.com</td>
                    <td><span class="badge bg-primary">Gestor</span></td>
                    <td class="text-nowrap">
                        <a href="gestor_editar_usuario.php" class="btn btn-sm btn-primary me-2"><i class="bi bi-pencil me-1"></i>Editar</a>
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalExcluir">
                            <i class="bi bi-trash me-1"></i>Excluir
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalExcluir" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white justify-content-center bg-primary">
                <h5 class="modal-title">Excluir Usuário</h5>
            </div>
            <div class="modal-body text-center py-5">
                <i class="bi bi-exclamation-circle text-danger display-3 mb-3"></i>
                <p class="fs-5 text-secondary">Deseja excluir esse usuário?</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pb-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger px-4">Excluir</button>
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

<?php require_once 'includes/gestor_footer.php'; ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Perfil</th>
                            <th>Ativo</th>
                            <th>Senha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#1</td>
                            <td>João Técnico</td>
                            <td>tecnico@sgm.com</td>
                            <td><span class="badge bg-warning">Técnico</span></td>
                            <td class="text-nowrap">
                                <a href="gestor_editar_usuario.php" class="btn btn-sm btn-warning me-2"><i class="bi bi-pencil me-2"></i>Editar</a>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalExcluir">
                                    <i class="bi bi-trash me-2"></i>Excluir
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>