<?php
// view_document.php

session_start();
include 'config/db.php';

// 1. SEGURANÇA: Verifica se o administrador está logado.
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403); // Resposta "Forbidden"
    die("Acesso negado. Você precisa ser um administrador.");
}

// 2. PEGA O ID DO DOCUMENTO: Pega o ID da URL (ex: view_document.php?doc_id=5)
$doc_id = isset($_GET['doc_id']) ? (int)$_GET['doc_id'] : 0;
if ($doc_id === 0) {
    http_response_code(400); // Resposta "Bad Request"
    die("ID de documento inválido.");
}

try {
    // 3. BUSCA O CAMINHO DO ARQUIVO NO BANCO DE DADOS
    // Isso garante que estamos buscando um documento que realmente existe no sistema.
    $stmt = $conn->prepare("SELECT file_path, original_filename FROM pending_documents WHERE id = :doc_id");
    $stmt->execute(['doc_id' => $doc_id]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($document && file_exists($document['file_path'])) {
        
        // 4. SERVE O ARQUIVO PARA O NAVEGADOR
        // Pega o tipo do arquivo para o navegador saber o que fazer (mostrar imagem, PDF, etc.)
        $mime_type = mime_content_type($document['file_path']);
        
        // Define os cabeçalhos corretos para o navegador
        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: inline; filename="' . $document['original_filename'] . '"');
        header('Content-Length: ' . filesize($document['file_path']));
        
        // Lê e envia o conteúdo do arquivo
        readfile($document['file_path']);
        exit();

    } else {
        http_response_code(404); // Resposta "Not Found"
        die("Documento não encontrado no servidor.");
    }

} catch (PDOException $e) {
    http_response_code(500); // Resposta "Internal Server Error"
    die("Erro de banco de dados.");
}
?>