<?php
include 'config/db.php';

// Definir charset para UTF-8 MB4 na conexão com o banco
$conn->exec("SET NAMES utf8mb4");

echo "<h1>Correção de Emojis em Posts Antigos</h1>";

// Selecionar todos os posts antigos (antes da correção de codificação)
$stmt = $conn->query("SELECT id, title, content FROM posts WHERE created_at < '2025-08-22'");

$posts_corrigidos = 0;

while ($row = $stmt->fetch()) {
    echo "<h2>Processando Post: " . htmlspecialchars($row['title']) . "</h2>";
    echo "<p>Conteúdo original: " . htmlspecialchars(substr($row['content'], 0, 200)) . "...</p>";
    
    // Converter conteúdo para a codificação correta
    $conteudo_corrigido = mb_convert_encoding($row['content'], 'UTF-8', 'ISO-8859-1');
    
    // Decodificar entidades HTML
    $conteudo_corrigido = html_entity_decode($conteudo_corrigido, ENT_QUOTES, 'UTF-8');
    
    echo "<p>Conteúdo corrigido: " . htmlspecialchars(substr($conteudo_corrigido, 0, 200)) . "...</p>";
    
    // Atualizar o post no banco de dados
    try {
        $stmt_update = $conn->prepare("UPDATE posts SET content = :conteudo WHERE id = :id");
        $stmt_update->bindParam(':conteudo', $conteudo_corrigido, PDO::PARAM_STR);
        $stmt_update->bindParam(':id', $row['id'], PDO::PARAM_INT);
        $stmt_update->execute();
        
        echo "<p style='color: green;'>✓ Post ID {$row['id']} corrigido com sucesso!</p>";
        $posts_corrigidos++;
    } catch (PDOException $e) {
        echo "<p style='color: red;'>✗ Erro ao corrigir post ID {$row['id']}: " . $e->getMessage() . "</p>";
    }
    
    echo "<hr>";
}

echo "<h2>Resumo da Correção</h2>";
echo "<p>Total de posts corrigidos: $posts_corrigidos</p>";

// Fechar conexão
$conn = null;
?>