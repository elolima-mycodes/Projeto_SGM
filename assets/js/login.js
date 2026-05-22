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

        // Previne erro de sintaxe verificando se o servidor retornou HTML em vez de JSON
        if (textoRetorno.trim().startsWith('<')) {
            console.warn("O servidor retornou HTML em vez de JSON (erro no backend).");
            
            let erroAmigavel = "Erro de conexão com o banco de dados no servidor remoto.";
            
            // Analisa se é um erro de acesso ao banco (credenciais erradas no config/database.php do servidor)
            if (textoRetorno.includes("Access denied for user") || textoRetorno.includes("mysqli_sql_exception")) {
                erroAmigavel = "Erro de banco de dados: Acesso negado. Por favor, configure as credenciais corretas do banco de dados no arquivo 'config/database.php' do seu servidor remoto.";
            } else if (textoRetorno.includes("Falha na conexão")) {
                erroAmigavel = "Erro de conexão com o banco de dados. Verifique a configuração do banco de dados no servidor.";
            }
            
            msg.innerText = erroAmigavel;
            btn.disabled = false;
            btn.innerText = 'Entrar no Sistema';
            return;
        }

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
            console.error("Erro ao decodificar JSON:", e);
            msg.innerText = "Erro ao processar resposta do servidor (JSON inválido).";
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