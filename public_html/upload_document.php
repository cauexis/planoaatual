<?php
// upload_document.php
session_start();
include 'config/db.php';

// 1. VERIFICA SE O USUÁRIO ESTÁ LOGADO
if (!isset($_SESSION['user_id'])) {
    die("Acesso negado. Você precisa estar logado.");
}
$user_id = $_SESSION['user_id'];

// 2. VERIFICA SE O FORMULÁRIO FOI ENVIADO CORRETAMENTE
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['document_file'])) {
    header('Location: dashboard.php');
    exit();
}

$document_type = $_POST['document_type'];
$file = $_FILES['document_file'];

// 3. VALIDAÇÃO DE SEGURANÇA DO ARQUIVO
$upload_dir = 'uploads/documents/';
$allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
$max_size = 5 * 1024 * 1024; // 5 MB

if ($file['error'] !== UPLOAD_ERR_OK) {
    die("Erro no upload do arquivo. Código: " . $file['error']);
}
if (!in_array($file['type'], $allowed_types)) {
    die("Erro: Tipo de arquivo não permitido. Apenas JPG, PNG e PDF.");
}
if ($file['size'] > $max_size) {
    die("Erro: Arquivo muito grande. O tamanho máximo é de 5MB.");
}

// 4. CRIA UM NOME DE ARQUIVO SEGURO E ÚNICO
$original_filename = basename($file['name']);
$file_extension = pathinfo($original_filename, PATHINFO_EXTENSION);
$safe_filename = "user_{$user_id}_" . uniqid('', true) . '.' . $file_extension;
$target_path = $upload_dir . $safe_filename;

// 5. MOVE O ARQUIVO PARA A PASTA DE DESTINO
if (move_uploaded_file($file['tmp_name'], $target_path)) {
    
    // 6. SALVA A REFERÊNCIA NO BANCO DE DADOS
    try {
        $stmt = $conn->prepare("INSERT INTO user_documents (user_id, document_type, file_path, original_filename) VALUES (:user_id, :doc_type, :file_path, :orig_name)");
        $stmt->execute([
            'user_id' => $user_id,
            'doc_type' => $document_type,
            'file_path' => $target_path,
            'orig_name' => $original_filename
        ]);
        
        // Redireciona de volta para o dashboard com mensagem de sucesso (opcional)
        header('Location: dashboard.php?upload=success');
        exit();

    } catch (PDOException $e) {
        // Se falhar ao salvar no banco, apaga o arquivo para não deixar lixo
        unlink($target_path); 
        die("Erro de banco de dados: " . $e->getMessage());
    }

} else {
    die("Erro crítico: Não foi possível mover o arquivo enviado.");
}
?>