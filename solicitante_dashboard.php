<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] != 'solicitante') {
    header("Location: login.php");
    exit;
}

// Handler para requisição AJAX de listagem (para não alterar arquivos na pasta api/)
if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    require_once 'config/database.php';
    header('Content-Type: application/json');
    $userId = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("SELECT 
        c.id_chamado, 
        c.descricao_problema, 
        c.status, 
        c.prioridade, 
        c.data_abertura, 
        c.id_ambiente,
        c.id_tipo_servico,
        a.nome as ambiente_nome, 
        b.nome as bloco_nome,
        b.id_bloco,
        an.caminho_arquivo as foto_caminho
    FROM chamados c 
    JOIN ambientes a ON c.id_ambiente = a.id_ambiente 
    JOIN blocos b ON a.id_bloco = b.id_bloco
    LEFT JOIN (
        SELECT id_chamado, MIN(caminho_arquivo) as caminho_arquivo
        FROM chamados_anexos 
        GROUP BY id_chamado
    ) an ON c.id_chamado = an.id_chamado
    WHERE c.id_solicitante = ? 
    ORDER BY c.data_abertura DESC");
    
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
    exit;
}

$pageTitle = 'SGM - Minhas Solicitações';
$activePage = 'dashboard';
$pageHeading = 'Minhas Solicitações';
$pageSubheading = 'Acompanhe o status dos seus chamados abertos.';

require_once 'includes/user_layout.php';
?>

