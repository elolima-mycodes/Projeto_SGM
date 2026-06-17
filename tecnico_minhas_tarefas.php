<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'tecnico') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'SGM - Minhas Tarefas';
$activePage = 'minhas_tarefas';
$pageHeading = 'Minha Fila de Trabalho';
$pageSubheading = 'Gerencie suas tarefas designadas.';

require_once 'includes/user_layout.php';
?>

<style>
    /* Estilos Premium para a Fila de Trabalho */
    .filter-bar {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(226, 232, 240, 0.8);
    }
    
    .stats-widget {
        transition: all 0.3s ease;
        border-radius: 12px;
        border: 1px solid rgba(226, 232, 240, 0.8);
        background: #ffffff;
    }
    .stats-widget:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .task-card {
        border-radius: 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(226, 232, 240, 0.8);
        background: #ffffff;
        overflow: hidden;
    }
    .task-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 20px rgba(0, 0, 0, 0.06);
    }

    /* Destaques e Cores por Status (Bordas Laterais) */
    .task-card.status-agendado {
        border-left: 6px solid #3b82f6 !important; /* Azul */
    }
    .task-card.status-em_execucao {
        border-left: 6px solid #eab308 !important; /* Amarelo/Laranja */
        background: #fffbeb; /* Tom sutil e quente */
    }
    .task-card.status-concluido {
        border-left: 6px solid #10b981 !important; /* Verde */
        background: #f8fafc;
        opacity: 0.85;
    }
    .task-card.status-concluido .task-title {
        text-decoration: line-through;
        color: #64748b;
    }

    /* Container e Imagem da Foto no Card */
    .task-img-container {
        width: 110px;
        height: 110px;
        flex-shrink: 0;
        background-color: #f1f5f9;
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(226, 232, 240, 0.8);
    }
    .task-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        cursor: pointer;
        transition: transform 0.2s ease;
    }
    .task-img:hover {
        transform: scale(1.05);
    }

    /* Painel de Atualizações Rápidas */
    .update-panel {
        background: #f8fafc;
        border-radius: 12px;
        padding: 0.75rem;
        border: 1px solid rgba(226, 232, 240, 0.5);
    }

    /* Badges de Prioridade */
    .badge-prioridade-urgente {
        background-color: #fee2e2;
        color: #ef4444;
        border: 1px solid #fca5a5;
    }
    .badge-prioridade-alta {
        background-color: #ffedd5;
        color: #f97316;
        border: 1px solid #fed7aa;
    }
    .badge-prioridade-media {
        background-color: #fef9c3;
        color: #854d0e;
        border: 1px solid #fef08a;
    }
    .badge-prioridade-baixa {
        background-color: #f1f5f9;
        color: #64748b;
        border: 1px solid #cbd5e1;
    }

    .animate-pulse {
        animation: pulse 1.6s infinite;
    }
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.5;
            transform: scale(0.92);
        }
    }

    /* Estilo Especial das Imagens dentro do Modal de Detalhes */
    .modal-detail-img {
        max-width: 100%;
        max-height: 350px;
        object-fit: contain;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        display: block;
        margin: 10px auto 0;
    }
</style>

<div class="content-panel mb-4">
    <div class="row align-items-center g-3">
        <div class="col-md-6">
            <h5 class="mb-1 fw-bold">Minhas Tarefas Atribuídas</h5>
            <p class="text-muted mb-0 small">Gerencie seu fluxo de trabalho, alterne status e consulte prazos.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm" onclick="carregarTarefas()">
                <i class="bi bi-arrow-clockwise me-1"></i> Atualizar Fila
            </button>
        </div>
    </div>
</div>

