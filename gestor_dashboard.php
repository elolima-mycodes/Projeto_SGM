<?php
session_start();

if (!isset($_SESSION['user_perfil']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Gestor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: #2563eb;     
            --dark-bg: #0f172a;     
            --light-bg: #f1f5f9;     
            --text-main: #1e293b;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        body { 
            background-color: var(--light-bg);
            font-family: 'Inter', sans-serif;
            color: var(--text-main);
        }

        .navbar-custom {
            background-color: var(--dark-bg);
            padding: 0.8rem 2rem;
            border-bottom: 3px solid var(--primary);
        }

        .card-gestao {
            border: none !important;
            border-radius: 12px !important;
            background: #ffffff;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .card-gestao::before {
            content: "";
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 5px;
        }

        .border-primary::before { background-color: #3b82f6; }
        .border-warning::before { background-color: #f59e0b; }
        .border-danger::before { background-color: #ef4444; }

        .card-gestao:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .btn-primario {
            background-color: var(--primary);
            color: white;
            border-radius: 8px;
            padding: 10px 25px;
            border: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
                <i class="bi bi-shield-check fs-4 me-2 text-info"></i> SGM | GESTÃO
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-person-circle me-1"></i> Perfil</a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center gap-3">
                    <span class="text-white fw-light d-none d-md-inline">Olá, Gestor</span>
                    <button type="button" class="btn btn-outline-light btn-sm ms-lg-3" data-bs-toggle="modal" data-bs-target="#modalLogout">
                        <i class="bi bi-box-arrow-right me-1"></i> Sair
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-5">
        <div class="container">
                    
                    <div class="row mb-4 text-center">
                        <div class="col mb-5">
                           
                            <h1 class="display-3 fw-bold" style="color: var(--azul-marinho)">Visão Geral</h1>
                            <div class="mx-auto" style="width: auto; height: 5px; background-color: var(--azul-royal); border-radius: 5px;"></div>
                        
                        </div>
                    </div>

                    <div class="row mb-5 justify-content-center mt-5">
                        <div class="col-md-4 mb-3">
                            <div class="shadow-sm text-center p-3 card border-primary border-5">
                                <div class="card-body">
                                    <h6 class="text-muted fw-bold">Novas solicitações</h6>
                                    <h2 id="numNovos" class="display-5 fw-bold text-primary">0</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="shadow-sm text-center p-3 card border-warning border-5">
                                <div class="card-body">
                                    <h6 class="text-muted fw-bold">Em Andamento</h6>
                                    <h2 id="numAndamento" class="display-5 fw-bold text-warning">0</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="shadow-sm text-center p-3 card border-danger border-5">
                                <div class="card-body">
                                    <h6 class="text-muted fw-bold">Crítico</h6>
                                    <h2 id="numCritico" class="display-5 fw-bold text-danger">0</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="d-flex col-12 gap-1 align-middle">
                            <a href="gestor_chamados.php" class="btn btn-primario px-5 py-3 shadow-sm fw-bold">
                               <i class="bi bi-list-check me-2"></i> Gerenciar Chamados
                            </a>
                            <a href="gestor_lista_ambientes.php" class="btn btn-primario px-5 py-3 shadow-sm fw-bold">
                               <i class="bi bi-geo-alt me-2"></i> Gerenciar Ambientes
                            </a>
                            <a href="gestor_lista_blocos.php" class="btn btn-primario px-5 py-3 shadow-sm fw-bold">
                               <i class="bi bi-building"></i> Gerenciar Blocos
                            </a>
                            <a href="gestor_lista_tipos_de_servico.php" class="btn btn-primario px-5 py-3 shadow-sm fw-bold">
                               <i class="bi bi-geo-alt me-2"></i> Gerenciar Tipos de Serviço
                            </a>
                            <a href="gestor_lista_usuarios.php" class="btn btn-primario px-5 py-3 shadow-sm fw-bold">
                               <i class="bi bi-geo-alt me-2"></i> Gerenciar Usuários
                            </a>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <div class="modal fade" id="modalLogout" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white justify-content-center" style="background-color: var(--azul-marinho)">
                    <h5 class="modal-title">Encerrar Sessão</h5>
                </div>
                <div class="modal-body text-center py-5">
                    <i class="bi bi-exclamation-circle text-danger display-3 mb-3"></i>
                    <p class="fs-5 text-secondary">Sua sessão será encerrada!<br>Deseja continuar?</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                    <a href="api/logout.php" class="btn btn-danger px-4">Sair</a>
                </div>
            </div>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    async function carregarStats() {
        try {
            const response = await fetch('api/dashboard_gestor.php');
            const data = await response.json();
            
            if(data.error) return;

            // Atualiza os números na tela
            document.getElementById('numNovos').innerText = data.abertos;
            document.getElementById('numAndamento').innerText = data.em_execucao;
            document.getElementById('numCritico').innerText = data.urgentes;
            
        } catch (error) {
            console.error("Erro ao buscar estatísticas:", error);
        }
    }

    // Carrega ao abrir a página
    carregarStats();
    
    // Opcional: Atualiza a cada 30 segundos sozinho
    setInterval(carregarStats, 30000);
</script>
</body>
</html>