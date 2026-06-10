<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'tecnico') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'SGM - Minhas Tarefas';
$activePage = 'minhas_tarefas';
$pageHeading = 'Minha Fila de Trabalho';
$pageSubheading = 'Gerencie suas tarefas designadas para hoje.';

require_once 'includes/user_layout.php';
?>

<div class="content-panel">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1">Tarefas atribuídas</h5>
            <p class="text-muted mb-0">Somente chamados com status diferente de aberto ou concluído aparecem aqui.</p>
        </div>
        <button class="btn btn-sm btn-outline-primary rounded-pill" onclick="carregarTarefas()">Atualizar</button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">ID</th>
                    <th>Descrição</th>
                    <th>Prioridade</th>
                    <th>Status</th>
                    <th>Prazo</th>
                    <th class="text-center">Anexos</th>
                    <th class="text-end pe-4">Ações</th>
                </tr>
            </thead>
            <tbody id="tabelaTarefas">
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <div class="spinner-border text-primary spinner-border-sm me-2"></div>
                        Carregando suas tarefas...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de Edição pelo Técnico -->
<div class="modal fade" id="modalEditarTarefa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title">Editar Chamado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarTarefa">
                    <input type="hidden" id="edIdChamado">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Solicitante</label>
                            <input type="text" id="edSolicitante" class="form-control" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ambiente</label>
                            <input type="text" id="edAmbiente" class="form-control" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Prioridade</label>
                            <input type="text" id="edPrioridade" class="form-control" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Data de Abertura</label>
                            <input type="text" id="edDataAbertura" class="form-control" disabled>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descrição do Problema</label>
                            <textarea id="edDescricao" class="form-control" rows="4" disabled></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select id="edStatus" class="form-select">
                                <option value="agendado">AGENDADO</option>
                                <option value="em_execucao">EM EXECUÇÃO</option>
                                <option value="fechado">FECHADO</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Previsão de Conclusão</label>
                            <input type="date" id="edDataPrevista" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Anexos</label>
                            <div id="edAnexos" class="d-flex flex-wrap gap-2"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary rounded-pill" onclick="salvarTarefa()">Salvar</button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/user_footer.php'; ?>

<script>
    const statusOcultos = ['aberto', 'concluido'];

    async function carregarTarefas() {
        const tabela = document.getElementById('tabelaTarefas');
        tabela.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-muted"><div class="spinner-border text-primary spinner-border-sm me-2"></div>Carregando suas tarefas...</td></tr>';
        try {
            const res = await fetch('api/tecnico_chamados.php');
            const tarefas = await res.json();
            const filtradas = tarefas.filter(t => !statusOcultos.includes(t.status));

            if (filtradas.length === 0) {
                tabela.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-muted">Nenhuma tarefa pendente para os filtros aplicados.</td></tr>';
                return;
            }

            tabela.innerHTML = filtradas.map(t => `
                <tr>
                    <td class="ps-4 fw-bold">#${t.id_chamado}</td>
                    <td class="text-truncate" style="max-width: 250px;">${t.descricao_problema}</td>
                    <td>${t.prioridade.toUpperCase()}</td>
                    <td>${t.status.replace('_', ' ').toUpperCase()}</td>
                    <td>${t.data_previsao_conclusao ? t.data_previsao_conclusao.split(' ')[0] : '<span class="text-muted">-</span>'}</td>
                    <td id="anexos-${t.id_chamado}" class="text-center"><span class="text-muted">Carregando...</span></td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="abrirModalEdicao(${t.id_chamado})">Editar</button>
                    </td>
                </tr>
            `).join('');

            filtradas.forEach(t => carregarAnexosLinha(t.id_chamado));
        } catch (error) {
            console.error('Erro ao carregar tarefas:', error);
            tabela.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-danger">Erro ao carregar tarefas. Tente novamente.</td></tr>';
        }
    }

    async function carregarAnexosLinha(idChamado) {
        try {
            const res = await fetch(`api/anexos.php?id_chamado=${idChamado}`);
            const anexos = await res.json();
            const celula = document.getElementById(`anexos-${idChamado}`);
            if (!celula) return;
            if (anexos && anexos.length > 0) {
                celula.innerHTML = `<button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="mostrarAnexo('${anexos[0].caminho_arquivo}')"><i class="bi bi-camera-fill"></i> ${anexos.length}</button>`;
            } else {
                celula.innerHTML = '<span class="text-muted">-</span>';
            }
        } catch (error) {
            console.error('Erro ao carregar anexos da linha:', error);
        }
    }

    function mostrarAnexo(url) {
        const modal = new bootstrap.Modal(document.getElementById('modalFoto'));
        document.getElementById('imgModal').src = url;
        modal.show();
    }

    async function abrirModalEdicao(idChamado) {
        try {
            const res = await fetch(`api/tecnico_chamados.php?id=${idChamado}`);
            const chamado = await res.json();
            if (!chamado || !chamado.id_chamado) {
                alert('Chamado não encontrado ou não atribuído a você.');
                return;
            }

            document.getElementById('edIdChamado').value = chamado.id_chamado;
            document.getElementById('edSolicitante').value = chamado.solicitante_nome || '';
            document.getElementById('edAmbiente').value = chamado.ambiente_nome || '';
            document.getElementById('edPrioridade').value = chamado.prioridade || '';
            document.getElementById('edDataAbertura').value = new Date(chamado.data_abertura).toLocaleString('pt-BR');
            document.getElementById('edDescricao').value = chamado.descricao_problema || '';
            document.getElementById('edStatus').value = chamado.status || 'agendado';
            document.getElementById('edDataPrevista').value = chamado.data_previsao_conclusao ? chamado.data_previsao_conclusao.split(' ')[0] : '';
            document.getElementById('edAnexos').innerHTML = '<div class="spinner-border spinner-border-sm text-primary"></div> Carregando...';

            const anexosRes = await fetch(`api/anexos.php?id_chamado=${idChamado}`);
            const anexos = await anexosRes.json();
            const container = document.getElementById('edAnexos');
            if (anexos && anexos.length) {
                container.innerHTML = anexos.map(arq => `<img src="${arq.caminho_arquivo}" class="thumb-img" onclick="mostrarAnexo('${arq.caminho_arquivo}')" title="Visualizar anexo">`).join('');
            } else {
                container.innerHTML = '<span class="text-muted">Nenhum anexo encontrado.</span>';
            }

            new bootstrap.Modal(document.getElementById('modalEditarTarefa')).show();
        } catch (error) {
            console.error('Erro ao abrir modal de edição:', error);
            alert('Não foi possível carregar o chamado.');
        }
    }

    async function salvarTarefa() {
        const idChamado = document.getElementById('edIdChamado').value;
        const status = document.getElementById('edStatus').value;
        const dataPrevista = document.getElementById('edDataPrevista').value;

        const payload = {
            id_chamado: parseInt(idChamado, 10),
            status,
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
                alert('Chamado atualizado com sucesso!');
                carregarTarefas();
                bootstrap.Modal.getInstance(document.getElementById('modalEditarTarefa')).hide();
            } else {
                alert('Erro: ' + result.message);
            }
        } catch (error) {
            console.error('Erro ao salvar tarefa:', error);
            alert('Erro de comunicação com o servidor.');
        }
    }

    carregarTarefas();
</script>