<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitante - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    
    <style>
    :root {
        --azul-marinho: #1B263B;
        --azul-royal: #415A77;
        --gelo: #F8F9FA;
    }

    body { 
        background-color: var(--gelo); 
    }

    /* Navbar Custom */
    .navbar-custom {
        background-color: var(--azul-marinho);
        padding: 0.8rem 2rem;
    }

    /* Título Centralizado */
    .titulo-secao {
        color: var(--azul-marinho);
        font-weight: 800;
        text-align: center;
        margin-top: 2.5rem;
        margin-bottom: 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .titulo-secao::after {
        content: "";
        width: 60px;
        height: 4px;
        background: var(--azul-royal);
        display: block;
        margin-top: 10px;
        border-radius: 2px;
    }

    /* Tabela e Thumbnails */
    .mini-thumb {
        width: 50px !important;
        height: 50px !important;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        border: 2px solid #dee2e6;
        transition: all 0.2s;
    }

    .mini-thumb:hover {
        transform: scale(1.15);
        border-color: var(--azul-royal);
    }

    .card-tabela {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        background: white;
    }

    .table thead {
        background-color: var(--azul-marinho);
        color: white;
    }

    .table thead th {
        font-weight: 500;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 1px;
        padding: 15px;
        border: none;
    }

    /* Botão Nova Solicitação */
    .btn-nova-solicitacao {
        background-color: var(--azul-marinho);
        color: white;
        border-radius: 50px;
        padding: 12px 30px;
        font-weight: 600;
        transition: 0.3s;
        border: none;
    }

    .btn-nova-solicitacao:hover {
        background-color: var(--azul-royal);
        color: white;
        transform: translateY(-2px);
        shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .col-id { width: 70px; }
    .col-foto { width: 90px; text-align: center; }
    .col-data { width: 130px; }
    .col-status { width: 140px; }
</style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm mb-4">
            <div class="container">
                <span class="navbar-brand fw-bold">
                    <i class="bi bi-person-badge me-2 text-info"></i>SGM | SOLICITANTE
                </span>
                <div class="ms-auto d-flex align-items-center">
                    <span class="text-white me-3 d-none d-md-inline opacity-75">Olá, Solicitante</span>
                    <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalLogout">
                        Sair
                    </button>
                </div>
            </div>
        </nav>
    </header>

    <main class="container">
        <div class="titulo-secao">
            <h2 class="display-6">Minhas Solicitações</h2>
        </div>

        <div class="text-center mb-5">
            <a href="solicitante_abrir_chamado.php" class="btn btn-nova-solicitacao shadow">
                <i class="bi bi-plus-lg me-2"></i> Abrir Novo Chamado
            </a>
        </div>

        <div class="card card-tabela shadow-sm mb-5">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="table-layout: fixed; width: 100%;">
                    <thead>
                        <tr>
                            <th class="col-id ps-4">ID</th>
                            <th class="col-foto">Foto</th>
                            <th>Local</th>
                            <th>Descrição</th>
                            <th class="col-data">Data</th>
                            <th class="col-status pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaChamados">
                        </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="modalFoto" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg bg-dark">
                    <div class="modal-body p-0">
                        <img id="imgModal" src="" class="img-fluid rounded">
                    </div>
                    <div class="modal-footer border-0 justify-content-center">
                        <button type="button" class="btn btn-light btn-sm rounded-pill px-4" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="modalLogout" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white justify-content-center border-0" style="background-color: var(--azul-marinho)">
                    <h5 class="modal-title fw-bold">Encerrar Sessão</h5>
                </div>
                <div class="modal-body text-center py-5">
                    <i class="bi bi-door-open text-warning display-3 mb-3"></i>
                    <p class="fs-5 text-secondary">Você está prestes a sair do sistema.<br>Deseja continuar?</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button type="button" class="btn btn-light px-4 rounded-pill shadow-sm" data-bs-dismiss="modal">Voltar</button>
                    <a href="api/logout.php" class="btn btn-danger px-4 rounded-pill shadow-sm">Sim, Sair</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function verFoto(url) {
            document.getElementById('imgModal').src = url;
            new bootstrap.Modal(document.getElementById('modalFoto')).show();
        }

        async function carregarChamados() {
            try {
                const response = await fetch('api/chamados.php');
                const chamados = await response.json();
                const lista = document.getElementById('tabelaChamados');
                
                const cores = { 
                    'aberto': 'bg-secondary', 
                    'agendado': 'bg-info', 
                    'em_execucao': 'bg-warning text-dark', 
                    'concluido': 'bg-success', 
                    'fechado': 'bg-dark' 
                };

                // Limpa a tabela antes de carregar
                lista.innerHTML = '';

                const rows = await Promise.all(chamados.map(async c => {
                    // Busca anexos
                    const resAnexos = await fetch(`api/anexos.php?id_chamado=${c.id_chamado}`);
                    const anexos = await resAnexos.json();
                    
                    const thumbHtml = (anexos && anexos.length > 0) ?
                        `<img src="${anexos[0].caminho_arquivo}" class="mini-thumb" onclick="verFoto('${anexos[0].caminho_arquivo}')">` :
                        '<i class="bi bi-image text-muted fs-3"></i>';

                    return `<tr>
                        <td class="fw-bold">#${c.id_chamado}</td>
                        <td class="text-center">${thumbHtml}</td>
                        <td>${c.bloco_nome} - ${c.ambiente_nome}</td>
                        <td>${c.descricao_problema}</td>
                        <td>${new Date(c.data_abertura).toLocaleDateString('pt-BR')}</td>
                        <td><span class="badge ${cores[c.status] || 'bg-secondary'}">${c.status.toUpperCase().replace('_', ' ')}</span></td>
                    </tr>`;
                }));

                lista.innerHTML = rows.join('');
            } catch (err) {
                console.error("Erro ao carregar:", err);
            }
        }
        
        carregarChamados();
    </script>
</body>
</html>