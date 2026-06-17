<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'gestor') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'SGM - Abrir Chamado';
$activePage = 'chamados';
$pageHeading = 'Abrir Novo Chamado';
$pageSubheading = 'Registre uma solicitação que será atribuída a um técnico.';

require_once 'includes/gestor_layout.php';
?>

<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        <div class="content-panel shadow-sm p-4">
            <form id="formAbrirChamado">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase text-muted">Bloco</label>
                        <select id="selectBloco" class="form-select border-0 bg-light py-2" required onchange="carregarAmbientes(this.value)">
                            <option value="">Selecione o bloco...</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase text-muted">Ambiente</label>
                        <select id="selectAmbiente" class="form-select border-0 bg-light py-2" required disabled>
                            <option value="">Selecione o ambiente...</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase text-muted">Tipo de serviço</label>
                        <select id="selectTipoServico" class="form-select border-0 bg-light py-2">
                            <option value="">Selecione o tipo de serviço...</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase text-muted">Prioridade</label>
                        <select id="selectPrioridade" class="form-select border-0 bg-light py-2">
                            <option value="baixa">Baixa</option>
                            <option value="media">Média</option>
                            <option value="alta">Alta</option>
                            <option value="urgente">Urgente</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase text-muted">Técnico Responsável (Opcional)</label>
                        <select id="selectTecnico" class="form-select border-0 bg-light py-2">
                            <option value="">Nenhum técnico atribuído</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small text-uppercase text-muted">Descrição do problema</label>
                        <textarea id="descricaoProblema" class="form-control border-0 bg-light py-2" rows="5" required placeholder="Descreva o problema para o técnico..."></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small text-uppercase text-muted">Anexar Foto (Opcional)</label>
                        <div class="input-group">
                            <input type="file" id="foto" name="foto" class="form-control border-0 bg-light py-2" accept="image/*">
                            <span class="input-group-text border-0 bg-light"><i class="bi bi-camera"></i></span>
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-between align-items-center mt-4">
                        <a href="gestor_chamados.php" class="btn btn-light rounded-pill px-4 fw-bold">Cancelar</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">Abrir Chamado</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    async function carregarOpcoes() {
        try {
            const resBlocos = await fetch('api/localizacoes.php?acao=listar_blocos');
            const blocos = await resBlocos.json();
            const selectBloco = document.getElementById('selectBloco');
            blocos.forEach(b => {
                selectBloco.innerHTML += `<option value="${b.id_bloco}">${b.nome}</option>`;
            });

            const resTipos = await fetch('api/localizacoes.php?acao=listar_tipos');
            const tipos = await resTipos.json();
            const selectTipo = document.getElementById('selectTipoServico');
            tipos.forEach(t => {
                selectTipo.innerHTML += `<option value="${t.id_tipo}">${t.nome}</option>`;
            });

            const resTecnicos = await fetch('api/usuarios.php?perfil=tecnico&ativo=1');
            const tecWrap = await resTecnicos.json();
            const tecnicos = tecWrap.data || [];
            const selectTecnico = document.getElementById('selectTecnico');
            tecnicos.forEach(t => {
                selectTecnico.innerHTML += `<option value="${t.id_usuario}">${t.nome}</option>`;
            });
        } catch (error) {
            console.error('Erro ao carregar opções:', error);
            alert('Erro ao carregar dados do servidor. Tente recarregar a página.');
        }
    }

    async function carregarAmbientes(id_bloco) {
        const selectAmbiente = document.getElementById('selectAmbiente');
        selectAmbiente.innerHTML = '<option value="">Selecione o ambiente...</option>';
        selectAmbiente.disabled = true;

        if (!id_bloco) return;

        try {
            const res = await fetch(`api/localizacoes.php?acao=listar_ambientes&id_bloco=${id_bloco}`);
            const ambientes = await res.json();
            ambientes.forEach(a => {
                selectAmbiente.innerHTML += `<option value="${a.id_ambiente}">${a.nome}</option>`;
            });
            selectAmbiente.disabled = false;
        } catch (error) {
            console.error('Erro ao carregar ambientes:', error);
            alert('Erro ao carregar ambientes.');
        }
    }

    document.getElementById('formAbrirChamado').addEventListener('submit', async (e) => {
        e.preventDefault();

        const payload = {
            id_ambiente: parseInt(document.getElementById('selectAmbiente').value, 10),
            descricao_problema: document.getElementById('descricaoProblema').value,
            prioridade: document.getElementById('selectPrioridade').value
        };

        const tipo = document.getElementById('selectTipoServico').value;
        if (tipo) {
            payload.id_tipo_servico = parseInt(tipo, 10);
        }

        const tecnico = document.getElementById('selectTecnico').value;
        if (tecnico) {
            payload.id_tecnico = parseInt(tecnico, 10);
        }

        const fotoFile = document.getElementById('foto').files[0];
        if (fotoFile) {
            formData.append('foto', fotoFile);
        }


        try {
            const response = await fetch('api/gestor_chamados.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await response.json();
            if (result.success) {
                alert('Chamado aberto com sucesso!');
                window.location.href = 'gestor_chamados.php';
            } else {
                alert('Erro: ' + result.message);
            }
        } catch (error) {
            console.error('Erro ao criar chamado:', error);
            alert('Erro na comunicação com o servidor.');
        }
    });

    carregarOpcoes();
</script>

<?php require_once 'includes/gestor_footer.php'; ?>
