<?php
session_start();

if (!isset($_SESSION['user_perfil']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'SGM - Dashboard Gestor';
$activePage = 'dashboard';
$pageHeading = 'Olá, ' . explode(' ', $_SESSION['user_nome'])[0];
$pageSubheading = 'Aqui está o resumo da operação hoje.';
require_once 'includes/gestor_layout.php';
?>

<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card-soft bg-white p-4 d-flex align-items-center">
            <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                <i class="bi bi-envelope-paper text-primary fs-3"></i>
            </div>
            <div>
                <span class="text-muted small fw-bold text-uppercase">Abertos</span>
                <h2 class="fw-bold m-0" id="numNovos">0</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-soft bg-white p-4 d-flex align-items-center">
            <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                <i class="bi bi-tools text-warning fs-3"></i>
            </div>
            <div>
                <span class="text-muted small fw-bold text-uppercase">Em Execução</span>
                <h2 class="fw-bold m-0" id="numAndamento">0</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-soft bg-white p-4 d-flex align-items-center">
            <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                <i class="bi bi-exclamation-triangle text-danger fs-3"></i>
            </div>
            <div>
                <span class="text-muted small fw-bold text-uppercase">Críticos</span>
                <h2 class="fw-bold m-0" id="numCritico">0</h2>
            </div>
        </div>
    </div>
</div>

<div class="content-panel">
    <h5 class="fw-bold mb-4">Atividades Recentes</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Solicitante</th>
                    <th>Local</th>
                    <th>Prioridade</th>
                    <th>Status</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody id="tabelaRecentes">
                <tr>
                    <td colspan="6" class="text-center py-4">Carregando atividades...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    async function carregarStats() {
        try {
            const response = await fetch('api/dashboard_gestor.php');
            const data = await response.json();
            
            if(data.error) return;

            document.getElementById('numNovos').innerText = data.abertos;
            document.getElementById('numAndamento').innerText = data.em_execucao;
            document.getElementById('numCritico').innerText = data.urgentes;

            // Carregar recentes (exemplo simplificado baseado na estrutura anterior)
            const resChamados = await fetch('api/chamados.php');
            const chamadosData = await resChamados.json();
            const recentes = Array.isArray(chamadosData) ? chamadosData.slice(0, 5) : [];
            
            const tabela = document.getElementById('tabelaRecentes');
            if (recentes.length === 0) {
                tabela.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">Nenhuma atividade recente.</td></tr>';
            } else {
                tabela.innerHTML = recentes.map(c => `
                    <tr>
                        <td class="fw-bold">#${c.id_chamado}</td>
                        <td>${c.solicitante_nome}</td>
                        <td><small>${c.bloco_nome} - ${c.ambiente_nome}</small></td>
                        <td><span class="badge bg-light text-dark border">${c.prioridade || 'N/A'}</span></td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary">${c.status.toUpperCase()}</span></td>
                        <td><a href="gestor_detalhes.php?id=${c.id_chamado}" class="btn btn-sm btn-outline-primary rounded-pill">Ver</a></td>
                    </tr>
                `).join('');
            }
            
        } catch (error) {
            console.error("Erro ao buscar estatísticas:", error);
        }
    }

    carregarStats();
    setInterval(carregarStats, 30000);
</script>

<?php require_once 'includes/gestor_footer.php'; ?>