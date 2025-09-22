<?php
// review_registration.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include 'config/db.php';

$pending_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($pending_id === 0) {
    header("Location: admin_registrations.php");
    exit();
}

// Lógica para APROVAR ou REJEITAR
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->beginTransaction();
    try {
        $stmt_pending = $conn->prepare("SELECT * FROM pending_registrations WHERE id = ?");
        $stmt_pending->execute([$pending_id]);
        $pending_user = $stmt_pending->fetch();

        if ($pending_user) {
            if (isset($_POST['approve'])) {
                // APROVAR: Insere na tabela 'users'
                $stmt_insert_user = $conn->prepare("INSERT INTO users (full_name, email, cpf, password) VALUES (?, ?, ?, ?)");
                $stmt_insert_user->execute([$pending_user['full_name'], $pending_user['email'], $pending_user['cpf'], $pending_user['password_hash']]);
                $new_user_id = $conn->lastInsertId();

                // Move os documentos
                $stmt_get_docs = $conn->prepare("SELECT * FROM pending_documents WHERE pending_reg_id = ?");
                $stmt_get_docs->execute([$pending_id]);
                $docs_to_move = $stmt_get_docs->fetchAll();
                
                $stmt_insert_doc = $conn->prepare("INSERT INTO user_documents (user_id, document_type, file_path, original_filename, status) VALUES (?, ?, ?, ?, 'Aprovado')");
                foreach ($docs_to_move as $doc) {
                    $stmt_insert_doc->execute([$new_user_id, $doc['document_type'], $doc['file_path'], $doc['original_filename']]);
                }
            }
            // Seja aprovando ou rejeitando, remove o registro pendente
            $stmt_delete = $conn->prepare("DELETE FROM pending_registrations WHERE id = ?");
            $stmt_delete->execute([$pending_id]);
        }
        $conn->commit();
        header("Location: admin_registrations.php");
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        die("Erro ao processar o cadastro: " . $e->getMessage());
    }
}

// Busca os detalhes para exibir na página
try {
    $stmt = $conn->prepare("SELECT * FROM pending_registrations WHERE id = ?");
    $stmt->execute([$pending_id]);
    $registration = $stmt->fetch();

    $stmt_docs = $conn->prepare("SELECT * FROM pending_documents WHERE pending_reg_id = ?");
    $stmt_docs->execute([$pending_id]);
    $documents = $stmt_docs->fetchAll();
} catch (PDOException $e) { die("Erro ao buscar detalhes: " . $e->getMessage()); }

if (!$registration) { die("Cadastro pendente não encontrado."); }
?>
<?php include 'partials/header.php'; ?>
<section>
    <div class="container-section">
        <h2>Analisando Cadastro de: <?= htmlspecialchars($registration['full_name']) ?></h2>
        
        <div class="review-card">
            <h3>Dados Pessoais</h3>
            <div class="data-item"><span class="data-label">Nome:</span><span class="data-value"><?= htmlspecialchars($registration['full_name']) ?></span></div>
            <div class="data-item"><span class="data-label">E-mail:</span><span class="data-value"><?= htmlspecialchars($registration['email']) ?></span></div>
            <div class="data-item"><span class="data-label">CPF:</span><span class="data-value"><?= htmlspecialchars($registration['cpf']) ?></span></div>
        </div>

        <div class="review-card">
            <h3>Documentos Enviados</h3>
            <ul class="document-list">
                <?php foreach ($documents as $doc): ?>
                    <li>
                        <strong><?= htmlspecialchars(str_replace('_', ' ', $doc['document_type'])) ?>:</strong>
                        <a href="view_document.php?doc_id=<?= $doc['id'] ?>" target="_blank" class="btn-download">Ver/Baixar Arquivo</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="action-buttons">
            <form action="review_registration.php?id=<?= $pending_id ?>" method="POST">
                <button type="submit" name="approve" class="btn-approve">Aprovar Cadastro</button>
            </form>
            <form action="review_registration.php?id=<?= $pending_id ?>" method="POST">
                <button type="submit" name="reject" class="btn-reject">Rejeitar Cadastro</button>
            </form>
        </div>
    </div>
</section>
<?php include 'partials/footer.php'; ?>