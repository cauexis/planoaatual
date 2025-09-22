<?php
// enviar_email_beneficiario.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Inclua a conexão com o banco de dados e as classes do PHPMailer
require 'config/db.php'; // Seu arquivo de conexão com o banco de dados
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

// Esta função recebe o ID do usuário e envia o e-mail
function enviarEmailBeneficiario($conn, $userId) {
    try {
        // --- BUSCA OS DADOS DO USUÁRIO NO BANCO DE DADOS ---
        // A sua tabela 'users' precisa ter a coluna 'plano_id' para que esta consulta funcione.
        $sql = "SELECT u.full_name, u.email, p.nome_plano 
                FROM users u
                LEFT JOIN planos p ON u.plano_id = p.id
                WHERE u.id = :userId";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $beneficiario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$beneficiario) {
            return "Erro: Beneficiário não encontrado.";
        }

        // --- CONFIGURAÇÃO DO PHPMailer ---
        $mail = new PHPMailer(true);
        $mail->isSMTP();
$mail->Host       = 'smtp.hostinger.com';
$mail->SMTPAuth   = true;
$mail->Username   = 'contato@admplanoa.com';
$mail->Password   = '@Planoa1232';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
$mail->Port       = 465;
$mail->CharSet    = 'UTF-8';
$mail->setFrom('contato@admplanoa.com', 'Equipe da Administradora Plano A');

        // --- REMETENTE E DESTINATÁRIO ---
        $mail->setFrom('contato@planmedsolucoes.com.br', 'Planmed');
        $mail->addAddress($beneficiario['email'], $beneficiario['full_name']);

        // --- CONTEÚDO DO E-MAIL ---
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Detalhes do seu Plano de Saúde';
        $mail->Body    = "
            <h1>Olá, " . htmlspecialchars($beneficiario['full_name']) . "!</h1>
            <p>Seu plano de saúde <b>" . htmlspecialchars($beneficiario['nome_plano']) . "</b> foi confirmado.</p>
            <p>Em anexo, você encontrará informações importantes sobre sua cobertura.</p>
            <p>Atenciosamente,<br>Equipe Plano A</p>
        ";
        $mail->AltBody = "Olá, " . htmlspecialchars($beneficiario['full_name']) . "!\nSeu plano de saúde " . htmlspecialchars($beneficiario['nome_plano']) . " foi confirmado.";

        $mail->send();
        return "E-mail enviado com sucesso para " . htmlspecialchars($beneficiario['full_name']) . ".";
    } catch (Exception $e) {
        return "Erro ao enviar e-mail. Mailer Error: {$mail->ErrorInfo}";
    }
}