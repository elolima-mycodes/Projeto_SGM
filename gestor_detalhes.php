<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor - Detalhes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

</head>
<body>
    <main>
        <div class="d-flex gap-3 container-xxl">
            <div class="container-lg gap-3">
                <button class="btn btn-outline-secondary mt-2"><a href="#">Voltar</a></button>
                <section class="shadow p-4 mb-4 bg-body-tertiary rounded" style="height:500px">
                    <h5 class="fw-bold mb-4">Detalhes da Solicitação</h5>
                    <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted small">Status:</span>
                        <span class="badge bg-primary rounded-pill">Fechado</span>
                    </li>
                    <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted small">Descrição:</span>
                        <span class="fw-semibold">Ar condicionado quebrado</span>
                    </li>
                    <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted small">Local:</span>
                        <span class="fw-semibold">Bloco Administrativo - Recepção</span>
                    </li>
                    <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted small">Técnico Responsável:</span>
                        <span class="fw-semibold">Ricardo Souza</span>
                    </li>
                    <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted small">Abertura:</span>
                        <span>06/02/2026 - 10:30</span>
                    </li>
                </ul>
                </section>
                <div class="d-grid gap-2"><button class="btn btn-warning">Reabrir Chamado</button></div>
            </div>
            <section class="shadow p-3 mb-5 bg-body-tertiary rounded container-fluid" style="height:300px"></section>
        </div>
    </main>
    
</body>
</html>