<style>
    .mini-thumb {
        width: 45px;
        height: 45px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .mini-thumb:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    .btn-action {
        transition: all 0.2s ease;
    }
    .btn-action:hover {
        transform: translateY(-1px);
    }
</style>

<div class="row mb-4">
    <div class="col-12 text-end">
        <button class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="abrirCriarModal()">
            <i class="bi bi-plus-lg me-2"></i> Abrir Novo Chamado
        </button>
    </div>
</div>

<div class="content-panel">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 80px;">ID</th>
                    <th style="width: 100px;">Foto</th>
                    <th>Local</th>
                    <th>Descrição</th>
                    <th style="width: 130px;">Data</th>
                    <th style="width: 130px;">Status</th>
                    <th style="width: 320px;" class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody id="tabelaChamados">
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        Carregando solicitações...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Foto -->
<div class="modal fade" id="modalFoto" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg bg-dark text-white">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 text-center">
                <img id="imgModal" src="" class="img-fluid rounded shadow-sm" style="max-height: 80vh;">
            </div>
            <div class="modal-footer border-0 justify-content-center pt-0">
                <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Criar Chamado -->
<div class="modal fade" id="modalCriar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle-fill me-2"></i> Abrir Novo Chamado</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCriarChamado" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Bloco</label>
                            <select id="criarBloco" class="form-select border-0 bg-light py-2" required onchange="carregarAmbientesCriar(this.value)">
                                <option value="">Selecione...</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Ambiente</label>
                            <select id="criarAmbiente" class="form-select border-0 bg-light py-2" required disabled>
                                <option value="">Selecione...</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase text-muted">Tipo de Serviço</label>
                            <select id="criarTipoServico" class="form-select border-0 bg-light py-2" required>
                                <option value="">Selecione...</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase text-muted">Descrição do Problema</label>
                            <textarea id="criarDescricao" class="form-control border-0 bg-light py-2" rows="4" required placeholder="Descreva o problema aqui..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase text-muted">Anexar Foto (Opcional)</label>
                            <div class="input-group">
                                <input type="file" id="criarFoto" name="foto" class="form-control border-0 bg-light py-2" accept="image/*">
                                <span class="input-group-text border-0 bg-light"><i class="bi bi-camera"></i></span>
                            </div>
                            <div class="form-text mt-1 text-muted">Apenas imagens (jpg, jpeg, png, gif, webp).</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnSalvarCriar" class="btn btn-primary rounded-pill px-4 fw-bold">Registrar Chamado</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detalhes -->
<div class="modal fade" id="modalDetalhes" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-info-circle-fill me-2"></i> Detalhes do Chamado #<span id="detalheId"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="fw-bold small text-uppercase text-muted d-block mb-1">Status</label>
                        <div><span id="detalheStatus" class="badge rounded-pill px-3 py-2"></span></div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold small text-uppercase text-muted d-block mb-1">Prioridade</label>
                        <div><span id="detalhePrioridade" class="badge rounded-pill px-3 py-2"></span></div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold small text-uppercase text-muted d-block mb-1">Tipo de Serviço</label>
                        <div id="detalheTipoServico" class="fw-semibold text-dark"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold small text-uppercase text-muted d-block mb-1">Local / Ambiente</label>
                        <div id="detalheLocal" class="fw-semibold text-dark"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold small text-uppercase text-muted d-block mb-1">Previsão de Conclusão</label>
                        <div id="detalhePrevisao" class="fw-semibold text-primary"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold small text-uppercase text-muted d-block mb-1">Data de Fechamento</label>
                        <div id="detalheFechamento" class="fw-semibold text-secondary"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold small text-uppercase text-muted d-block mb-1">Tempo Gasto</label>
                        <div id="detalheTempo" class="fw-semibold text-dark"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold small text-uppercase text-muted d-block mb-1">Técnico Responsável</label>
                        <div id="detalheTecnico" class="fw-semibold text-dark"></div>
                    </div>
                    <div class="col-12 mt-3">
                        <label class="fw-bold small text-uppercase text-muted d-block mb-1">Descrição do Problema</label>
                        <div id="detalheDescricao" class="p-3 bg-light rounded text-break text-dark" style="white-space: pre-wrap; min-height: 60px;"></div>
                    </div>
                    <div class="col-12 mt-3">
                        <label class="fw-bold small text-uppercase text-muted d-block mb-1">Solução Técnica</label>
                        <div id="detalheSolucao" class="p-3 bg-light border-start border-success border-4 rounded text-break text-dark" style="white-space: pre-wrap; min-height: 60px;"></div>
                    </div>
                    <div class="col-12 mt-3">
                        <label class="fw-bold small text-uppercase text-muted d-block mb-1">Foto do Chamado</label>
                        <div id="detalheFotoArea" class="mt-2"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-dark border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Editar Chamado #<span id="editIdTitulo"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarChamado">
                <div class="modal-body p-4">
                    <input type="hidden" id="editId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Bloco</label>
                            <select id="editBloco" class="form-select border-0 bg-light py-2" required onchange="carregarAmbientesEdit(this.value)">
                                <option value="">Selecione...</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Ambiente</label>
                            <select id="editAmbiente" class="form-select border-0 bg-light py-2" required disabled>
                                <option value="">Selecione...</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase text-muted">Tipo de Serviço</label>
                            <select id="editTipoServico" class="form-select border-0 bg-light py-2" required>
                                <option value="">Selecione...</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase text-muted">Descrição do Problema</label>
                            <textarea id="editDescricao" class="form-control border-0 bg-light py-2" rows="4" required placeholder="Descreva o problema aqui..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="btnSalvarEdicao" class="btn btn-warning rounded-pill px-4 fw-bold">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let globalBlocos = [];
    let globalTipos = [];

    function verFoto(url) {
        document.getElementById('imgModal').src = url;
        new bootstrap.Modal(document.getElementById('modalFoto')).show();
    }

    async function carregarDadosAuxiliares() {
        try {
            const resB = await fetch('api/localizacoes.php?acao=listar_blocos');
            globalBlocos = await resB.json();
            
            const resT = await fetch('api/localizacoes.php?acao=listar_tipos');
            globalTipos = await resT.json();
        } catch (err) {
            console.error("Erro ao carregar dados auxiliares:", err);
        }
    }

    // Modal de Novo Chamado
    function abrirCriarModal() {
        document.getElementById('formCriarChamado').reset();
        document.getElementById('criarAmbiente').innerHTML = '<option value="">Selecione...</option>';
        document.getElementById('criarAmbiente').disabled = true;

        const selB = document.getElementById('criarBloco');
        selB.innerHTML = '<option value="">Selecione...</option>';
        globalBlocos.forEach(b => {
            const opt = document.createElement('option');
            opt.value = b.id_bloco;
            opt.textContent = b.nome;
            selB.appendChild(opt);
        });

        const selT = document.getElementById('criarTipoServico');
        selT.innerHTML = '<option value="">Selecione...</option>';
        globalTipos.forEach(t => {
            const opt = document.createElement('option');
            opt.value = t.id_tipo;
            opt.textContent = t.nome;
            selT.appendChild(opt);
        });

        new bootstrap.Modal(document.getElementById('modalCriar')).show();
    }

    async function carregarAmbientesCriar(id_bloco) {
        const selA = document.getElementById('criarAmbiente');
        selA.innerHTML = '<option value="">Selecione...</option>';
        
        if (!id_bloco) { 
            selA.disabled = true; 
            return; 
        }
       
        try {
            const res = await fetch(`api/localizacoes.php?acao=listar_ambientes&id_bloco=${id_bloco}`);
            const ambientes = await res.json();
           
            ambientes.forEach(a => {
                const opt = document.createElement('option');
                opt.value = a.id_ambiente;
                opt.textContent = a.nome;
                selA.appendChild(opt);
            });
            selA.disabled = false;
        } catch (err) {
            console.error("Erro ao carregar ambientes:", err);
        }
    }

    async function verDetalhes(id) {
        try {
            const response = await fetch(`api/solicitante_chamados.php?id=${id}`);
            const c = await response.json();
            
            document.getElementById('detalheId').textContent = c.id_chamado;
            
            const cores = { 
                'aberto': 'bg-secondary bg-opacity-10 text-secondary', 
                'agendado': 'bg-info bg-opacity-10 text-info', 
                'em_execucao': 'bg-warning bg-opacity-10 text-warning', 
                'concluido': 'bg-success bg-opacity-10 text-success', 
                'fechado': 'bg-dark bg-opacity-10 text-dark',
                'cancelado': 'bg-danger bg-opacity-10 text-danger'
            };
            
            const badgeStatus = document.getElementById('detalheStatus');
            badgeStatus.className = `badge rounded-pill px-3 py-2 ${cores[c.status] || 'bg-secondary'}`;
            badgeStatus.textContent = c.status.toUpperCase().replace('_', ' ');
            
            const coresPrioridade = {
                'baixa': 'bg-success bg-opacity-10 text-success',
                'media': 'bg-warning bg-opacity-10 text-warning',
                'alta': 'bg-danger bg-opacity-10 text-danger',
                'urgente': 'bg-danger text-white'
            };
            const badgePrioridade = document.getElementById('detalhePrioridade');
            badgePrioridade.className = `badge rounded-pill px-3 py-2 ${coresPrioridade[c.prioridade] || 'bg-secondary'}`;
            badgePrioridade.textContent = c.prioridade.toUpperCase();
            
            document.getElementById('detalheTipoServico').textContent = c.tipo_servico_nome || 'Não definido';
            document.getElementById('detalheLocal').textContent = `${c.bloco_nome} - ${c.ambiente_nome}`;
            
            document.getElementById('detalhePrevisao').textContent = c.data_previsao_conclusao ? new Date(c.data_previsao_conclusao + 'T00:00:00').toLocaleDateString('pt-BR') : 'Não informada';
            document.getElementById('detalheFechamento').textContent = c.data_fechamento ? new Date(c.data_fechamento).toLocaleString('pt-BR') : 'Aberto / Em andamento';
            document.getElementById('detalheTempo').textContent = c.tempo_gasto_minutos ? `${c.tempo_gasto_minutos} minutos` : 'Não registrado';
            document.getElementById('detalheTecnico').textContent = c.tecnico_nome || 'Não atribuído';
            document.getElementById('detalheDescricao').textContent = c.descricao_problema;
            document.getElementById('detalheSolucao').textContent = c.solucao_tecnica || 'Nenhuma solução técnica registrada até o momento.';
            
            // Carregar anexo/foto
            const resAnexos = await fetch(`api/anexos.php?id_chamado=${c.id_chamado}`);
            const anexos = await resAnexos.json();
            const fotoArea = document.getElementById('detalheFotoArea');
            if (anexos && anexos.length > 0) {
                fotoArea.innerHTML = `<img src="${anexos[0].caminho_arquivo}" class="img-fluid rounded border shadow-sm" style="max-height: 250px; cursor: pointer;" onclick="verFoto('${anexos[0].caminho_arquivo}')" title="Clique para ampliar">`;
            } else {
                fotoArea.innerHTML = '<div class="text-muted small"><i class="bi bi-image me-1"></i> Nenhuma foto anexada a este chamado.</div>';
            }
            
            new bootstrap.Modal(document.getElementById('modalDetalhes')).show();
        } catch (err) {
            console.error("Erro ao carregar detalhes:", err);
            alert("Erro ao carregar os detalhes do chamado.");
        }
    }

    async function abrirEditarModal(id) {
        try {
            const response = await fetch(`api/solicitante_chamados.php?id=${id}`);
            const c = await response.json();

            // Trava de segurança no frontend
            if (c.status !== 'aberto') {
                alert("Somente chamados com status 'aberto' podem ser editados.");
                return;
            }

            document.getElementById('editIdTitulo').textContent = c.id_chamado;
            document.getElementById('editId').value = c.id_chamado;

            // Preencher Bloco
            const selB = document.getElementById('editBloco');
            selB.innerHTML = '<option value="">Selecione...</option>';
            globalBlocos.forEach(b => {
                const opt = document.createElement('option');
                opt.value = b.id_bloco;
                opt.textContent = b.nome;
                if (b.id_bloco == c.id_bloco) opt.selected = true;
                selB.appendChild(opt);
            });

            // Carregar Ambientes e selecionar
            await carregarAmbientesEdit(c.id_bloco, c.id_ambiente);

            // Preencher Tipo de Serviço
            const selT = document.getElementById('editTipoServico');
            selT.innerHTML = '<option value="">Selecione...</option>';
            globalTipos.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t.id_tipo;
                opt.textContent = t.nome;
                if (t.id_tipo == c.id_tipo_servico) opt.selected = true;
                selT.appendChild(opt);
            });

            // Preencher Descrição
            document.getElementById('editDescricao').value = c.descricao_problema;

            new bootstrap.Modal(document.getElementById('modalEditar')).show();
        } catch (err) {
            console.error("Erro ao carregar dados do chamado para edição:", err);
            alert("Erro ao abrir formulário de edição.");
        }
    }

    async function carregarAmbientesEdit(id_bloco, id_ambiente_selecionado = null) {
        const selA = document.getElementById('editAmbiente');
        selA.innerHTML = '<option value="">Selecione...</option>';
        
        if (!id_bloco) { 
            selA.disabled = true; 
            return; 
        }
       
        try {
            const res = await fetch(`api/localizacoes.php?acao=listar_ambientes&id_bloco=${id_bloco}`);
            const ambientes = await res.json();
           
            ambientes.forEach(a => {
                const opt = document.createElement('option');
                opt.value = a.id_ambiente;
                opt.textContent = a.nome;
                if (id_ambiente_selecionado && a.id_ambiente == id_ambiente_selecionado) {
                    opt.selected = true;
                }
                selA.appendChild(opt);
            });
            selA.disabled = false;
        } catch (err) {
            console.error("Erro ao carregar ambientes:", err);
        }
    }

    async function excluirChamado(id) {
        try {
            // Validar o status antes de solicitar confirmação
            const response = await fetch(`api/solicitante_chamados.php?id=${id}`);
            const c = await response.json();

            // Trava de segurança no frontend
            if (c.status !== 'aberto') {
                alert("Somente chamados com status 'aberto' podem ser excluídos.");
                return;
            }

            if (!confirm(`Tem certeza que deseja excluir o chamado #${id}?`)) {
                return;
            }

            const res = await fetch('api/solicitante_chamados.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id_chamado: id })
            });
            
            const result = await res.json();
            if (result.success) {
                await carregarChamados();
            } else {
                alert("Erro: " + result.message);
            }
        } catch (err) {
            console.error("Erro ao excluir chamado:", err);
            alert("Ocorreu um erro ao tentar excluir o chamado.");
        }
    }

    async function carregarChamados() {
        try {
            const response = await fetch('solicitante_dashboard.php?ajax=1');
            const chamados = await response.json();
            const lista = document.getElementById('tabelaChamados');
            
            const cores = { 
                'aberto': 'bg-secondary bg-opacity-10 text-secondary', 
                'agendado': 'bg-info bg-opacity-10 text-info', 
                'em_execucao': 'bg-warning bg-opacity-10 text-warning', 
                'concluido': 'bg-success bg-opacity-10 text-success', 
                'fechado': 'bg-dark bg-opacity-10 text-dark',
                'cancelado': 'bg-danger bg-opacity-10 text-danger'
            };

            if (!chamados || chamados.length === 0) {
                lista.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-muted">Nenhum chamado encontrado.</td></tr>';
                return;
            }

            const rows = chamados.map(c => {
                const thumbHtml = (c.foto_caminho) ?
                    `<img src="${c.foto_caminho}" class="mini-thumb" onclick="verFoto('${c.foto_caminho}')" title="Clique para ver imagem original">` :
                    '<div class="rounded bg-light d-flex align-items-center justify-content-center" style="width:45px; height:45px;"><i class="bi bi-image text-muted opacity-50"></i></div>';

                // Botão de Editar visível apenas se status for 'aberto'
                const bEditar = (c.status === 'aberto') ?
                    `<button class="btn btn-outline-warning btn-sm rounded-pill px-3 me-1 fw-bold btn-action" onclick="abrirEditarModal(${c.id_chamado})"><i class="bi bi-pencil me-1"></i>Editar</button>` :
                    '';

                // Botão de Excluir visível apenas se status for 'aberto'
                const bExcluir = (c.status === 'aberto') ?
                    `<button class="btn btn-outline-danger btn-sm rounded-pill px-3 mb-3 mt-3 fw-bold btn-action" onclick="excluirChamado(${c.id_chamado})"><i class="bi bi-trash me-1"></i>Excluir</button>` :
                    '';

                const bDetalhes = `<button class="btn btn-outline-primary btn-sm rounded-pill px-3 me-5 fw-bold btn-action" onclick="verDetalhes(${c.id_chamado})"><i class="bi bi-eye me-1"></i>Detalhes</button>`;

                return `<tr>
                    <td class="fw-bold">#${c.id_chamado}</td>
                    <td>${thumbHtml}</td>
                    <td>
                        <div class="fw-bold">${c.bloco_nome}</div>
                        <div class="small text-muted">${c.ambiente_nome}</div>
                    </td>
                    <td>
                        <div class="text-truncate" style="max-width: 300px;" title="${c.descricao_problema}">
                            ${c.descricao_problema}
                        </div>
                    </td>
                    <td><span class="small text-muted">${new Date(c.data_abertura).toLocaleDateString('pt-BR')}</span></td>
                    <td><span class="badge rounded-pill ${cores[c.status] || 'bg-secondary'}">${c.status.toUpperCase().replace('_', ' ')}</span></td>
                    <td class="text-end">
                        ${bEditar}
                        ${bExcluir}
                        ${bDetalhes}
                    </td>
                </tr>`;
            });

            lista.innerHTML = rows.join('');
        } catch (err) {
            console.error("Erro ao carregar:", err);
            document.getElementById('tabelaChamados').innerHTML = '<tr><td colspan="7" class="text-center py-5 text-danger">Erro ao carregar dados.</td></tr>';
        }
    }

    // Submit de Criar Chamado
    document.getElementById('formCriarChamado').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('btnSalvarCriar');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Registrando...';

        const formData = new FormData();
        formData.append('id_ambiente', document.getElementById('criarAmbiente').value);
        formData.append('id_tipo', document.getElementById('criarTipoServico').value);
        formData.append('descricao', document.getElementById('criarDescricao').value);
       
        const fotoFile = document.getElementById('criarFoto').files[0];
        if (fotoFile) {
            formData.append('foto', fotoFile);
        }

        try {
            const response = await fetch('api/salvar_chamado.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            if (result.success) {
                // Fechar modal
                const modalEl = document.getElementById('modalCriar');
                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();
                
                // Recarregar listagem
                await carregarChamados();
            } else {
                alert("Erro: " + result.message);
            }
        } catch (err) {
            console.error("Erro ao registrar chamado:", err);
            alert("Ocorreu um erro ao enviar sua solicitação.");
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    // Submit de Editar Chamado
    document.getElementById('formEditarChamado').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('btnSalvarEdicao');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Salvando...';

        const id = document.getElementById('editId').value;
        const data = {
            id_chamado: parseInt(id),
            id_ambiente: parseInt(document.getElementById('editAmbiente').value),
            id_tipo_servico: parseInt(document.getElementById('editTipoServico').value),
            descricao_problema: document.getElementById('editDescricao').value
        };
        
        try {
            const res = await fetch('api/solicitante_chamados.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            const result = await res.json();
            if (result.success) {
                // Fechar modal
                const modalEl = document.getElementById('modalEditar');
                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();
                
                // Recarregar chamados
                await carregarChamados();
            } else {
                alert("Erro: " + result.message);
            }
        } catch (err) {
            console.error("Erro ao salvar edição:", err);
            alert("Ocorreu um erro ao atualizar o chamado.");
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
    
    // Iniciar carregando auxiliares e os chamados
    (async () => {
        await carregarDadosAuxiliares();
        await carregarChamados();
    })();
</script>

<?php require_once 'includes/user_footer.php'; ?>