<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'Gestor - Adicionar Bloco';
$activePage = 'blocos';
$pageHeading = 'Adicionar Bloco';
$pageSubheading = 'Registre um novo bloco físico para o sistema.';
$pageActionLabel = '';
$pageActionLink = '';
require_once 'includes/gestor_layout.php';
?>

<a href="gestor_lista_blocos.php" class="btn btn-outline-primary btn-sm mb-3">
    <i class="bi bi-arrow-left me-2"></i> Voltar
</a>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="content-panel col-lg-6 px-0">
            <form id="formAdicionarBloco" class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Nome do Bloco</label>
                    <input type="text" id="nome" class="form-control" placeholder="Ex: Pavilhão Central, Bloco A..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Descrição do Bloco</label>
                    <textarea id="descricao" class="form-control" rows="3" placeholder="Descreva o propósito ou características deste bloco..."></textarea>
                </div>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="gestor_lista_blocos.php" class="btn btn-light py-2 px-4 fw-bold">Cancelar</a>
                    <button type="submit" class="btn btn-primary py-2 px-4 fw-bold">Adicionar Bloco</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('formAdicionarBloco').addEventListener('submit', async (e) => {
        e.preventDefault();
        const novoBloco = {
            nome: document.getElementById('nome').value,
            descricao: document.getElementById('descricao').value
        };
        // Agora a API de blocos aceita nome e descrição.
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