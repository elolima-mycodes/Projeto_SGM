<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'Gestor - Adicionar Bloco';
$activePage = 'infraestrutura';
$pageHeading = 'Adicionar Bloco';
$pageSubheading = 'Registre um novo bloco físico para o sistema.';
$pageActionLabel = '';
$pageActionLink = '';
require_once 'includes/gestor_layout.php';
?>

<div class="content-panel col-lg-6 px-0">
    <form id="formAdicionarBloco" class="card-soft p-4">
        <h2 class="fw-bold text-center mb-4">Adicionar Bloco</h2>
        <div class="mb-3">
            <label class="form-label fw-bold">Nome do Bloco</label>
            <input type="text" id="nome" class="form-control" placeholder="Ex: Pavilhão Central" required>
        </div>
        <div class="mb-4">
            <label class="form-label fw-bold">Descrição (Opcional)</label>
            <textarea id="descricao" class="form-control" rows="4" placeholder="Detalhes sobre a localização ou uso..."></textarea>
        </div>
        <div class="d-flex gap-2 justify-content-center">
            <a href="gestor_lista_blocos.php" class="btn btn-outline-secondary py-2 px-4">Cancelar</a>
            <button type="submit" class="btn btn-primary py-2 px-4">Adicionar</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('formAdicionarBloco').addEventListener('submit', async (e) => {
        e.preventDefault();
        const novoBloco = {
            nome: document.getElementById('nome').value,
            descricao: document.getElementById('descricao').value,
        };
        try {
            const response = await fetch('api/blocos.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(novoBloco)
            });
            const result = await response.json();
            if (result.success) {
                alert('Bloco adicionado com sucesso!');
                window.location.href = 'gestor_lista_blocos.php';
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