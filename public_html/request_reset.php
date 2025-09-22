<?php
// request_reset.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'config/db.php';

// Importa as classes do PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    try {
        // Verifica se o e-mail existe no banco de dados
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            // Gera um token seguro e único
            $token = bin2hex(random_bytes(50));
            // Define a data de expiração (ex: 1 hora a partir de agora)
            $expires_at = date("Y-m-d H:i:s", time() + 3600);

            // Salva o token e a data de expiração no registro do usuário
            $update_stmt = $conn->prepare("UPDATE users SET reset_token = :token, reset_token_expires_at = :expires WHERE id = :id");
            $update_stmt->execute(['token' => $token, 'expires' => $expires_at, 'id' => $user['id']]);

            // Envia o e-mail com o link de redefinição
            $reset_link = "http://localhost/planoa/reset_password.php?token=" . $token;

            $mail = new PHPMailer(true);
            // --- COLE SUAS CONFIGURAÇÕES SMTP DA HOSTINGER AQUI ---
            $mail->isSMTP();
            $mail->Host       = 'smtp.hostinger.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'cadastro@admplanoa.com.br'; // SEU E-MAIL
            $mail->Password   = '@Planoa1232'; // SUA SENHA
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom('cadastro@admplanoa.com.br', 'Suporte Plano A');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Redefinição de Senha - Plano A';
            $mail->Body    = "Olá,<br><br>Recebemos uma solicitação de redefinição de senha para sua conta. Clique no link abaixo para criar uma nova senha:<br><br>" .
                             "<a href='{$reset_link}'>{$reset_link}</a><br><br>" .
                             "Se você não solicitou isso, por favor, ignore este e-mail.<br><br>Atenciosamente,<br>Equipe Plano A";
            
            $mail->send();
            $message = "Se o e-mail estiver cadastrado, um link de redefinição de senha foi enviado.";

        } else {
            // Mostra a mesma mensagem de sucesso para não revelar se um e-mail existe ou não (segurança)
            $message = "Se o e-mail estiver cadastrado, um link de redefinição de senha foi enviado.";
        }
    } catch (Exception $e) {
        $error = "Não foi possível enviar o e-mail. Por favor, tente novamente mais tarde.";
    }
}
?>
<?php include 'partials/header.php'; ?>

<section>
    <div class="container-section" style="max-width: 500px;">
        <h2>Redefinir Senha</h2>
        <p>Digite o seu e-mail cadastrado e enviaremos um link para você criar uma nova senha.</p>
        
        <?php if ($message): ?> <div style="padding: 15px; background-color: lightgreen; border-radius: 5px; margin-bottom: 20px;"><?= htmlspecialchars($message) ?></div> <?php endif; ?>
        <?php if ($error): ?> <div style="padding: 15px; background-color: #ffcccb; border-radius: 5px; margin-bottom: 20px;"><?= htmlspecialchars($error) ?></div> <?php endif; ?>

        <form action="request_reset.php" method="POST">
            <label for="email">E-mail:</label>
            <input type="email" name="email" id="email" required>
            <button type="submit">Enviar Link de Redefinição</button>
        </form>
    </div>
</section>

<?php include 'partials/footer.php'; ?>