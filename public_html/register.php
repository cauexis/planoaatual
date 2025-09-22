<?php
// register.php (VERSÃO COM UPLOAD DE DOCUMENTOS)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$message = '';
$error = '';

if (isset($_SESSION['reg_message'])) {
    $message = $_SESSION['reg_message'];
    unset($_SESSION['reg_message']);
}
if (isset($_SESSION['reg_error'])) {
    $error = $_SESSION['reg_error'];
    unset($_SESSION['reg_error']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF--8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Plano A</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-branding-panel">
            <a href="index.php"><img src="img/logo-plano-a.png" alt="Logo Plano A" class="logo"></a>
            <h2>Crie sua Conta</h2>
            <p>Envie seus dados e documentos para análise. É rápido e seguro.</p>
        </div>
        <div class="login-form-panel">
            <div class="form-container">
                <h2>Formulário de Cadastro</h2>
                
                <?php if ($message): ?><div class="form-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
                <?php if ($error): ?><div class="form-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

                <form action="handle_register.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group"><label for="full_name">Nome Completo:</label><input type="text" id="full_name" name="full_name" required></div>
                    <div class="form-group"><label for="email">E-mail:</label><input type="email" id="email" name="email" required></div>
                    <div class="form-group"><label for="cpf">CPF:</label><input type="text" id="cpf" name="cpf" required></div>
                    <div class="form-group"><label for="password">Senha (mínimo 8 caracteres):</label><input type="password" id="password" name="password" required></div>
                    <div class="form-group"><label for="password_confirm">Confirme sua Senha:</label><input type="password" id="password_confirm" name="password_confirm" required></div>
                    <hr style="margin: 20px 0;">
                    <h4>Documentação (PDF, JPG, PNG)</h4>
                    <div class="form-group"><label for="doc_rg">RG ou CNH (frente e verso):</label><input type="file" id="doc_rg" name="doc_rg" required></div>
                    <div class="form-group"><label for="doc_cpf">CPF (se não estiver no RG/CNH):</label><input type="file" id="doc_cpf" name="doc_cpf"></div>
                    <div class="form-group"><label for="doc_residencia">Comprovante de Residência:</label><input type="file" id="doc_residencia" name="doc_residencia" required></div>
                    <button type="submit">Enviar Cadastro para Análise</button>
                </form>
                <p style="text-align: center; margin-top: 20px;">Já tem uma conta? <a href="login.php">Faça o login aqui</a>.</p>
            </div>
        </div>
    </div>
</body>
</html>