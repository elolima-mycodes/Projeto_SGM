<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] != 'solicitante') {
    header("Location: login.php");
    exit;
}


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abrir Chamado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
        
        <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow border-0 rounded-4">
                    <div class="card-body p-4">
                        <h2 class="fw-bold text-center mb-4">Abrir Chamado</h2>
                        
                        <form id="formChamado">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Bloco</label>
                                <select id="selectBloco" class="form-select" required onchange="carregarAmbientes(this.value)">
                                    <option value="">Selecione o bloco...</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Ambiente</label>
                                <select id="selectAmbiente" class="form-select" required>
                                    <option value="">Selecione o ambiente...</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Tipo de serviço</label>
                                <select id="selectTipo" class="form-select" required>
                                    <option value="">Selecione o tipo...</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Descrição do problema</label>
                                <textarea id="descricao" class="form-control" rows="4" required placeholder="Ex: Lâmpada queimada ou vazamento..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Foto da ocorrência (Opcional)</label>
                                <input type="file" id="foto" name="foto" class="form-control" accept="image/*" multiple>
                                
                            </div>

                            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Registrar solicitação</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
     // Carrega Blocos e Tipos ao iniciar
        async function iniciar() {
            // Blocos
            const resB = await fetch('api/localizacoes.php?acao=listar_blocos');
            const blocos = await resB.json();
            const selB = document.getElementById('selectBloco');
            blocos.forEach(b => selB.innerHTML += `<option value="${b.id_bloco}">${b.nome}</option>`);

            // Tipos
            const resT = await fetch('api/localizacoes.php?acao=listar_tipos');
            const tipos = await resT.json();
            const selT = document.getElementById('selectTipo');
            tipos.forEach(t => selT.innerHTML += `<option value="${t.id_tipo}">${t.nome}</option>`);
        }

        // Carrega Ambientes dinamicamente quando o Bloco muda
        async function carregarAmbientes(id_bloco) {
            const selA = document.getElementById('selectAmbiente');
            if (!id_bloco) { selA.disabled = true; return; }
           
            const res = await fetch(`api/localizacoes.php?acao=listar_ambientes&id_bloco=${id_bloco}`);
            const ambientes = await res.json();
           
            selA.innerHTML = '<option value="">Selecione a Sala...</option>';
            ambientes.forEach(a => selA.innerHTML += `<option value="${a.id_ambiente}">${a.nome}</option>`);
            selA.disabled = false;
        }

document.getElementById('formChamado').addEventListener('submit', async (e) => {
    e.preventDefault();
   
    const formData = new FormData();
    formData.append('id_ambiente', document.getElementById('selectAmbiente').value);
    formData.append('id_tipo', document.getElementById('selectTipo').value);
    formData.append('descricao', document.getElementById('descricao').value);
   
    const fotoFile = document.getElementById('foto').files[0];
    if (fotoFile) {
        formData.append('foto', fotoFile);
    }

    const response = await fetch('api/salvar_chamado.php', {
        method: 'POST',
         body: formData
    });

    const result = await response.json();
    if (result.success) {
        alert(result.message);
        window.location.href = 'solicitante_dashboard.php';
    } else {
        alert("Erro: " + result.message);
    }
});

        iniciar();
</script>
</body>
</html>