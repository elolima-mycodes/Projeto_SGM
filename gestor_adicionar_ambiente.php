<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'Gestor - Adicionar Ambiente';
$activePage = 'infraestrutura';
$pageHeading = 'Adicionar Ambiente';
$pageSubheading = 'Registre um novo ambiente ligado a um bloco existente.';

require_once 'includes/gestor_layout.php'; 
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="content-panel col-lg-6 px-0">
            <form id="formAdicionarAmbiente" class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Nome do Ambiente</label>
                    <input type="text" id="nomeAmbiente" class="form-control" placeholder="Ex: Sala 101, Laboratório..." required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary">Bloco Correspondente</label>
                    <select id="selectBloco" class="form-select" required>
                        <option value="">Selecione o bloco...</option>
                    </select>
                </div>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="gestor_lista_ambientes.php" class="btn btn-light py-2 px-4 fw-bold">Cancelar</a>
                    <button type="submit" class="btn btn-primary py-2 px-4 fw-bold">Salvar Ambiente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    async function iniciar() {
        try { // ADICIONADO O TRY QUE FALTAVA
            const resB = await fetch('api/localizacoes.php?acao=listar_blocos');
            const blocos = await resB.json();
            const selB = document.getElementById('selectBloco');
            
            // Limpa e preenche
            selB.innerHTML = '<option value="">Selecione o bloco...</option>';
            blocos.forEach(b => {
                selB.innerHTML += `<option value="${b.id_bloco}">${b.nome}</option>`;
            });
        } catch (error) {
            console.error('Erro ao carregar blocos:', error);
        }
    }

    document.getElementById('formAdicionarAmbiente').addEventListener('submit', async (e) => {
        e.preventDefault();
        const novoAmbiente = {
            nome: document.getElementById('nomeAmbiente').value,
            id_bloco: document.getElementById('selectBloco').value
        };

        try {
            const response = await fetch('api/ambientes.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(novoAmbiente)
            });
            const result = await response.json();
            if (result.success == true) {
                alert('Ambiente adicionado com sucesso!');
                window.location.href = 'gestor_lista_ambientes.php';
            } else {
                alert('Erro: ' + result.message);
            }
        } catch (error) {
            console.error('Erro na comunicação:', error);
            alert('Não foi possível conectar à API.');
        }
    });

    iniciar();
</script>

<?php require_once 'includes/gestor_footer.php'; ?>