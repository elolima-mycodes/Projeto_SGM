<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'Gestor - Editar Bloco';
$activePage = 'blocos';
$pageHeading = 'Editar Bloco';
$pageSubheading = 'Altere o nome e a descrição do bloco físico.';

require_once 'includes/gestor_layout.php';
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="content-panel col-lg-6 px-0">
            <form id="formEditarBloco" class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Nome do Bloco</label>
                    <input type="text" id="nomeBloco" class="form-control" placeholder="Ex: Pavilhão Norte" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Descrição do Bloco</label>
                    <textarea id="descricaoBloco" class="form-control" rows="3" placeholder="Descreva o propósito ou características deste bloco..."></textarea>
                </div>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="gestor_lista_blocos.php" class="btn btn-light py-2 px-4 fw-bold">Cancelar</a>
                    <button type="submit" class="btn btn-primary py-2 px-4 fw-bold">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    const idBloco = urlParams.get('id');

    async function carregarDados() {
        if (!idBloco) return;
        try {
            const res = await fetch(`api/blocos.php?id=${idBloco}`);
            const data = await res.json();
            // api/blocos.php agora retorna nome, descricao e nomes_ambientes.
            if (data.success && data.data && data.data[0]) {
                const b = data.data[0];
                document.getElementById('nomeBloco').value = b.nome;
                document.getElementById('descricaoBloco').value = b.descricao || '';
            }
        } catch (error) {
            console.error('Erro ao carregar bloco:', error);
        }
    }

    document.getElementById('formEditarBloco').addEventListener('submit', async (e) => {
        e.preventDefault();
        const bloco = {
            id_bloco: idBloco,
            nome: document.getElementById('nomeBloco').value,
            descricao: document.getElementById('descricaoBloco').value
        };
        try {
            const res = await fetch('api/blocos.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(bloco)
            });
            const result = await res.json();
            if (result.success) {
                alert('Bloco atualizado com sucesso!');
                window.location.href = 'gestor_lista_blocos.php';
            } else {
                alert('Erro: ' + result.message);
            }
        } catch (error) {
            console.error(error);
            alert('Erro de conexão.');
        }
    });

    carregarDados();
</script>

<?php require_once 'includes/gestor_footer.php'; ?>