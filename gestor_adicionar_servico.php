<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'Gestor - Adicionar Serviço';
$activePage = 'infraestrutura';
$pageHeading = 'Adicionar Serviço';
$pageSubheading = 'Crie um novo tipo de serviço para o sistema.';

require_once 'includes/gestor_layout.php';
?>

<a href="gestor_lista_tipos_de_servico.php" class="btn btn-outline-primary btn-sm mb-3">
    <i class="bi bi-arrow-left me-2"></i> Voltar
</a>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="content-panel col-lg-6 px-0">
            <form id="formAdicionarServico" class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Nome do Serviço</label>
                    <input type="text" id="nomeServico" class="form-control" placeholder="Ex: Manutenção Elétrica" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Descrição do Serviço</label>
                    <textarea id="descricaoServico" class="form-control" rows="3" placeholder="Explique o que este tipo de serviço cobre..."></textarea>
                </div>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="gestor_lista_tipos_de_servico.php" class="btn btn-light py-2 px-4 fw-bold">Cancelar</a>
                    <button type="submit" class="btn btn-primary py-2 px-4 fw-bold">Adicionar Serviço</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('formAdicionarServico').addEventListener('submit', async (e) => {
        e.preventDefault();
        const novoServico = {
            nome: document.getElementById('nomeServico').value,
            descricao: document.getElementById('descricaoServico').value
        };
        // Agora a API de tipos de serviço aceita nome e descrição.
        try {
            const response = await fetch('api/tipos_de_servico.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(novoServico)
            });
            const result = await response.json();
            if (result.success) {
                alert('Serviço adicionado com sucesso!');
                window.location.href = 'gestor_lista_tipos_de_servico.php';
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