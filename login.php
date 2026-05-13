<?php
session_start();
// Se já estiver logado, manda pro dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGM - Login</title>
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/sgm-style.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #4f46e5 0%, #1e293b 100%);
        }
        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 3rem 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .brand-icon {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card text-center">
            <div class="brand-icon">
                <i class="bi bi-shield-check"></i>
            </div>
            <h2 class="fw-bold mb-2">Bem-vindo</h2>
            <p class="text-muted mb-4">Acesse o Sistema de Gestão e Manutenção</p>
            
            <form id="formLogin" class="text-start">
                <div class="mb-3">
                    <label class="form-label fw-semibold">E-mail</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                        <input type="email" id="email" name="email" class="form-control bg-light border-start-0" placeholder="seu@email.com" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Senha</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" id="senha" name="senha" class="form-control bg-light border-start-0" placeholder="••••••••" required>
                    </div>
                </div>
                <button type="submit" id="btnEntrar" class="btn btn-primary w-100 py-3 fw-bold rounded-3">
                    Entrar no Sistema
                </button>
                <div id="mensagem" class="mt-3 text-center text-danger small" style="min-height: 20px;"></div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/login.js?v=<php?= time() ?>"></script>
</body>
</html>
