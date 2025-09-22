<?php
// handle_form.php

session_start();

// Importa as classes do PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $mensagem = trim($_POST['mensagem']);

    // Validação simples
    if (empty($nome) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($mensagem)) {
        $_SESSION['form_error'] = "Por favor, preencha todos os campos corretamente.";
        header('Location: contato.php');
        exit();
    }

    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 2; // ATIVA O MODO DE DEBUG DETALHADO
    $mail->Timeout   = 10;

    try {
        // --- CONFIGURAÇÕES DO SERVIDOR DE E-MAIL ---
        $mail->isSMTP();
    $mail->Host       = 'smtp.hostinger.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'contato@admplanoa.com';
    $mail->Password   = '@Planoa1232';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // CORRIGIDO: Usar SMTPS para SSL
    $mail->Port       = 465;                         // CORRIGIDO: A porta 465 é para SSL

// --- QUEM ENVIA E QUEM RECEBE ---
// O e-mail em setFrom DEVE ser o mesmo do Username para evitar ser marcado como spam
    $mail->setFrom('contato@admplanoa.com', 'Site Plano A'); 
    $mail->addAddress('contato@admplanoa.com', 'Contato Plano A');
        // --- CONTEÚDO DO E-MAIL ---
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Nova Mensagem do Site - ' . $nome;
        $mail->Body    = "Você recebeu uma nova mensagem do formulário de contato do site:<br><br>" .
                         "<strong>Nome:</strong> " . htmlspecialchars($nome) . "<br>" .
                         "<strong>E-mail:</strong> " . htmlspecialchars($email) . "<br><br>" .
                         "<strong>Mensagem:</strong><br>" . nl2br(htmlspecialchars($mensagem));
        $mail->AltBody = "Nome: {$nome}\nE-mail: {$email}\n\nMensagem:\n{$mensagem}";

        $mail->send();
        $_SESSION['form_message'] = 'Mensagem enviada com sucesso! Agradecemos o seu contato.';
    } catch (Exception $e) {
        $_SESSION['form_error'] = "A mensagem não pôde ser enviada. Mailer Error: {$mail->ErrorInfo}";
    }

    // Redireciona de volta para a página de contato
    header('Location: contato.php');
    exit();
    
} else {
    // Se alguém tentar acessar o arquivo diretamente
    header('Location: index.php');
    exit();
}
?>