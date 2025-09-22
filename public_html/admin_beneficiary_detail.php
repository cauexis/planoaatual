<?php
// admin_beneficiary_detail.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include 'config/db.php';

$beneficiary_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($beneficiary_id === 0) {
    header("Location: admin_beneficiaries.php");
    exit();
}

$page_title = 'Ficha do Beneficiário - Painel Admin';

try {
    $stmt = $conn->prepare("SELECT * FROM beneficiaries WHERE id = :id");
    $stmt->execute(['id' => $beneficiary_id]);
    $beneficiary = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar dados do beneficiário: " . $e->getMessage());
}

if (!$beneficiary) {
    die("Beneficiário não encontrado.");
}
?>
<?php include 'partials/header.php'; ?>

<section class="admin-section">
    <div class="container-section">
        <h2>Ficha de: <?= htmlspecialchars($beneficiary['nome_associado']) ?></h2>
        <a href="admin_beneficiaries.php">← Voltar para a lista</a>

        <div class="detail-grid">
            <div class="detail-card">
                <h3>Dados Pessoais e Contrato</h3>
                <div class="data-item">
                    <span class="data-label">Nome Completo:</span>
                    <span class="data-value"><?= htmlspecialchars($beneficiary['nome_associado']) ?></span>
                </div>
                <div class="data-item">
                    <span class="data-label">Data de Nascimento:</span>
                    <span class="data-value"><?= !empty($beneficiary['data_nascimento']) ? date('d/m/Y', strtotime($beneficiary['data_nascimento'])) : 'Não informado' ?></span>
                </div>
                <div class="data-item">
                    <span class="data-label">E-mail:</span>
                    <span class="data-value"><?= htmlspecialchars($beneficiary['endereco_email']) ?></span>
                </div>
                <div class="data-item">
                    <span class="data-label">Cód. Plano:</span>
                    <span class="data-value"><?= htmlspecialchars($beneficiary['codigo_plano']) ?></span>
                </div>
                 <div class="data-item">
                    <span class="data-label">Cód. Grupo de Contrato:</span>
                    <span class="data-value"><?= htmlspecialchars($beneficiary['codigo_grupo_contrato']) ?></span>
                </div>
            </div>

            <div class="detail-card">
                <h3>Endereço</h3>
                <div class="data-item">
                    <span class="data-label">Bairro:</span>
                    <span class="data-value"><?= htmlspecialchars($beneficiary['bairro']) ?></span>
                </div>
                 <div class="data-item">
                    <span class="data-label">Cidade:</span>
                    <span class="data-value"><?= htmlspecialchars($beneficiary['cidade']) ?></span>
                </div>
                 <div class="data-item">
                    <span class="data-label">Estado:</span>
                    <span class="data-value"><?= htmlspecialchars($beneficiary['estado']) ?></span>
                </div>
                 <div class="data-item">
                    <span class="data-label">CEP:</span>
                    <span class="data-value"><?= htmlspecialchars($beneficiary['cep']) ?></span>
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <a href="admin_compose_email.php?single_email=<?= urlencode($beneficiary['endereco_email']) ?>" class="btn-approve">Notificar Beneficiário</a>
        </div>
    </div>
</section>

<?php include 'partials/footer.php'; ?>