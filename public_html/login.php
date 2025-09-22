<?php
// login.php (VERSÃO COM NOVA ESTÉTICA)

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header("Location: area_cliente_dashboard.php");
    exit();
}

include 'config/db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT id, email, password FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            header("Location: area_cliente_dashboard.php");
            exit();
        } else {
            $error = "E-mail ou senha inválidos.";
        }
    } catch(PDOException $e) {
        $error = "Erro no servidor. Tente novamente mais tarde.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Área do Cliente Plano A</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
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
                    <div class="form-error">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <div class="form-group">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" required>
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
            </div>
        </div>
    </div>
</body>
</html>