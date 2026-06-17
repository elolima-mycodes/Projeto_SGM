<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: gestor_chamados.php");
    exit;
}

$pageTitle = 'SGM - Detalhes do Chamado';
$activePage = 'chamados';
$pageHeading = 'Gerenciar Chamado #' . $id;
$pageSubheading = 'Visualize, edite ou remova as informações do chamado.';

require_once 'includes/gestor_layout.php';
?>

<style>
    .detail-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        background: #fff;
        height: 100%;
    }
    .info-group {
        padding: 1.2rem;
        border-radius: 12px;
        background: #f8fafc;
        margin-bottom: 1rem;
        border: 1px solid #e2e8f0;
    }
    .info-label {
        font-size: 0.75rem;
        color: #94a3b8;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.5rem;
        display: block;
    }
    .info-value {
        font-size: 1rem;
        color: #1e293b;
        font-weight: 600;
    }
    .form-control-custom {
        background-color: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.75rem;
        font-weight: 500;
    }
    .form-control-custom:focus {
        background-color: #fff;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }
    .thumb-img {
        width: 100px; height: 100px;
        object-fit: cover;
        border-radius: 10px;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .thumb-img:hover { transform: scale(1.05); }
</style>

<div class="mb-4">
    <a href="gestor_chamados.php" class="btn btn-light rounded-pill px-4 fw-bold">
        <i class="bi bi-arrow-left me-2"></i>Voltar
    </a>
</div>

<div class="row g-4">
    <!-- Bloco 1: Detalhes do Chamado -->
    <div class="col-lg-6">
        <div class="card detail-card p-4">
            <h5 class="fw-bold mb-4 d-flex align-items-center">
                <span class="rounded-circle bg-primary bg-opacity-10 p-2 me-2">
                    <i class="bi bi-info-circle text-primary"></i>
                </span>
                Informações do Chamado
            </h5>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="info-label">Solicitante</label>
                    <div id="displaySolicitante" class="info-value">Carregando...</div>
                </div>
                <div class="col-md-6 mb-3 text-md-end">
                    <label class="info-label">Data de Abertura</label>
                    <div id="displayData" class="info-value">--/--/----</div>
                </div>
            </div>

            <div class="mb-3">
                <label class="info-label">Descrição do Problema</label>
                <textarea id="descProblema" class="form-control form-control-custom" rows="4"></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="info-label">Localização</label>
                    <div id="displayLocal" class="info-value">Carregando...</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="info-label">Tipo de Serviço</label>
                    <select id="selectTipoServico" class="form-select form-control-custom">
                        <option value="">Nenhum tipo selecionado</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="info-label">Status do Chamado</label>
                    <select id="selectStatus" class="form-select form-control-custom">
                        <option value="aberto">ABERTO</option>
                        <option value="agendado">AGENDADO</option>
                        <option value="em_execucao">EM EXECUÇÃO</option>
                        <option value="concluido">CONCLUÍDO</option>
                        <option value="fechado">FECHADO</option>
                    </select>
                </div>
            </div>

            <div id="fotosContainer" class="mt-3 d-flex gap-2 flex-wrap"></div>
        </div>
    </div>

    <!-- Bloco 2: Atribuição e Prioridade -->
    <div class="col-lg-6">
        <div class="card detail-card p-4">
            <h5 class="fw-bold mb-4 d-flex align-items-center">
                <span class="rounded-circle bg-warning bg-opacity-10 p-2 me-2">
                    <i class="bi bi-person-gear text-warning"></i>
                </span>
                Atribuição e Planejamento
            </h5>
            
            <form id="formGestor">
                <div class="mb-4">
                    <label class="info-label">Técnico Responsável</label>
                    <select id="selectTecnico" class="form-select form-control-custom">
                        <option value="">Nenhum técnico atribuído</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="info-label">Nível de Prioridade</label>
                    <select id="prioridade" class="form-select form-control-custom">
                        <option value="baixa">BAIXA</option>
                        <option value="media">MÉDIA</option>
                        <option value="alta">ALTA</option>
                        <option value="urgente">URGENTE</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="info-label">Previsão de Conclusão</label>
                    <input type="date" id="data_prevista" class="form-control form-control-custom">
                </div>

                <div id="infoConclusao" class="mt-auto pt-3 border-top d-none">
                    <label class="info-label text-success">Solução do Técnico</label>
                    <p id="solucaoTecnica" class="small text-muted mb-0"></p>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Botões de Ação no Rodapé -->
<div class="mt-5 d-flex gap-3 justify-content-center pb-5">
    <button type="button" onclick="excluirChamado()" class="btn btn-outline-danger px-5 py-3 rounded-pill fw-bold shadow-sm">
        <i class="bi bi-trash me-2"></i>Excluir Chamado
    </button>
    <button type="button" onclick="salvarAlteracoes()" class="btn btn-primary px-5 py-3 rounded-pill fw-bold shadow-lg">
        <i class="bi bi-check2-circle me-2"></i>Salvar Alterações
    </button>
</div>

<!-- Modal para Ampliar Foto -->
<div class="modal fade" id="modalFoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body p-0 text-center">
                <img id="imgModal" src="" class="img-fluid rounded-4 shadow-lg">
                <button type="button" class="btn btn-light rounded-pill mt-3 px-4 fw-bold" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
    const idChamado = <?= $id ?>;

    function ampliarFoto(url) {
        document.getElementById('imgModal').src = url;
        new bootstrap.Modal(document.getElementById('modalFoto')).show();
    }

    async function carregarDados() {
        try {
            // 1. Carrega Técnicos
            const resTec = await fetch('api/usuarios.php?perfil=tecnico&ativo=1');
            const tecWrap = await resTec.json();
            const tecnicos = tecWrap.data || [];
            const selTec = document.getElementById('selectTecnico');
            tecnicos.forEach(t => {
                selTec.innerHTML += `<option value="${t.id_usuario}">${t.nome}</option>`;
            });

            const resTipos = await fetch('api/localizacoes.php?acao=listar_tipos');
            const tipos = await resTipos.json();
            const selTipoServico = document.getElementById('selectTipoServico');
            tipos.forEach(t => {
                selTipoServico.innerHTML += `<option value="${t.id_tipo}">${t.nome}</option>`;
            });

            // 2. Carrega Dados do Chamado
            const res = await fetch(`api/gestor_chamados.php?id=${idChamado}`);
            const c = await res.json();

            if (!c || !c.id_chamado) {
                alert("Chamado não encontrado!");
                window.location.href = 'gestor_chamados.php';
                return;
            }

            // Preenche Campos Estáticos
            document.getElementById('displaySolicitante').innerText = c.solicitante_nome;
            document.getElementById('displayData').innerText = new Date(c.data_abertura).toLocaleString('pt-BR');
            document.getElementById('displayLocal').innerText = `${c.bloco_nome} - ${c.ambiente_nome}`;
            
            // Preenche Campos Editáveis
            document.getElementById('descProblema').value = c.descricao_problema;
            document.getElementById('selectStatus').value = c.status;
            document.getElementById('selectTecnico').value = c.id_tecnico || "";
            document.getElementById('selectTipoServico').value = c.id_tipo_servico || "";
            document.getElementById('prioridade').value = c.prioridade || "baixa";
            if (c.data_previsao_conclusao) {
                document.getElementById('data_prevista').value = c.data_previsao_conclusao.split(' ')[0];
            }

            // Exibe solução se houver
            if (c.solucao_tecnica) {
                document.getElementById('infoConclusao').classList.remove('d-none');
                document.getElementById('solucaoTecnica').innerText = c.solucao_tecnica;
            }

            // 3. Carrega Fotos
            const resAnexos = await fetch(`api/anexos.php?id_chamado=${idChamado}`);
            const anexos = await resAnexos.json();
            const container = document.getElementById('fotosContainer');
            if (anexos && anexos.length > 0) {
                anexos.forEach(arq => {
                    container.innerHTML += `<img src="${arq.caminho_arquivo}" class="thumb-img" onclick="ampliarFoto('${arq.caminho_arquivo}')">`;
                });
            }

        } catch (error) {
            console.error(error);
            alert("Erro ao carregar dados do servidor.");
        }
    }

    async function salvarAlteracoes() {
        const dados = {
            id_chamado: idChamado,
            descricao_problema: document.getElementById('descProblema').value,
            status: document.getElementById('selectStatus').value,
            id_tecnico: document.getElementById('selectTecnico').value ? parseInt(document.getElementById('selectTecnico').value, 10) : null,
            id_tipo_servico: document.getElementById('selectTipoServico').value ? parseInt(document.getElementById('selectTipoServico').value, 10) : null,
            prioridade: document.getElementById('prioridade').value,
            data_previsao_conclusao: document.getElementById('data_prevista').value || null
        };

        try {
            const res = await fetch('api/gestor_chamados.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dados)
            });
            const result = await res.json();
            if (res.ok && result.success) {
                alert("Chamado atualizado com sucesso!");
                window.location.href = 'gestor_chamados.php';
            } else {
                const msg = result.message || (res.statusText ? res.statusText : 'Erro desconhecido');
                alert("Erro ao atualizar: " + msg);
            }
        } catch (e) {
            alert("Erro de comunicação com o servidor.");
        }
    }

    async function excluirChamado() {
        if (!confirm("Tem certeza que deseja excluir este chamado permanentemente?")) return;

        try {
            const res = await fetch('api/gestor_chamados.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_chamado: idChamado })
            });
            const result = await res.json();
            if (result.success) {
                alert("Chamado excluído com sucesso.");
                window.location.href = 'gestor_chamados.php';
            } else {
                alert("Erro: " + result.message);
            }
        } catch (e) {
            alert("Erro ao tentar excluir.");
        }
    }

    carregarDados();
</script>

<?php require_once 'includes/gestor_footer.php'; ?>