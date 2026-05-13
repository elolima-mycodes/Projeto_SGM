<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'Gestor - Editar Ambiente';
$activePage = 'infraestrutura';
$pageHeading = 'Editar Ambiente';
$pageSubheading = 'Atualize o nome e o bloco associado à sala.';
$pageActionLabel = '';
$pageActionLink = '';
require_once 'includes/gestor_layout.php';
?>

<div class="content-panel col-lg-6 px-0">
    <form id="formEditarAmbiente" class="card-soft p-4">
        <h2 class="fw-bold text-center mb-4">Editar Ambiente</h2>
        <div class="mb-3">
            <label class="form-label fw-bold">Nome</label>
            <textarea id="nomeAmbiente" class="form-control" rows="1"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Bloco</label>
            <select id="selectBloco" class="form-select" required>
                <option value="">Selecione o bloco...</option>
            </select>
        </div>
        <div class="d-flex gap-2 justify-content-center">
            <a href="gestor_lista_ambientes.php" class="btn btn-outline-secondary py-2 px-4">Cancelar</a>
            <button type="submit" class="btn btn-primary py-2 px-4">Salvar</button>
        </div>
    </form>
</div>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    const idAmbiente = urlParams.get('id');

    async function iniciar() {
        const resB = await fetch('api/localizacoes.php?acao=listar_blocos');
        const blocos = await resB.json();
        const selB = document.getElementById('selectBloco');
        blocos.forEach(b => {
            selB.innerHTML += `<option value="${b.id_bloco}">${b.nome}</option>`;
        });
        if (idAmbiente) {
            try {
                const res = await fetch(`api/ambientes.php?id=${idAmbiente}`);
                const response = await res.json();
                console.log('Ambiente buscado:', response);
                const ambiente = response.data && response.data.length ? response.data[0] : null;
                if (ambiente) {
                    document.getElementById('nomeAmbiente').value = ambiente.nome;
                    document.getElementById('selectBloco').value = ambiente.id_bloco;
                }
            } catch (e) {
                console.error(e);
            }
        }
    }

    document.getElementById('formEditarAmbiente').addEventListener('submit', async (e) => {
        e.preventDefault();
        const editarAmbiente = {
            id_ambiente: idAmbiente,
            nome: document.getElementById('nomeAmbiente').value,
            id_bloco: document.getElementById('selectBloco').value
        };
        try {
            const res = await fetch('api/ambientes.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(editarAmbiente)
            });
            const result = await res.json();
            if (result.success === true || result.success === "true") {
                window.location.href = 'gestor_lista_ambientes.php';
            } else {
                alert('Erro: ' + result.message);
            }
        } catch (error) {
            console.error(error);
            alert('Não foi possível conectar à API.');
        }
    });

    iniciar();
</script>

<?php require_once 'includes/gestor_footer.php'; ?>