<!-- Barra de Filtros Dinâmicos -->
<div class="filter-bar p-3 mb-4">
    <div class="row g-3 align-items-center">
        <!-- Busca por ID ou Descrição -->
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="filtroBusca" class="form-control border-start-0 ps-0" placeholder="Buscar por ID ou descrição..." oninput="filtrarERenderizarTarefas()">
            </div>
        </div>
        <!-- Filtro Prioridade -->
        <div class="col-md-3">
            <select id="filtroPrioridade" class="form-select" onchange="filtrarERenderizarTarefas()">
                <option value="">Todas as prioridades</option>
                <option value="baixa">Prioridade: Baixa</option>
                <option value="media">Prioridade: Média</option>
                <option value="alta">Prioridade: Alta</option>
                <option value="urgente">Prioridade: Urgente</option>
            </select>
        </div>
        <!-- Filtro Status -->
        <div class="col-md-3">
            <select id="filtroStatus" class="form-select" onchange="filtrarERenderizarTarefas()">
                <option value="">Todos os status ativos</option>
                <option value="agendado">Status: Agendado</option>
                <option value="em_execucao">Status: Em Execução</option>
                <option value="concluido">Status: Concluído</option>
            </select>
        </div>
        <!-- Ocultar Finalizados Switch -->
        <div class="col-md-2 d-flex justify-content-md-end">
            <div class="form-check form-switch d-flex align-items-center gap-2">
                <input class="form-check-input" type="checkbox" id="filtroOcultarFinalizados" onchange="filtrarERenderizarTarefas()" style="cursor: pointer;">
                <label class="form-check-label text-muted fw-semibold small mb-0" for="filtroOcultarFinalizados" style="cursor: pointer; user-select: none;">Ocultar Finalizados</label>
            </div>
        </div>
    </div>
</div>

<!-- Resumo do Status das Tarefas (Mini-dashboard) -->
<div class="row g-3 mb-4" id="statsContainer">
    <!-- Renderizado dinamicamente pelo JS -->
</div>

<!-- Listagem de Tarefas (Cards) -->
<div id="cardsTarefas" class="row">
    <!-- Cards inseridos dinamicamente pelo JS -->
</div>

<!-- Modal Detalhes do Chamado (Totalmente Desabilitado / Visualização) -->
<div class="modal fade" id="modalDetalhesTarefa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom py-3">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                    <i class="bi bi-info-circle-fill text-primary me-2"></i> 
                    Detalhes do Chamado #<span id="detIdChamadoLabel"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formDetalhesTarefa">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small">Solicitante</label>
                            <input type="text" id="detSolicitante" class="form-control bg-light" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small">Ambiente / Local</label>
                            <input type="text" id="detAmbiente" class="form-control bg-light" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small">Prioridade</label>
                            <input type="text" id="detPrioridade" class="form-control bg-light" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small">Data de Abertura</label>
                            <input type="text" id="detDataAbertura" class="form-control bg-light" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small">Status Atual</label>
                            <input type="text" id="detStatus" class="form-control bg-light" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small">Previsão de Conclusão</label>
                            <input type="date" id="detDataPrevista" class="form-control bg-light" disabled>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-muted small">Descrição do Problema</label>
                            <textarea id="detDescricao" class="form-control bg-light" rows="3" disabled></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-muted small">Solução Técnica</label>
                            <textarea id="detSolucaoTecnica" class="form-control bg-light" rows="3" disabled placeholder="Nenhuma solução técnica cadastrada"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-muted small d-block mb-2">Fotos / Anexos</label>
                            <div id="detAnexos" class="d-flex flex-wrap gap-2 border p-3 rounded bg-light min-height-50 justify-content-center align-items-center">
                                <!-- Preenchido dinamicamente -->
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Foto (Para zoom da imagem) -->
<div class="modal fade" id="modalFoto" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg bg-dark text-white">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body p-3 text-center">
                <img id="imgModal" src="" class="img-fluid rounded shadow-sm" style="max-height: 80vh; object-fit: contain;">
            </div>
            <div class="modal-footer border-0 justify-content-center pt-0">
                <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/user_footer.php'; ?>

