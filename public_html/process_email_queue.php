<?php
// process_email_queue.php
include 'config/db.php';

// Importa as classes do PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

echo "<h1>Processando Fila de E-mails...</h1>";

// Pega até 20 e-mails pendentes para enviar
$stmt = $conn->query("SELECT * FROM email_queue WHERE status = 'Pendente' LIMIT 20");
$emails_to_send = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($emails_to_send)) {
    die("Nenhum e-mail na fila para enviar.");
}

$mail = new PHPMailer(true);
$mail->SMTPDebug = 2; 
// --- COLE SUAS CONFIGURAÇÕES SMTP DA HOSTINGER AQUI ---
$mail->isSMTP();
$mail->Host       = 'smtp.hostinger.com';
$mail->SMTPAuth   = true;
$mail->Username   = 'contato@admplanoa.com';
$mail->Password   = '@Planoa1232';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
$mail->Port       = 465;
$mail->CharSet    = 'UTF-8';
$mail->setFrom('contato@admplanoa.com', 'Equipe da Administradora Plano A');

foreach ($emails_to_send as $email_data) {
    try {
        $mail->clearAddresses(); // Limpa o destinatário anterior
        $mail->addAddress($email_data['recipient_email']);
        $mail->Subject = $email_data['subject'];
        $mail->Body    = $email_data['body'];
        $mail->isHTML(true);

        $mail->send();
        
        // Atualiza o status para 'Enviado'
        $update_stmt = $conn->prepare("UPDATE email_queue SET status = 'Enviado', sent_at = NOW() WHERE id = ?");
        $update_stmt->execute([$email_data['id']]);
        echo "<p>E-mail para ".htmlspecialchars($email_data['recipient_email'])." enviado com sucesso!</p>";

    } catch (Exception $e) {
        // Atualiza o status para 'Falhou'
        $update_stmt = $conn->prepare("UPDATE email_queue SET status = 'Falhou' WHERE id = ?");
        $update_stmt->execute([$email_data['id']]);
        echo "<p style='color:red;'>Falha ao enviar para ".htmlspecialchars($email_data['recipient_email']).": {$mail->ErrorInfo}</p>";
    }
}
echo "<h2>Processamento finalizado.</h2>";
?>