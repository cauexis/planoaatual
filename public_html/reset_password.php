<?php
// reset_password.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'config/db.php';

$error = '';
$message = '';
$token_valid = false;

// 1. Validação do Token (quando a página carrega)
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    try {
        $stmt = $conn->prepare("SELECT id, reset_token_expires_at FROM users WHERE reset_token = :token");
        $stmt->execute(['token' => $token]);
        $user = $stmt->fetch();

        // Verifica se o token existe e não expirou
        if ($user && strtotime($user['reset_token_expires_at']) > time()) {
            $token_valid = true;
            $_SESSION['reset_user_id'] = $user['id']; // Salva o ID do usuário na sessão para o próximo passo
        } else {
            $error = "Token inválido ou expirado. Por favor, solicite a redefinição novamente.";
        }
    } catch (Exception $e) {
        $error = "Erro no servidor. Tente novamente.";
    }
} else {
    $error = "Nenhum token fornecido.";
}

// 2. Processamento do Formulário de Nova Senha
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['reset_user_id'])) {
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (strlen($password) < 8) {
        $error = "A nova senha deve ter no mínimo 8 caracteres.";
    } elseif ($password !== $password_confirm) {
        $error = "As senhas não coincidem.";
    } else {
        try {
            $user_id = $_SESSION['reset_user_id'];
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Atualiza a senha e anula o token para não ser usado novamente
            $stmt = $conn->prepare("UPDATE users SET password = :password, reset_token = NULL, reset_token_expires_at = NULL WHERE id = :id");
            $stmt->execute(['password' => $hashed_password, 'id' => $user_id]);

            unset($_SESSION['reset_user_id']); // Limpa a sessão
            $message = "Senha redefinida com sucesso! Você já pode fazer o login com sua nova senha.";
            $token_valid = false; // Esconde o formulário após o sucesso
        } catch (Exception $e) {
            $error = "Não foi possível redefinir a senha. Tente novamente.";
        }
    }
}
?>
<?php include 'partials/header.php'; ?>

<section>
    <div class="container-section" style="max-width: 500px;">
        <h2>Crie sua Nova Senha</h2>

        <?php if ($message): ?> <div style="padding: 15px; background-color: lightgreen; border-radius: 5px; margin-bottom: 20px;"><?= htmlspecialchars($message) ?><br><a href='login.php'>Ir para o Login</a></div> <?php endif; ?>
        <?php if ($error): ?> <div style="padding: 15px; background-color: #ffcccb; border-radius: 5px; margin-bottom: 20px;"><?= htmlspecialchars($error) ?></div> <?php endif; ?>

        <?php if ($token_valid): ?>
            <form action="reset_password.php?token=<?= htmlspecialchars($token) ?>" method="POST">
                <label for="password">Nova Senha (mínimo 8 caracteres):</label>
                <input type="password" name="password" id="password" required>
                
                <label for="password_confirm">Confirme a Nova Senha:</label>
                <input type="password" name="password_confirm" id="password_confirm" required>

                <button type="submit">Redefinir Senha</button>
            </form>
        <?php endif; ?>
    </div>
</section>

<?php include 'partials/footer.php'; ?>