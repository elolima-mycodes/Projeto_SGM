<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'Gestor - Editar Serviço';
$activePage = 'servicos';
$pageHeading = 'Editar Serviço';
$pageSubheading = 'Atualize as informações do tipo de serviço.';

require_once 'includes/gestor_layout.php';
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="content-panel col-lg-6 px-0">
            <form id="formEditarServico" class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Nome do Serviço</label>
                    <input type="text" id="nomeServico" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary">Descrição</label>
                    <textarea id="descricaoServico" class="form-control" rows="4"></textarea>
                </div>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="gestor_lista_tipos_de_servico.php" class="btn btn-light py-2 px-4 fw-bold">Cancelar</a>
                    <button type="submit" class="btn btn-primary py-2 px-4 fw-bold">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    const idServico = urlParams.get('id');

    async function carregarDados() {
        if (!idServico) return;
        try {
            const res = await fetch(`api/servicos.php?id=${idServico}`);
            const data = await res.json();
            if (data.success && data.data) {
                document.getElementById('nomeServico').value = data.data.nome;
                document.getElementById('descricaoServico').value = data.data.descricao;
            }
        } catch (error) {
            console.error('Erro ao carregar serviço:', error);
        }
    }

    document.getElementById('formEditarServico').addEventListener('submit', async (e) => {
        e.preventDefault();
        const servico = {
            id_servico: idServico,
            nome: document.getElementById('nomeServico').value,
            descricao: document.getElementById('descricaoServico').value
        };
        try {
            const res = await fetch('api/servicos.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(servico)
            });
            const result = await res.json();
            if (result.success) {
                alert('Serviço atualizado com sucesso!');
                window.location.href = 'gestor_lista_tipos_de_servico.php';
            } else {
                alert('Erro: ' + result.message);
            }
        } catch (error) {
            console.error(error);
            alert('Erro na comunicação com o servidor.');
        }
    });

    carregarDados();
</script>

<?php require_once 'includes/gestor_footer.php'; ?>