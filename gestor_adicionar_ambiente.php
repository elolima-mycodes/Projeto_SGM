<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor - Adicionar Ambiente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow border-0 rounded-4">
                    <div class="card-body p-4">
                        <h2 class="fw-bold text-center mb-4">Adicionar Ambiente</h2>
                        
                        <form id="formAdicionarAmbiente">

                            <div class="mb-3">
                                <label class="form-label fw-bold">Nome</label>
                                <textarea id="nomeAmbiente" class="form-control" rows="1"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Bloco</label>
                                <select id="selectBloco" class="form-select" required>
                                    <option value="">Selecione o bloco...</option>
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
                                $pageActionLabel = '';
                                $pageActionLink = '';
                                require_once 'includes/gestor_layout.php';
                                ?>

                                <div class="content-panel col-lg-6 px-0">
                                    <form id="formAdicionarAmbiente" class="card-soft p-4">
                                        <h2 class="fw-bold text-center mb-4">Adicionar Ambiente</h2>
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
                                    async function iniciar() {
                                        const resB = await fetch('api/localizacoes.php?acao=listar_blocos');
                                        const blocos = await resB.json();
                                        const selB = document.getElementById('selectBloco');
                                        blocos.forEach(b => {
                                            selB.innerHTML += `<option value="${b.id_bloco}">${b.nome}</option>`;
                                        });
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
                                            if (result.success === true || result.success === "true") {
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