<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor - Chamados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    
</head>
<body>
    <header>
    <nav class="navbar navbar-expand navbar-dark bg-dark shadow-sm mb-4">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="#">SGM | Gestão</a>


            <div class="d-flex align-items-center">
                <ul class="navbar-nav me-auto" style="margin-left: 1000px">
                <li class="nav-item">
                    <a class="nav-link active" href="#">Chamados</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Locais</a>
                </li>
                
            </ul> <br>
                <div class="vr bg-white opacity-25 me-3 d-none d-md-block" style="height: 20px;"></div> 
                <a href="api/logout.php" class="btn btn-outline-light btn-sm">Sair</a>
            </div>
        </div>
    </nav>
</header>
    <main>
        <h3 class="ms-5 mb-4">Todos os chamados</h3>
        <div class="d-flex gap-3 ms-5">
            <button class="btn btn-outline-secondary">Todos</button>
            <button class="btn btn-outline-primary">Abertos</button>
            <button class="btn btn-outline-warning">Em Andamento</button>
            <button class="btn btn-outline-danger">Concluídos</button>
        </div>
        <div class="justify-content-center" style="margin-top:20px">
            <table class="table mx-auto p-2 justify-content-center border border-opacity-50 shadow p-3 mb-5 bg-body-tertiary rounded" style="width:90rem;"> 
                <thead>
                    <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Solicitante</th>
                    <th scope="col">Local/Tipo</th>
                    <th scope="col">Prioridade</th>
                    <th scope="col">Tecnico</th>
                    <th scope="col">Status</th>
                    <th scope="col">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    <th scope="row">#1</th>
                    <td>Maria</td>
                    <td>Bloco Administrativo - Recepção</td>
                    <td><i class="bi bi-circle-fill text-warning me-2"></i>Alta</td>
                    <td>João</td>
                    <td><span class="badge bg-secondary">Fechado</span></td>
                    <td><span class="badge bg-primary"><i class="bi bi-gear-wide me-2"></i>Gerenciar</span></td>
                    </tr>
                    
                </tbody>
            </table>
        </div>
    </main>
    
</body>
</html>