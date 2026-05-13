document.getElementById('formLogin').addEventListener('submit', async (e) => {
    e.preventDefault();
    console.log("Formulário enviado");
    
    const email = document.getElementById('email').value;
    const senha = document.getElementById('senha').value;
    const msg = document.getElementById('mensagem');
    const btn = document.getElementById('btnEntrar');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Acessando...';
    msg.innerText = "";

    try {
        console.log("Enviando requisição para api/login.php...");
        const response = await fetch('api/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: email, senha: senha })
        });

        const textoRetorno = await response.text();
        console.log("Resposta bruta do servidor:", textoRetorno);
        
        try {
            const result = JSON.parse(textoRetorno);
            if (result.success) {
                console.log("Login bem-sucedido, redirecionando...");
                window.location.href = 'dashboard.php';
            } else {
                msg.innerText = result.message;
                btn.disabled = false;
                btn.innerText = 'Entrar no Sistema';
            }
        } catch (e) {
            console.error("Erro ao processar JSON:", e);
            msg.innerText = "Erro interno no servidor (JSON inválido).";
            btn.disabled = false;
            btn.innerText = 'Entrar no Sistema';
        }
    } catch (error) {
        console.error("Erro na requisição fetch:", error);
        msg.innerText = "Erro ao conectar com o servidor.";
        btn.disabled = false;
        btn.innerText = 'Entrar no Sistema';
    }
});