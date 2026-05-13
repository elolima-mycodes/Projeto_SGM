<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'Gestor - Adicionar Usuário';
$activePage = 'usuarios';
$pageHeading = 'Adicionar Usuário';
$pageSubheading = 'Registre um novo utilizador no sistema.';
$pageActionLabel = '';
$pageActionLink = '';
require_once 'includes/gestor_layout.php';
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="content-panel col-lg-8 px-0">
            <form id="formAdicionarUsuario" class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary">Nome Completo</label>
                        <input type="text" id="nome" class="form-control" placeholder="Nome do usuário" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary">E-mail</label>
                        <input type="email" id="email" class="form-control" placeholder="email@exemplo.com" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary">Perfil de Acesso</label>
                        <select id="perfil" class="form-select" required>
                            <option value="">Selecione o perfil...</option>
                            <option value="gestor">Gestor</option>
                            <option value="tecnico">Técnico</option>
                            <option value="solicitante">Solicitante</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary">Status</label>
                        <select id="status" class="form-select" required>
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary">Senha Temporária</label>
                    <input type="password" id="senha" class="form-control" placeholder="••••••••" required>
                </div>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="gestor_lista_usuarios.php" class="btn btn-light py-2 px-4 fw-bold">Cancelar</a>
                    <button type="submit" class="btn btn-primary py-2 px-4 fw-bold">Adicionar Usuário</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('formAdicionarUsuario').addEventListener('submit', async (e) => {
        e.preventDefault();
        const novoUsuario = {
            nome: document.getElementById('nome').value,
            email: document.getElementById('email').value,
            perfil: document.getElementById('perfil').value,
            ativo: document.getElementById('status').value,
            senha: document.getElementById('senha').value
        };
        try {
            const response = await fetch('api/usuarios.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(novoUsuario)
            });
            const result = await response.json();
            if (result.success) {
                alert('Usuário criado com sucesso!');
                window.location.href = 'gestor_lista_usuarios.php';
            } else {
                alert('Erro: ' + result.message);
            }
        } catch (error) {
            console.error('Erro na requisição:', error);
            alert('Erro ao conectar com o servidor.');
        }
    });
</script>

<?php require_once 'includes/gestor_footer.php'; ?>