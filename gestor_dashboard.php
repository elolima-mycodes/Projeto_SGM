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
            --sidebar-width: 260px;
            --primary: #4f46e5;
            --bg-light: #f8fafc;
            --text-dark: #1e293b;
        }

        body { background-color: var(--bg-light); font-family: 'Inter', sans-serif; overflow-x: hidden; }

        /* Sidebar Fixa */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: #1e293b;
            position: fixed;
            left: 0; top: 0;
            padding: 1.5rem;
            color: white;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }

        /* Cartões de Métricas */
        .stat-card {
            border: none;
            border-radius: 16px;
            padding: 1.5rem;
            transition: transform 0.2s;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .stat-card:hover { transform: translateY(-5px); }

        .icon-box {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        /* Estilos específicos dos cards */
        .card-novos { background: #eff6ff; color: #1e40af; }
        .card-novos .icon-box { background: #dbeafe; }

        .card-andamento { background: #fffbeb; color: #92400e; }
        .card-andamento .icon-box { background: #fef3c7; }

        .card-critico { background: #fef2f2; color: #991b1b; }
        .card-critico .icon-box { background: #fee2e2; }

        /* Menu lateral */
        .nav-link {
            color: #94a3b8;
            padding: 0.8rem 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            display: flex; align-items: center;
            text-decoration: none;
        }
        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .nav-link i { margin-right: 12px; }
    </style>
</head>
<body>
    <nav class="sidebar">
        <h4 class="fw-bold mb-5"><i class="bi bi-shield-check"></i> SGM</h4>
        <div class="nav flex-column">
            <a href="gestor_dashboard.php" class="nav-link active"><i class="bi bi-grid-1x2"></i> Dashboard</a>
            <a href="gestor_chamados.php" class="nav-link"><i class="bi bi-ticket-perforated"></i> Chamados</a>
            <a href="gestor_lista_blocos.php" class="nav-link"><i class="bi bi-building"></i> Infraestrutura</a>
            <a href="gestor_lista_usuarios.php" class="nav-link"><i class="bi bi-people"></i> Utilizadores</a>
            <hr class="text-secondary">
            <a href="api/logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-left"></i> Sair</a>
        </div>
    </nav>

    <main class="main-content">
        <header class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold m-0">Olá, Gestor</h2>
                <p class="text-muted">Aqui está o resumo da operação hoje.</p>
            </div>
            <button class="btn btn-primary px-4 rounded-pill">
                <i class="bi bi-plus-lg"></i> Novo Chamado
            </button>
        </header>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="stat-card card-novos">
                    <div class="icon-box"><i class="bi bi-envelope-paper"></i></div>
                    <span class="small fw-bold text-uppercase">Abertos</span>
                    <h2 class="display-5 fw-bold m-0" id="numNovos">0</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card card-andamento">
                    <div class="icon-box"><i class="bi bi-tools"></i></div>
                    <span class="small fw-bold text-uppercase">Em Execução</span>
                    <h2 class="display-5 fw-bold m-0" id="numAndamento">0</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card card-critico">
                    <div class="icon-box"><i class="bi bi-exclamation-triangle"></i></div>
                    <span class="small fw-bold text-uppercase">Críticos</span>
                    <h2 class="display-5 fw-bold m-0" id="numCritico">0</h2>
                </div>
            </div>
        </div>

        <div class="mt-5 p-4 bg-white rounded-4 shadow-sm">
            <h5 class="fw-bold mb-4">Atividades Recentes</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Solicitante</th>
                            <th>Local</th>
                            <th>Prioridade</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaRecentes">
                        </tbody>
                </table>
            </div>
        </div>
    </main>
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