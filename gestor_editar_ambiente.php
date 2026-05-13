<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'Gestor - Editar Ambiente';
$activePage = 'ambientes';
$pageHeading = 'Editar Ambiente';
$pageSubheading = 'Atualize o nome e o bloco associado à sala.';
$pageActionLabel = '';
$pageActionLink = '';
require_once 'includes/gestor_layout.php';
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="content-panel col-lg-6 px-0">
            <form id="formEditarAmbiente" class="card-soft p-4">
                <h2 class="fw-bold text-center mb-4">Atualizar Ambiente</h2>
                <div class="mb-3">
                    <label class="form-label fw-bold">Nome do Ambiente</label>
                    <input type="text" id="nomeAmbiente" class="form-control" placeholder="Ex: Sala 101" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Bloco Associado</label>
                    <select id="selectBloco" class="form-select" required>
                        <option value="">Selecione o bloco...</option>
                    </select>
                </div>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="gestor_lista_ambientes.php" class="btn btn-light py-2 px-4 fw-bold">Cancelar</a>
                    <button type="submit" class="btn btn-primary py-2 px-4 fw-bold">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    const idAmbiente = urlParams.get('id');

    async function iniciar() {
        try {
            // 1. Listar blocos
            const resB = await fetch('api/localizacoes.php?acao=listar_blocos');
            const blocos = await resB.json();
            const selB = document.getElementById('selectBloco');
            blocos.forEach(b => {
                const opt = document.createElement('option');
                opt.value = b.id_bloco;
                opt.textContent = b.nome;
                selB.appendChild(opt);
            });

            // 2. Carregar dados do ambiente
            if (idAmbiente) {
                const res = await fetch(`api/ambientes.php?id=${idAmbiente}`);
                const response = await res.json();
                const ambiente = response.data && response.data.length ? response.data[0] : null;
                if (ambiente) {
                    document.getElementById('nomeAmbiente').value = ambiente.nome;
                    document.getElementById('selectBloco').value = ambiente.id_bloco;
                }
            }
        } catch (error) {
            console.error('Erro ao iniciar:', error);
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
                alert('Ambiente atualizado com sucesso!');
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