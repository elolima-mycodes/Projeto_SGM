<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitante - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

</head>
<body>
    <header>
        <nav class="navbar navbar-dark bg-dark shadow-sm mb-4">
            <div class="container-fluid px-4">
            <span class="navbar-brand mb-0 h1">SGM | Solicitante</span>
            
            <div class="d-flex align-items-center">
                <span class="text-white me-3 d-none d-md-inline">Olá, Solicitante     |</span>
                <a href="api/logout.php" class="btn btn-outline-light btn-sm">Sair</a>
            </div>
        </div>
        </nav>
    </header>
    <main>
        <div class="d-flex justify-content-between">
            <div class="ms-6" style="margin-left: 50px">
                <h4 class="fw-bolder">Minhas solicitações</h4>
            </div>
            <div style="margin-right: 50px">
                <button class="btn btn-success btn-lg">+ Nova solicitação</button>
            </div>
            </div>
        <div class="justify-content-center" style="margin-top:20px">
            <table class="table mx-auto p-2 justify-content-center border border-opacity-50 shadow p-3 mb-5 bg-body-tertiary rounded" style="width:90rem;">
                <thead>
                    <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Foto</th>
                    <th scope="col">Local</th>
                    <th scope="col">Descrição</th>
                    <th scope="col">Data</th>
                    <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    <th scope="row">#1</th>
                    <td>Foto</td>
                    <td>Bloco Administrativo - Recepção</td>
                    <td>Ar condicionado quebrado</td>
                    <td>06/02/2026</td>
                    <td>Fechado</td>
                    </tr>
                    
                </tbody>
            </table>
        </div>

    </main>
</body>
</html>