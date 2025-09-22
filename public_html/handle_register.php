<?php
// handle_register.php
session_start();
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta dados do formulário
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $cpf = trim($_POST['cpf']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // Validações
    if (strlen($password) < 8) {
        $_SESSION['reg_error'] = "A senha deve ter no mínimo 8 caracteres.";
        header('Location: register.php'); exit();
    }
    if ($password !== $password_confirm) {
        $_SESSION['reg_error'] = "As senhas não coincidem.";
        header('Location: register.php'); exit();
    }
    if (empty($_FILES['doc_rg']) || $_FILES['doc_rg']['error'] != 0 || empty($_FILES['doc_residencia']) || $_FILES['doc_residencia']['error'] != 0) {
        $_SESSION['reg_error'] = "RG/CNH e Comprovante de Residência são obrigatórios.";
        header('Location: register.php'); exit();
    }

    // Função auxiliar para salvar arquivos
    function save_file($file, $doc_type, $user_cpf) {
        $upload_dir = 'uploads/documents/';
        $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
        if ($file['error'] != 0 || !in_array($file['type'], $allowed_types) || $file['size'] > 5 * 1024 * 1024) {
            return false; // Falha na validação
        }
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $safe_filename = "reg_{$user_cpf}_{$doc_type}_" . uniqid('', true) . '.' . $file_extension;
        $target_path = $upload_dir . $safe_filename;
        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            return ['path' => $target_path, 'name' => basename($file['name'])];
        }
        return false;
    }

    $conn->beginTransaction();
    try {
        // Verifica se usuário ou CPF já existem em qualquer uma das tabelas
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = :email OR cpf = :cpf UNION SELECT id FROM pending_registrations WHERE email = :email OR cpf = :cpf");
        $stmt_check->execute(['email' => $email, 'cpf' => $cpf]);
        if ($stmt_check->fetch()) {
            throw new Exception("E-mail ou CPF já cadastrado em nosso sistema.");
        }

        // Salva os dados do usuário na tabela de pendentes
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt_reg = $conn->prepare("INSERT INTO pending_registrations (full_name, email, cpf, password_hash) VALUES (:name, :email, :cpf, :hash)");
        $stmt_reg->execute(['name' => $full_name, 'email' => $email, 'cpf' => $cpf, 'hash' => $hashed_password]);
        $pending_reg_id = $conn->lastInsertId();

        // Salva os arquivos
        $stmt_doc = $conn->prepare("INSERT INTO pending_documents (pending_reg_id, document_type, file_path, original_filename) VALUES (?, ?, ?, ?)");

        $rg_file = save_file($_FILES['doc_rg'], 'RG_CNH', $cpf);
        if (!$rg_file) throw new Exception("Falha ao salvar o arquivo de RG/CNH.");
        $stmt_doc->execute([$pending_reg_id, 'RG_CNH', $rg_file['path'], $rg_file['name']]);
        
        $residencia_file = save_file($_FILES['doc_residencia'], 'Residencia', $cpf);
        if (!$residencia_file) throw new Exception("Falha ao salvar o Comprovante de Residência.");
        $stmt_doc->execute([$pending_reg_id, 'Comprovante_Residencia', $residencia_file['path'], $residencia_file['name']]);
        
        // Salva CPF se foi enviado
        if (!empty($_FILES['doc_cpf']) && $_FILES['doc_cpf']['error'] == 0) {
            $cpf_file = save_file($_FILES['doc_cpf'], 'CPF', $cpf);
            if (!$cpf_file) throw new Exception("Falha ao salvar o arquivo de CPF.");
            $stmt_doc->execute([$pending_reg_id, 'CPF', $cpf_file['path'], $cpf_file['name']]);
        }

        $conn->commit();
        $_SESSION['reg_message'] = "Cadastro enviado com sucesso! Seus dados e documentos estão em análise. Você será notificado por e-mail quando sua conta for aprovada.";
        header('Location: register.php');
        exit();

    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['reg_error'] = $e->getMessage();
        header('Location: register.php');
        exit();
    }
}
?>