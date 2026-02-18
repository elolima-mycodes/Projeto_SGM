<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitante - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    
    <style>
        /* CORREÇÃO: Removida a tag style duplicada */
        .mini-thumb {
            width: 60px !important;
            height: 60px !important;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            border: 2px solid #dee2e6;
            transition: transform 0.2s;
        }

        .mini-thumb:hover {
            transform: scale(1.1);
        }

        .col-foto { width: 100px; text-align: center; }
        .col-id { width: 80px; }
        .col-data { width: 120px; }
        .col-status { width: 140px; }
        
        /* Quebra de linha para descrições longas não estourarem a tabela */
        td {
            word-wrap: break-word;
            overflow-wrap: break-word;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-dark bg-dark shadow-sm mb-4">
            <div class="container-fluid px-4">
                <span class="navbar-brand mb-0 h1">SGM | Solicitante</span>
                <div class="d-flex align-items-center">
                    <span class="text-white me-3 d-none d-md-inline">Olá, Solicitante |</span>
                    <a href="api/logout.php" class="btn btn-outline-light btn-sm">Sair</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4 px-md-5">
            <h4 class="fw-bolder mb-0">Minhas solicitações</h4>
            <a href="solicitante_abrir_chamado.php" class="btn btn-success btn-lg">
                <i class="bi bi-plus-lg"></i> Nova solicitação
            </a>
        </div>

        <div class="px-md-5">
            <table class="table border shadow-sm bg-body-tertiary rounded" style="table-layout: fixed; width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="col-id">ID</th>
                        <th scope="col" class="col-foto">Foto</th>
                        <th scope="col">Local</th>
                        <th scope="col">Descrição</th>
                        <th scope="col" class="col-data">Data</th>
                        <th scope="col" class="col-status">Status</th>
                    </tr>
                </thead>
                <tbody id="tabelaChamados">
                    </tbody>
            </table>
        </div>

        <div class="modal fade" id="modalFoto" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body p-0">
                        <img id="imgModal" src="" class="img-fluid rounded">
                    </div>
                </div>
            </div>
        </div>
    </main>

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