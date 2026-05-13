<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'Gestor - Editar Usuário';
$activePage = 'usuarios';
$pageHeading = 'Editar Usuário';
$pageSubheading = 'Atualize os dados cadastrais do utilizador.';

require_once 'includes/gestor_layout.php';
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="content-panel col-lg-8 px-0">
            <form id="formEditarUsuario" class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary">Nome Completo</label>
                        <input type="text" id="nome" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary">E-mail</label>
                        <input type="email" id="email" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-secondary">Perfil de Acesso</label>
                        <select id="perfil" class="form-select" required>
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
                    <label class="form-label fw-bold text-secondary">Nova Senha (deixe em branco para manter)</label>
                    <input type="password" id="senha" class="form-control" placeholder="••••••••">
                </div>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="gestor_lista_usuarios.php" class="btn btn-light py-2 px-4 fw-bold">Cancelar</a>
                    <button type="submit" class="btn btn-primary py-2 px-4 fw-bold">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    const idUsuario = urlParams.get('id');

    async function carregarDados() {
        if (!idUsuario) return;
        try {
            const res = await fetch(`api/usuarios.php?id=${idUsuario}`);
            const data = await res.json();
            if (data.success && data.data) {
                const u = data.data;
                document.getElementById('nome').value = u.nome;
                document.getElementById('email').value = u.email;
                document.getElementById('perfil').value = u.perfil;
                document.getElementById('status').value = u.ativo;
            }
        } catch (error) {
            console.error('Erro ao carregar usuário:', error);
        }
    }

    document.getElementById('formEditarUsuario').addEventListener('submit', async (e) => {
        e.preventDefault();
        const usuario = {
            id_usuario: idUsuario,
            nome: document.getElementById('nome').value,
            email: document.getElementById('email').value,
            perfil: document.getElementById('perfil').value,
            ativo: document.getElementById('status').value,
            senha: document.getElementById('senha').value || null
        };
        try {
            const res = await fetch('api/usuarios.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(usuario)
            });
            const result = await res.json();
            if (result.success) {
                alert('Usuário atualizado com sucesso!');
                window.location.href = 'gestor_lista_usuarios.php';
            } else {
                alert('Erro: ' + result.message);
            }
        } catch (error) {
            console.error(error);
            alert('Erro ao conectar com o servidor.');
        }
    });

    carregarDados();
</script>

<?php require_once 'includes/gestor_footer.php'; ?>