<?php
// Adicionado para depuração
ini_set('display_errors', 1);
error_reporting(E_ALL);

// admin_user_detail.php
session_start();

// Segurança: Apenas administradores podem acessar
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include 'config/db.php';

// Validação do ID do usuário pego da URL
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($user_id === 0) {
    header("Location: admin_users.php");
    exit();
}

$page_title = 'Detalhes do Beneficiário - Painel Admin';

try {
    // 1. Busca os dados principais do usuário
    $stmt_user = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt_user->execute(['user_id' => $user_id]);
    $user = $stmt_user->fetch(PDO::FETCH_ASSOC);

    // 2. Busca os documentos enviados por este usuário
    $stmt_docs = $conn->prepare("SELECT * FROM user_documents WHERE user_id = :user_id ORDER BY uploaded_at DESC");
    $stmt_docs->execute(['user_id' => $user_id]);
    $documents = $stmt_docs->fetchAll(PDO::FETCH_ASSOC);

    // 3. Busca os boletos gerados para este usuário
    $stmt_boletos = $conn->prepare("SELECT * FROM boletos WHERE user_id = :user_id ORDER BY due_date DESC");
    $stmt_boletos->execute(['user_id' => $user_id]);
    $boletos = $stmt_boletos->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao buscar dados do beneficiário: " . $e->getMessage());
}

if (!$user) {
    die("Beneficiário não encontrado.");
}
?>
<?php include 'partials/header.php'; ?>

<section class="admin-section">
    <div class="container-section">
        <h2>Detalhes de: <?= htmlspecialchars($user['full_name']) ?></h2>
        <a href="admin_users.php">← Voltar para a lista de beneficiários</a>

        <div class="detail-grid">
            <div class="detail-card">
                <h3>Dados Cadastrais</h3>
                <div class="data-item">
                    <span class="data-label">ID do Usuário:</span>
                    <span class="data-value"><?= htmlspecialchars($user['id']) ?></span>
                </div>
                <div class="data-item">
                    <span class="data-label">Nome Completo:</span>
                    <span class="data-value"><?= htmlspecialchars($user['full_name']) ?></span>
                </div>
                <div class="data-item">
                    <span class="data-label">E-mail:</span>
                    <span class="data-value"><?= htmlspecialchars($user['email']) ?></span>
                </div>
                <div class="data-item">
                    <span class="data-label">CPF:</span>
                    <span class="data-value"><?= htmlspecialchars($user['cpf']) ?></span>
                </div>
                <div class="data-item">
                    <span class="data-label">Cliente Desde:</span>
                    <span class="data-value"><?= date('d/m/Y', strtotime($user['created_at'])) ?></span>
                </div>
            </div>

            <div class="detail-card">
                <h3>Documentos Enviados</h3>
                <table class="admin-table">
                    <thead><tr><th>Tipo</th><th>Status</th><th>Ação</th></tr></thead>
                    <tbody>
                        <?php if(empty($documents)): ?>
                            <tr><td colspan="3">Nenhum documento enviado.</td></tr>
                        <?php else: ?>
                            <?php foreach($documents as $doc): ?>
                                <tr>
                                    <td><?= htmlspecialchars(str_replace('_', ' ', $doc['document_type'])) ?></td>
                                    <td><span class="status <?= strtolower($doc['status']) ?>"><?= htmlspecialchars($doc['status']) ?></span></td>
                                    <td><a href="view_document.php?doc_id=<?= $doc['id'] ?>" class="btn-download" target="_blank">Ver</a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="detail-card">
            <h3>Histórico de Faturas (Boletos)</h3>
            <table class="admin-table">
                <thead><tr><th>Vencimento</th><th>Valor (R$)</th><th>Status</th><th>Ação</th></tr></thead>
                <tbody>
                    <?php if(empty($boletos)): ?>
                        <tr><td colspan="4">Nenhum boleto encontrado.</td></tr>
                    <?php else: ?>
                        <?php foreach($boletos as $boleto): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($boleto['due_date'])) ?></td>
                                <td><?= number_format($boleto['amount'], 2, ',', '.') ?></td>
                                <td><span class="status <?= strtolower($boleto['status']) ?>"><?= htmlspecialchars($boleto['status']) ?></span></td>
                                <td>
                                    <?php if(!empty($boleto['pdf_url'])): ?>
                                        <a href="<?= htmlspecialchars($boleto['pdf_url']) ?>" class="btn-download" target="_blank">2ª Via</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php include 'partials/footer.php'; ?>