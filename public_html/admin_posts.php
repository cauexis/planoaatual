<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include 'config/db.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content']; // Agora virá com HTML
    $author = $_POST['author'];
    $video_url = trim($_POST['video_url']);
    $image_path = null;

    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] == 0) {
        $upload_dir = 'uploads/';
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024;
        if (in_array($_FILES['post_image']['type'], $allowed_types) && $_FILES['post_image']['size'] <= $max_size) {
            $file_extension = pathinfo($_FILES['post_image']['name'], PATHINFO_EXTENSION);
            $unique_filename = uniqid('post_', true) . '.' . $file_extension;
            $target_path = $upload_dir . $unique_filename;
            if (move_uploaded_file($_FILES['post_image']['tmp_name'], $target_path)) {
                $image_path = $target_path;
            } else { $message = "Erro ao mover o ficheiro de imagem."; }
        } else { $message = "Erro: Tipo de ficheiro inválido ou imagem muito grande (máx 5MB)."; }
    }

    if (empty($message)) {
        try {
            $stmt = $conn->prepare("INSERT INTO posts (title, content, image_url, video_url, author) VALUES (:title, :content, :image_url, :video_url, :author)");
            $stmt->execute(['title' => $title, 'content' => $content, 'image_url' => $image_path, 'video_url' => $video_url, 'author' => $author]);
            $message = "Post criado com sucesso!";
        } catch(PDOException $e) { $message = "Erro ao criar post: " . $e->getMessage(); }
    }
}
?>
<?php include 'partials/header.php'; ?>

<script src="https://cdn.tiny.cloud/1/i22j3kqhyehxnly3ewkudg5z6b4e15zh3rtb57hh5v0no55o/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  // 2. Inicializa o editor na sua caixa de texto
  tinymce.init({
    selector: '#content',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
  });
</script>

<section class="admin-section">
    <div class="container-section">
        <h2>Adicionar Novo Post</h2>
        <?php if (!empty($message)): ?>
            <div class="form-success"><?= $message ?></div>
        <?php endif; ?>
        <form action="admin_posts.php" method="POST" enctype="multipart/form-data" class="admin-form">
            <fieldset>
                <legend>Informações do Post</legend>
                <div class="form-group"><label for="title">Título:</label><input type="text" id="title" name="title" required></div>
                <div class="form-group"><label for="content">Conteúdo:</label><textarea id="content" name="content" rows="20"></textarea></div>
                <div class="form-group"><label for="author">Autor:</label><input type="text" id="author" name="author" required></div>
            </fieldset>
            <fieldset>
                <legend>Mídia</legend>
                <div class="form-group"><label for="post_image">Imagem de Destaque (opcional):</label><input type="file" id="post_image" name="post_image"></div>
                <div class="form-group"><label for="video_url">Link do Vídeo do YouTube (Opcional):</label><input type="text" id="video_url" name="video_url" placeholder="https://www.youtube.com/watch?v=..."></div>
            </fieldset>
            <button type="submit">Salvar Post</button>
        </form>
    </div>
</section>

<?php include 'partials/footer.php'; ?>