<script>
    let tarefasAcumuladas = [];

    // Carrega as tarefas vinculadas ao técnico da API
    async function carregarTarefas() {
        const container = document.getElementById('cardsTarefas');
        container.innerHTML = `
            <div class="col-12 text-center py-5 text-muted bg-white rounded-3 shadow-sm border">
                <div class="spinner-border text-primary mb-3" role="status"></div>
                <div>Carregando suas tarefas...</div>
            </div>
        `;

        try {
            const res = await fetch('api/tecnico_chamados.php');
            const tarefas = await res.json();
            
            // Armazena no array global
            tarefasAcumuladas = tarefas;
            
            // Executa filtros e renderização local
            filtrarERenderizarTarefas();
        } catch (error) {
            console.error('Erro ao carregar tarefas:', error);
            container.innerHTML = `
                <div class="col-12 text-center py-5 text-danger bg-white rounded-3 shadow-sm border">
                    <i class="bi bi-exclamation-triangle-fill mb-3 text-warning" style="font-size: 3rem; display: block;"></i>
                    Erro ao carregar tarefas do servidor. Tente novamente clicando no botão "Atualizar Fila".
                </div>
            `;
        }
    }

    // Filtra tarefas com base nas opções e renderiza na tela
    function filtrarERenderizarTarefas() {
        const busca = (document.getElementById('filtroBusca').value || '').toLowerCase().trim();
        const prioridade = document.getElementById('filtroPrioridade').value;
        const status = document.getElementById('filtroStatus').value;
        const ocultarFinalizados = document.getElementById('filtroOcultarFinalizados').checked;

        const filtradas = tarefasAcumuladas.filter(t => {
            // Filtrar apenas chamados ativos no fluxo técnico (ignora aberto, fechado e cancelado)
            if (t.status === 'aberto' || t.status === 'fechado' || t.status === 'cancelado') {
                return false;
            }

            // Filtro por termo de busca (ID ou descrição)
            if (busca) {
                const matchesId = t.id_chamado.toString().includes(busca);
                const matchesDesc = (t.descricao_problema || '').toLowerCase().includes(busca);
                if (!matchesId && !matchesDesc) return false;
            }

            // Filtro por prioridade
            if (prioridade && t.prioridade !== prioridade) {
                return false;
            }

            // Filtro por status
            if (status && t.status !== status) {
                return false;
            }

            // Filtro de "Ocultar Finalizados" (Oculta status concluído)
            if (ocultarFinalizados && t.status === 'concluido') {
                return false;
            }

            return true;
        });

        // Atualiza os contadores estáticos de status com base em todos os chamados técnicos ativos do usuário
        const activeTasks = tarefasAcumuladas.filter(t => t.status === 'agendado' || t.status === 'em_execucao' || t.status === 'concluido');
        renderizarStats(activeTasks);

        // Renderiza os cartões de chamados filtrados
        renderizarCards(filtradas);
    }

    // Renderiza a barra de contadores (Dashboard Mini)
    function renderizarStats(tarefas) {
        const total = tarefas.length;
        const agendados = tarefas.filter(t => t.status === 'agendado').length;
        const emExecucao = tarefas.filter(t => t.status === 'em_execucao').length;
        const concluidos = tarefas.filter(t => t.status === 'concluido').length;

        const statsContainer = document.getElementById('statsContainer');
        statsContainer.innerHTML = `
            <div class="col-6 col-md-3">
                <div class="stats-widget p-3 d-flex align-items-center gap-3">
                    <div class="bg-primary-subtle text-primary p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-list-task"></i>
                    </div>
                    <div>
                        <div class="small text-muted fw-medium">Total</div>
                        <h5 class="fw-bold mb-0">${total}</h5>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stats-widget p-3 d-flex align-items-center gap-3">
                    <div class="bg-info-subtle text-info p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div>
                        <div class="small text-muted fw-medium">Agendadas</div>
                        <h5 class="fw-bold mb-0">${agendados}</h5>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stats-widget p-3 d-flex align-items-center gap-3">
                    <div class="bg-warning-subtle text-warning-emphasis p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-play-circle"></i>
                    </div>
                    <div>
                        <div class="small text-muted fw-medium">Em Execução</div>
                        <h5 class="fw-bold mb-0">${emExecucao}</h5>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stats-widget p-3 d-flex align-items-center gap-3">
                    <div class="bg-success-subtle text-success p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="small text-muted fw-medium">Concluídas</div>
                        <h5 class="fw-bold mb-0">${concluidos}</h5>
                    </div>
                </div>
            </div>
        `;
    }

    // Renderiza a lista de cartões (cards)
    function renderizarCards(tarefas) {
        const container = document.getElementById('cardsTarefas');
        if (tarefas.length === 0) {
            container.innerHTML = `
                <div class="col-12 text-center py-5 text-muted bg-white rounded-3 shadow-sm border">
                    <i class="bi bi-inbox mb-3" style="font-size: 3rem; display: block;"></i>
                    Nenhuma tarefa pendente para os filtros aplicados.
                </div>
            `;
            return;
        }

        container.innerHTML = tarefas.map(t => {
            let prioBadgeClass = '';
            let prioLabel = '';
            let flagPulse = '';
            
            switch(t.prioridade) {
                case 'urgente':
                    prioBadgeClass = 'badge-prioridade-urgente';
                    prioLabel = 'Urgente';
                    flagPulse = 'animate-pulse';
                    break;
                case 'alta':
                    prioBadgeClass = 'badge-prioridade-alta';
                    prioLabel = 'Alta';
                    break;
                case 'media':
                    prioBadgeClass = 'badge-prioridade-media';
                    prioLabel = 'Média';
                    break;
                default:
                    prioBadgeClass = 'badge-prioridade-baixa';
                    prioLabel = 'Baixa';
                    break;
            }

            const dateAbertura = new Date(t.data_abertura).toLocaleString('pt-BR');

            return `
                <div class="col-12 mb-3">
                    <div class="card task-card status-${t.status} p-3 shadow-sm">
                        <div class="row align-items-center g-3">
                            <div class="col-auto">
                                <div class="task-img-container" id="anexo-container-${t.id_chamado}">
                                    <div class="spinner-border spinner-border-sm text-secondary" role="status"></div>
                                </div>
                            </div>

                            <div class="col flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis fw-bold">#${t.id_chamado}</span>
                                    <span class="badge ${prioBadgeClass}">
                                        <i class="bi bi-flag-fill me-1 ${flagPulse}"></i> Prioridade: ${prioLabel}
                                    </span>
                                    <span class="small text-muted"><i class="bi bi-clock me-1"></i> Aberto em: ${dateAbertura}</span>
                                </div>
                                <h5 class="task-title mb-1 fw-bold text-dark text-break">${t.descricao_problema}</h5>
                            </div>

                            <div class="col-md-3">
                                <div class="update-panel d-flex flex-column gap-2" id="update-panel-${t.id_chamado}">
                                    
                                    <button class="btn btn-sm btn-outline-primary w-100 rounded-pill" onclick="abrirModalDetalhes(${t.id_chamado})">
                                        <i class="bi bi-info-circle me-1"></i> Ver Detalhes
                                    </button>

                                    <hr class="my-1 text-muted opacity-25"> 
                                    
                                    <div>
                                        <label class="form-label text-muted fw-semibold small mb-1">Status da Tarefa</label>
                                        <select class="form-select form-select-sm fw-semibold" id="status-${t.id_chamado}">
                                            <option value="agendado" ${t.status === 'agendado' ? 'selected' : ''}>Agendado</option>
                                            <option value="em_execucao" ${t.status === 'em_execucao' ? 'selected' : ''}>Em Execução</option>
                                            <option value="concluido" ${t.status === 'concluido' ? 'selected' : ''}>Concluído</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="form-label text-muted fw-semibold small mb-1">Previsão Conclusão</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar-event text-muted"></i></span>
                                            <input type="date" class="form-control border-start-0" id="data-${t.id_chamado}" value="${t.data_previsao_conclusao ? t.data_previsao_conclusao.split(' ')[0] : ''}">
                                        </div>
                                    </div>

                                    <button class="btn btn-sm btn-success w-100 mt-1 rounded-pill fw-bold" onclick="salvarAtualizacaoCompleta(${t.id_chamado})">
                                        <i class="bi bi-check-circle me-1"></i> Atualizar
                                    </button>
                                    
                                </div>
                            </div>
                            </div>
                    </div>
                </div>
            `;
        }).join('');

        // Carrega o anexo correspondente para cada card de forma assíncrona
        tarefas.forEach(t => carregarAnexoCard(t.id_chamado));
    }

    // Busca anexo do chamado da API e renderiza no card
    async function carregarAnexoCard(idChamado) {
        const container = document.getElementById(`anexo-container-${idChamado}`);
        if (!container) return;

        try {
            const res = await fetch(`api/anexos.php?id_chamado=${idChamado}`);
            const anexos = await res.json();
            if (anexos && anexos.length > 0) {
                const url = anexos[0].caminho_arquivo;
                container.innerHTML = `<img src="${url}" class="task-img" onclick="mostrarAnexo('${url}')" title="Clique para ampliar">`;
            } else {
                container.innerHTML = `<i class="bi bi-image text-muted opacity-50" style="font-size: 2rem;"></i>`;
            }
        } catch (error) {
            console.error(`Erro ao carregar anexo para tarefa #${idChamado}:`, error);
            container.innerHTML = `<i class="bi bi-exclamation-circle text-danger" title="Erro ao carregar imagem"></i>`;
        }
    }

    // Abre modal para zoom da foto/imagem
    function mostrarAnexo(url) {
        document.getElementById('imgModal').src = url;
        const modal = new bootstrap.Modal(document.getElementById('modalFoto'));
        modal.show();
    }

    // Envia atualização via PUT para a API quando há alteração de status ou data previsora
    async function salvarAtualizacaoCompleta(idChamado) {
        const selectEl = document.getElementById(`status-${idChamado}`);
        const dateEl = document.getElementById(`data-${idChamado}`);
        const panelEl = document.getElementById(`update-panel-${idChamado}`);

        if (!selectEl || !dateEl) return;

        const status = selectEl.value;
        const dataPrevista = dateEl.value;

        // Desabilita os campos temporariamente e aplica efeito visual de carregamento
        selectEl.disabled = true;
        dateEl.disabled = true;
        panelEl.style.opacity = '0.5';

        const payload = {
            id_chamado: parseInt(idChamado, 10),
            status: status,
            data_previsao_conclusao: dataPrevista || null
        };

        try {
            const res = await fetch('api/tecnico_chamados.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            
            const result = await res.json();
            
            if (result.success) {
                // Atualiza a listagem geral e recarrega os dados da API com sucesso
                await carregarTarefas();
            } else {
                alert('Erro ao atualizar dados: ' + result.message);
                // Devolve o controle ao usuário caso a API recuse a alteração
                selectEl.disabled = false;
                dateEl.disabled = false;
                panelEl.style.opacity = '1';
            }
        } catch (error) {
            console.error('Erro ao salvar atualização do chamado:', error);
            alert('Erro de comunicação com o servidor.');
            // Devolve o controle em caso de falha de internet
            selectEl.disabled = false;
            dateEl.disabled = false;
            panelEl.style.opacity = '1';
        }
    }

    // Carrega dados completos do chamado e abre o modal de visualização de detalhes (somente leitura)
    async function abrirModalDetalhes(idChamado) {
        try {
            const res = await fetch(`api/tecnico_chamados.php?id=${idChamado}`);
            const chamado = await res.json();
            
            if (!chamado || !chamado.id_chamado) {
                alert('Chamado não encontrado ou não atribuído a você.');
                return;
            }

            // Preenche os dados nos inputs desabilitados
            document.getElementById('detIdChamadoLabel').textContent = chamado.id_chamado;
            document.getElementById('detSolicitante').value = chamado.solicitante_nome || 'Não informado';
            document.getElementById('detAmbiente').value = chamado.ambiente_nome || 'Não informado';
            document.getElementById('detPrioridade').value = (chamado.prioridade || '').toUpperCase();
            document.getElementById('detDataAbertura').value = new Date(chamado.data_abertura).toLocaleString('pt-BR');
            document.getElementById('detStatus').value = (chamado.status || '').replace('_', ' ').toUpperCase();
            document.getElementById('detDataPrevista').value = chamado.data_previsao_conclusao ? chamado.data_previsao_conclusao.split(' ')[0] : '';
            document.getElementById('detDescricao').value = chamado.descricao_problema || '';
            document.getElementById('detSolucaoTecnica').value = chamado.solucao_tecnica || '';

            // Carrega e exibe os anexos aplicando as regras adequadas de CSS
            const detAnexos = document.getElementById('detAnexos');
            detAnexos.innerHTML = `
                <div class="text-center py-2 text-muted">
                    <div class="spinner-border spinner-border-sm text-primary me-2"></div> Carregando imagens...
                </div>
            `;

            const anexosRes = await fetch(`api/anexos.php?id_chamado=${idChamado}`);
            const anexos = await anexosRes.json();
            
            if (anexos && anexos.length > 0) {
                detAnexos.innerHTML = anexos.map(arq => `
                    <img src="${arq.caminho_arquivo}" class="modal-detail-img" onclick="mostrarAnexo('${arq.caminho_arquivo}')" title="Clique para ampliar" style="cursor: pointer;">
                `).join('');
            } else {
                detAnexos.innerHTML = '<span class="text-muted small"><i class="bi bi-image me-1"></i> Nenhum anexo encontrado para esta tarefa.</span>';
            }

            // Exibe o modal
            const modal = new bootstrap.Modal(document.getElementById('modalDetalhesTarefa'));
            modal.show();
        } catch (error) {
            console.error('Erro ao abrir detalhes:', error);
            alert('Não foi possível carregar as informações do chamado.');
        }
    }

    // Inicialização da listagem
    carregarTarefas();
</script>