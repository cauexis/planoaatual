<?php 
include 'partials/header.php'; 
include 'config/db.php'; 

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($post_id === 0) { header("Location: blog.php"); exit(); }

try {
    $stmt = $conn->prepare("SELECT title, content, author, created_at, image_url, video_url FROM posts WHERE id = :id");
    $stmt->execute(['id' => $post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) { 
    die("Erro ao carregar o post: " . $e->getMessage()); 
}

if (!$post) { 
    die("Post não encontrado!"); 
}

function getYouTubeEmbedUrl($url) {
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?|shorts)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
    $youtube_id = $match[1] ?? null;
    return $youtube_id ? 'https://www.youtube.com/embed/' . $youtube_id : null;
}
$embed_url = !empty($post['video_url']) ? getYouTubeEmbedUrl($post['video_url']) : null;
?>

<div class="single-post-page">
    
    <div class="post-header">
        <?php if ($embed_url): ?>
            <div class="video-container-full">
                <iframe src="<?= $embed_url ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        <?php elseif (!empty($post['image_url'])): ?>
            <div class="post-image-header" style="background-image: url('<?= htmlspecialchars($post['image_url']) ?>');"></div>
        <?php else: ?>
            <div class="post-image-header-placeholder"></div>
        <?php endif; ?>
    </div>

    <main class="post-main-content">
        <div class="post-card-full">
            <h1><?= htmlspecialchars($post['title']) ?></h1>
            <p class="post-meta">Publicado em: <?= date('d/m/Y', strtotime($post['created_at'])) ?></p>
            
            <div class="post-body">
                <?php
                $allowed_tags = '<p><a><strong><em><u><ul><ol><li><br><h2><h3><h4><h5><h6><img>';
                echo strip_tags($post['content'], $allowed_tags);
                ?>
            </div>
        </div>
    </main>

</div>

<?php include 'partials/footer.php'; ?>