<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] != 'solicitante') {
    header("Location: login.php");
    exit;
}

$pageTitle = 'SGM - Abrir Chamado';
$activePage = 'abrir_chamado';
$pageHeading = 'Abrir Novo Chamado';
$pageSubheading = 'Descreva o problema e nossa equipe irá atendê-lo.';

require_once 'includes/user_layout.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="content-panel shadow-sm">
            <form id="formChamado">
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

                    <div class="col-12">
                        <label class="form-label fw-bold small text-uppercase text-muted">Tipo de serviço</label>
                        <select id="selectTipo" class="form-select border-0 bg-light py-2" required>
                            <option value="">Selecione o tipo de serviço...</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small text-uppercase text-muted">Descrição do problema</label>
                        <textarea id="descricao" class="form-control border-0 bg-light py-2" rows="4" required placeholder="Descreva aqui o que está acontecendo..."></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small text-uppercase text-muted">Anexar Foto (Opcional)</label>
                        <div class="input-group">
                            <input type="file" id="foto" name="foto" class="form-control border-0 bg-light py-2" accept="image/*">
                            <span class="input-group-text border-0 bg-light"><i class="bi bi-camera"></i></span>
                        </div>
                        <div class="form-text mt-2">Fotos ajudam o técnico a entender melhor o problema.</div>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-3 rounded-pill shadow-sm">
                            <i class="bi bi-send-fill me-2"></i> Registrar Solicitação
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Carrega Blocos e Tipos ao iniciar
    async function iniciar() {
        try {
            // Blocos
            const resB = await fetch('api/localizacoes.php?acao=listar_blocos');
            const blocos = await resB.json();
            const selB = document.getElementById('selectBloco');
            blocos.forEach(b => {
                const opt = document.createElement('option');
                opt.value = b.id_bloco;
                opt.textContent = b.nome;
                selB.appendChild(opt);
            });

            // Tipos
            const resT = await fetch('api/localizacoes.php?acao=listar_tipos');
            const tipos = await resT.json();
            const selT = document.getElementById('selectTipo');
            tipos.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t.id_tipo;
                opt.textContent = t.nome;
                selT.appendChild(opt);
            });
        } catch (err) {
            console.error("Erro ao iniciar:", err);
        }
    }

    // Carrega Ambientes dinamicamente quando o Bloco muda
    async function carregarAmbientes(id_bloco) {
        const selA = document.getElementById('selectAmbiente');
        selA.innerHTML = '<option value="">Selecione o ambiente...</option>';
        
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

    document.getElementById('formChamado').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const btn = e.target.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
       
        const formData = new FormData();
        formData.append('id_ambiente', document.getElementById('selectAmbiente').value);
        formData.append('id_tipo', document.getElementById('selectTipo').value);
        formData.append('descricao', document.getElementById('descricao').value);
       
        const fotoFile = document.getElementById('foto').files[0];
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
                window.location.href = 'solicitante_dashboard.php';
            } else {
                alert("Erro: " + result.message);
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        } catch (err) {
            console.error("Erro ao salvar:", err);
            alert("Ocorreu um erro ao enviar sua solicitação.");
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    iniciar();
</script>

<?php require_once 'includes/user_footer.php'; ?>