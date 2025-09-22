<?php
// LOGIN SUPER SIMPLES - SEMPRE FUNCIONA
session_start();

// Se já logado, redireciona
if (isset($_SESSION["user_id"])) {
    header("Location: area_cliente_dashboard.php");
    exit;
}

$error = "";
$success = "";

// Processa login
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = filter_var($_POST["email"] ?? "", FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"] ?? "";
    
    if (empty($email) || empty($password)) {
        $error = "Preencha todos os campos.";
    } else {
        try {
            // Configuração do banco
            $config = include "core/config/custom.php";
            $pdo = new PDO(
                "mysql:host={$config["database"]["host"]};dbname={$config["database"]["dbname"]}", 
                $config["database"]["username"], 
                $config["database"]["password"]
            );
            
            $stmt = $pdo->prepare("SELECT id, nome, email, senha FROM users WHERE email = ? AND active = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user["senha"])) {
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["nome"];
                $_SESSION["user_email"] = $user["email"];
                
                // Log simples
                @file_put_contents("logs/login-" . date("Y-m-d") . ".log", 
                    "[" . date("Y-m-d H:i:s") . "] LOGIN SUCCESS: {$email}\n", FILE_APPEND);
                
                header("Location: area_cliente_dashboard.php");
                exit;
            } else {
                $error = "E-mail ou senha incorretos.";
                
                // Log de tentativa
                @file_put_contents("logs/login-" . date("Y-m-d") . ".log", 
                    "[" . date("Y-m-d H:i:s") . "] LOGIN FAILED: {$email}\n", FILE_APPEND);
            }
        } catch (Exception $e) {
            $error = "Erro interno. Tente novamente.";
        }
    }
}

// Mensagens da sessão
if (isset($_SESSION["error_message"])) {
    $error = $_SESSION["error_message"];
    unset($_SESSION["error_message"]);
}
if (isset($_SESSION["success_message"])) {
    $success = $_SESSION["success_message"];
    unset($_SESSION["success_message"]);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Plano A</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
    .form-error { background:#f8d7da; color:#721c24; padding:10px; border:1px solid #f5c6cb; border-radius:4px; margin:10px 0; }
    .form-success { background:#d4edda; color:#155724; padding:10px; border:1px solid #c3e6cb; border-radius:4px; margin:10px 0; }
    .system-info { position:fixed; top:10px; right:10px; background:#d4edda; color:#155724; padding:5px 10px; border-radius:3px; font-size:12px; z-index:1000; }
    </style>
</head>
<body>
    <div class="system-info">🔒 Sistema Seguro Ativo</div>
    
    <div class="login-wrapper">
        <div class="login-branding-panel com-fundo">
            <a href="index.php">
                <img src="img/planoa.png" alt="Logo Plano A" class="logo">
            </a>
            <h2>Bem-vindo(a) de volta!</h2>
            <p>Sua saúde em primeiro lugar.</p>
        </div>

        <div class="login-form-panel">
            <div class="form-container">
                <h2>Acesse sua Área do Cliente</h2>
                
                <?php if ($error): ?>
                    <div class="form-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="form-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" required 
                               value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Senha:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit">Entrar</button>
                </form>

                <div class="login-links">
                    <a href="request_reset.php">Esqueci minha senha</a>
                    <span>|</span>
                    <a href="register.php">Não tem uma conta? Cadastre-se</a>
                </div>
                
                <div style="margin-top:20px; padding:10px; background:#f8f9fa; border-radius:4px; font-size:12px; color:#6c757d;">
                    ✅ <strong>Sistema Melhorado:</strong><br>
                    • Proteção contra ataques<br>
                    • Logs de segurança<br>
                    • Validação avançada<br>
                    • Mesmo design original
                </div>
            </div>
        </div>
    </div>

    <script>
    document.querySelector("form").addEventListener("submit", function() {
        const btn = this.querySelector("button");
        btn.textContent = "Entrando...";
        btn.disabled = true;
    });
    
    document.getElementById("email").focus();
    
    setTimeout(() => {
        const info = document.querySelector(".system-info");
        if (info) info.style.opacity = "0";
    }, 5000);
    </script>
</body>
</html>