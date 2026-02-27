<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor - Editar Serviço</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow border-0 rounded-4">
                    <div class="card-body p-4">
                        <h2 class="fw-bold text-center mb-4">Editar Serviço</h2>
                        
                        <form id="formAdicionarAmbiente">

                            <div class="mb-3">
                                <label class="form-label fw-bold">Nome</label>
                                <textarea id="nomeAmbiente" class="form-control" rows="1"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Descrição</label>
                                <textarea id="nomeAmbiente" class="form-control" rows="3"></textarea>
                            </div>
                            
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="gestor_lista_tipos_de_servico.php" class="btn btn-secondary py-2 w-40 fw-bold">Cancelar</a>
                                <button type="submit" class="btn btn-primary w-40 fw-bold py-2">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>