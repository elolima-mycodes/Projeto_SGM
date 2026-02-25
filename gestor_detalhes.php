<?php
session_start();
// Verifica se há um ID na URL, se não, volta para a lista
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: gestor_chamados.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGM - Detalhes do Chamado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        .thumb-img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            cursor: pointer;
            border-radius: 8px;
            transition: transform 0.2s;
        }
        .thumb-img:hover { transform: scale(1.05); }
        .card-shadow { box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); border-radius: 10px; border: none; }
    </style>
</head>
<body class="bg-light">
    <main class="py-4">
        <div class="container-xxl">
            <a href="gestor_chamados.php" class="btn btn-outline-secondary mb-4">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card card-shadow mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold text-primary">Dados da Solicitação #<?= $id ?></h5>
                        </div>
                        <div id="detalhesChamado" class="card-body">
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="mt-2">Carregando informações...</p>
                            </div>
                        </div>
                    </div>
                    
                    <div id="areaFechamento"></div>
                </div>

                <div class="col-lg-4">
                    <section class="card card-shadow p-4 bg-white">
                        <h6 class="fw-bold mb-3">Atribuir Técnico</h6>
                        <form id="formAtribuir">
                            <input type="hidden" id="id_chamado" value="<?= $id ?>">
                            <div class="mb-3">
                                <label class="form-label small">Técnico Responsável</label>
                                <select id="selectTecnico" class="form-select" required>


                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small">Prioridade</label>
                                <select id="prioridade" class="form-select">
                                    <option value="baixa">Baixa</option>
                                    <option value="media">Média</option>
                                    <option value="alta">Alta</option>
                                    <option value="urgente">Urgente</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small">Data Prevista</label>
                                <input type="date" id="data_prevista" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Confirmar Atribuição</button>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="modalFoto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body p-0 text-center">
                    <img id="imgModal" src="" class="img-fluid rounded shadow-lg">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const idChamado = <?= $id ?>;

        function verFoto(url) {
            document.getElementById('imgModal').src = url;
            new bootstrap.Modal(document.getElementById('modalFoto')).show();
        }

        async function carregarDados() {
            try {
                // 1. Carrega Técnicos para o Select
                const resTec = await fetch('api/usuarios.php');
                const tecnicos = await resTec.json();
                const select = document.getElementById('selectTecnico');
                select.innerHTML = '<option value="">Selecione um técnico...</option>';
                tecnicos.forEach(t => { select.innerHTML += `<option value="${t.id_usuario}">${t.nome}</option>`;
                });

                // 2. Carrega Dados do Chamado
                const c = await (await fetch(`api/chamados.php?id=${idChamado}`)).json();
                
                document.getElementById('detalhesChamado').innerHTML = `
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <label class="text-muted small d-block">Status</label>
                            <span class="badge bg-secondary px-3 py-2">${c.status.toUpperCase()}</span>
                        </div>
                        <div class="col-sm-6 mb-3 text-sm-end">
                            <label class="text-muted small d-block">Data de Abertura</label>
                            <span>${new Date(c.data_abertura).toLocaleString('pt-BR')}</span>
                        </div>
                        <hr>
                        <div class="col-12 mb-3">
                            <label class="text-muted small d-block">Descrição do Problema</label>
                            <p class="fw-semibold">${c.descricao_problema}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block">Localização</label>
                            <p>${c.bloco_nome} - ${c.ambiente_nome}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small d-block">Solicitante</label>
                            <p>${c.solicitante_nome}</p>
                        </div>
                    </div>
                    <div id="fotosContainer"></div>
                `;

                // 3. Carrega Fotos de Evidência
                const resAnexos = await fetch(`api/anexos.php?id_chamado=${idChamado}`);
                const anexos = await resAnexos.json();
                
                if(anexos.length > 0) {
                    let htmlFotos = '<hr><h6 class="mb-3">Evidências:</h6><div class="row g-2">';
                    anexos.forEach(arq => {
                        htmlFotos += `
                            <div class="col-4 col-md-3 text-center mb-2">
                                <img src="${arq.caminho_arquivo}" class="thumb-img" onclick="verFoto('${arq.caminho_arquivo}')">
                                <small class="text-muted" style="font-size: 0.7rem">${arq.tipo_anexo.toUpperCase()}</small>
                            </div>`;
                    });
                    document.getElementById('fotosContainer').innerHTML = htmlFotos + '</div>';
                }

                // 4. Lógica de botões de Ação (UC08)
                const area = document.getElementById('areaFechamento');
                if (c.status === 'concluido') {
                    area.innerHTML = `
                        <div class="alert alert-success d-flex justify-content-between align-items-center shadow-sm">
                            <div>
                                <h6 class="mb-1 fw-bold">Serviço Concluído pelo Técnico</h6>
                                <p class="mb-0 small">${c.solucao_tecnica || 'O técnico não deixou comentário.'}</p>
                            </div>
                            <button onclick="alterarStatusOS(${idChamado}, 'fechar')" class="btn btn-success px-4">Fechar O.S.</button>
                        </div>`;
                } else if (c.status === 'fechado') {
                    area.innerHTML = `<button onclick="alterarStatusOS(${idChamado}, 'reabrir')" class="btn btn-warning w-100 shadow-sm">Reabrir Chamado</button>`;
                }

            } catch (error) {
                console.error("Erro ao carregar:", error);
                document.getElementById('detalhesChamado').innerHTML = '<div class="alert alert-danger">Erro ao carregar dados.</div>';
            }
        }

        async function alterarStatusOS(id, acao) {
            if(!confirm(`Deseja realmente ${acao} este chamado?`)) return;
            const res = await fetch('api/gestor_acoes.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ id_chamado: id, acao: acao })
            });
            const data = await res.json();
            if(data.success) location.reload();
            else alert("Erro: " + data.message);
        }

        document.getElementById('formAtribuir').onsubmit = async (e) => {
            e.preventDefault();
            const res = await fetch('api/atribuir_chamado.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    id_chamado: idChamado,
                    id_tecnico: document.getElementById('selectTecnico').value,
                    prioridade: document.getElementById('prioridade').value,
                    data_prevista: document.getElementById('data_prevista').value
                })
            });
            const data = await res.json();
            if(data.success) window.location.href = 'gestor_chamados.php';
            else alert("Erro ao atribuir!");
        };

        carregarDados();
    </script>
</body>
</html>