<?php 
include 'partials/header.php'; 
include 'config/db.php'; 

// Definir charset para UTF-8 MB4 na conexão com o banco
$conn->exec("SET NAMES utf8mb4");

// Verificar se há erro de conexão
if ($conn->errorCode() != 0) {
    die("Erro de conexão com o banco de dados: " . implode(", ", $conn->errorInfo()));
}
?>

<section>
    <div class="container-section">
        <h2>Informativo Plano A</h2>
        <p>Dicas de saúde, bem-estar e novidades da Plano A.</p>
        
        <div class="blog-list-container">
            <?php
            try {
                $stmt = $conn->query("SELECT id, title, content, created_at, image_url FROM posts ORDER BY created_at DESC");
                
                // Verificar se há resultados
                if ($stmt->rowCount() > 0) {
                    while ($row = $stmt->fetch()) {
                        // Converter conteúdo para a codificação correta (para posts antigos)
                        $conteudo_corrigido = $row['content'];
                        
                        // Verificar se é um post antigo (com problemas de codificação)
                        if (strpos($conteudo_corrigido, '&ecirc;') !== false || 
                            strpos($conteudo_corrigido, '&aacute;') !== false) {
                            // Converter para UTF-8 e decodificar entidades HTML
                            $conteudo_corrigido = mb_convert_encoding($conteudo_corrigido, 'UTF-8', 'ISO-8859-1');
                            $conteudo_corrigido = html_entity_decode($conteudo_corrigido, ENT_QUOTES, 'UTF-8');
                        }
                        
                        // Criar resumo (remover tags HTML e limitar texto) - preservar emojis
                        $resumo = mb_substr(strip_tags($conteudo_corrigido), 0, 150, 'UTF-8') . '...';
                        
                        // Obter URL da imagem (usar padrão se não houver)
                        $imageUrl = !empty($row['image_url']) ? htmlspecialchars($row['image_url']) : 'img/default-post.png';
            ?>
                        <div class="post-card">
                            <a href="post.php?id=<?= $row['id'] ?>">
                                <img class="post-card-image" src="<?= $imageUrl ?>" alt="Imagem de destaque do post <?= htmlspecialchars($row['title']) ?>">
                                <div class="post-card-content">
                                    <h3><?= htmlspecialchars($row['title']) ?></h3>
                                    <p><?= $resumo ?></p>
                                    <span class="btn-leia-mais">Leia Mais</span>
                                </div>
                            </a>
                        </div>
            <?php
                    }
                } else {
                    echo "<p style='text-align: center; padding: 40px;'>Nenhum post encontrado no banco de dados.</p>";
                }
            } catch(PDOException $e) {
                echo "<p style='text-align: center; padding: 40px; color: red;'>Erro ao carregar posts: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>
    </div>
</section>

<?php include 'partials/footer.php'; ?>