<?php
// admin_compose_email.php (VERSÃO FINAL COM LOG DE ERROS)
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: admin_login.php"); exit(); }
include 'config/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$message = '';
$error = '';
$detailed_log = '';

if (isset($_POST['send_campaign'])) {
    $subject = trim($_POST['subject']);
    $body_template = $_POST['body'];
    // ... (Lógica para buscar destinatários continua a mesma) ...
    $recipients = [];
    if ($_POST['recipient_type'] == 'single' && !empty($_POST['single_email'])) {
        $recipients[] = ['email' => trim($_POST['single_email']), 'full_name' => 'Cliente'];
    } elseif ($_POST['recipient_type'] == 'list' && !empty($_POST['list_id'])) {
        $stmt = $conn->prepare("SELECT email, full_name FROM email_contacts WHERE list_id = ?");
        $stmt->execute([$_POST['list_id']]);
        $recipients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    if (!empty($recipients)) {
        $queue_stmt = $conn->prepare("INSERT INTO email_queue (recipient_email, recipient_name, subject, body, scheduled_for) VALUES (?, ?, ?, ?, NOW())");
        foreach ($recipients as $recipient) {
            $queue_stmt->execute([$recipient['email'], $recipient['full_name'] ?? 'Cliente', $subject, $body_template]);
        }
        $message = count($recipients) . " e-mail(s) adicionado(s) à fila. Iniciando envio...<br><br>";

        // --- Processa a fila imediatamente ---
        $emails_to_send_stmt = $conn->query("SELECT * FROM email_queue WHERE status = 'Pendente' LIMIT 100");
        $emails_to_send = $emails_to_send_stmt->fetchAll(PDO::FETCH_ASSOC);

        $mail = new PHPMailer(true);
        // --- SUAS CONFIGURAÇÕES SMTP ---
        $mail->isSMTP();
       $mail->isSMTP();
$mail->Host       = 'smtp.hostinger.com';
$mail->SMTPAuth   = true;
$mail->Username   = 'contato@admplanoa.com';
$mail->Password   = '@Planoa1232';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
$mail->Port       = 465;
$mail->CharSet    = 'UTF-8';
$mail->setFrom('contato@admplanoa.com', 'Equipe da Administradora Plano A');

        $success_count = 0;
        $fail_count = 0;

        foreach ($emails_to_send as $email_data) {
            try {
                // ... (lógica de personalização e envio) ...
                $personalized_body = str_replace('[Nome do Cliente]', htmlspecialchars($email_data['recipient_name']), $email_data['body']);
                $mail->clearAddresses();
                $mail->addAddress($email_data['recipient_email']);
                $mail->Subject = $email_data['subject'];
                $mail->Body    = $personalized_body;
                $mail->isHTML(true);
                $mail->send();
                
                $update_stmt = $conn->prepare("UPDATE email_queue SET status = 'Enviado', sent_at = NOW() WHERE id = ?");
                $update_stmt->execute([$email_data['id']]);
                $success_count++;

            } catch (Exception $e) {
                // ATUALIZAÇÃO IMPORTANTE: Guarda a mensagem de erro no banco
                $error_info = $mail->ErrorInfo;
                $update_stmt = $conn->prepare("UPDATE email_queue SET status = 'Falhou', error_message = ? WHERE id = ?");
                $update_stmt->execute([$error_info, $email_data['id']]);
                $fail_count++;
                $detailed_log .= "Falha ao enviar para " . htmlspecialchars($email_data['recipient_email']) . " - Erro: " . $error_info . "<br>";
            }
            sleep(5); // Mantém a pausa de 1 segundo
        }
        $message .= "Processamento finalizado. Enviados: {$success_count}. Falhas: {$fail_count}.";

    } else {
        $error = "Nenhum destinatário foi selecionado.";
    }
}

// Busca as listas para o formulário
$lists_stmt = $conn->query("SELECT * FROM email_lists ORDER BY list_name");
$lists = $lists_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'partials/header.php'; ?>
<section class="admin-section">
    <div class="container-section">
        <h2>Escrever e Enviar E-mail</h2>
        <?php if($message): ?> <div class="form-success"><?= $message ?></div> <?php endif; ?>
        <?php if($error): ?> <div class="form-error"><?= $error ?></div> <?php endif; ?>

        <form action="admin_compose_email.php" method="POST" class="admin-form">
            <fieldset>
                <legend>Destinatários</legend>

                <?php if (!empty($pre_selected_emails)): ?>
                    <div class="form-group">
                        <input type="radio" name="recipient_type" value="pre_selected" id="rec_pre_selected" checked>
                        <label for="rec_pre_selected">Enviar para os <strong><?= count($pre_selected_emails) ?></strong> beneficiários selecionados:</label>
                        <textarea name="pre_selected_recipients" rows="4" readonly><?= htmlspecialchars(implode(', ', $pre_selected_emails)) ?></textarea>
                    </div>
                    <hr>
                <?php endif; ?>
                
                <div class="form-group">
                    <input type="radio" name="recipient_type" value="list" id="rec_list" <?= empty($pre_selected_emails) ? 'checked' : '' ?>>
                    <label for="rec_list">Enviar para uma lista de e-mails:</label>
                    <select name="list_id">
                        <option value="">-- Selecione uma lista --</option>
                        <?php foreach($lists as $list): ?>
                            <option value="<?= $list['id'] ?>"><?= htmlspecialchars($list['list_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </fieldset>

            <fieldset>
                <legend>Conteúdo</legend>
                <label for="subject">Assunto:</label>
                <input type="text" id="subject" name="subject" required>
                <label for="body">Corpo do E-mail (use [Nome do Cliente] para personalizar):</label>
                <textarea id="body" name="body" rows="15" required></textarea>
            </fieldset>

            <button type="submit" name="send_campaign">Enviar Campanha Agora</button>
        </form>
    </div>
</section>
<?php include 'partials/footer.php'; ?>