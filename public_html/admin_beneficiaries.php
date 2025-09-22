<?php
// admin_beneficiaries.php (VERSÃO CORRIGIDA E COMPLETA)
session_start();

// Segurança: Apenas administradores podem aceder a esta página
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include 'config/db.php';

$page_title = 'Gestão de Beneficiários - Painel Admin';
$beneficiaries = []; // Inicializa a variável

try {
    // Busca todos os beneficiários da tabela 'beneficiaries'
    $stmt = $conn->query("SELECT id, nome_associado, endereco_email, cidade, estado, codigo_plano FROM beneficiaries ORDER BY nome_associado ASC");
    $beneficiaries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Se houver um erro, a página não quebra, apenas mostra a lista vazia
    // Em um ambiente de produção, logaríamos o erro: error_log($e->getMessage());
}
?>
<?php include 'partials/header.php'; ?>

<section class="admin-section">
    <div class="container-section">
        <h2>Gestão de Vidas (Beneficiários)</h2>
        <p>Abaixo está a lista de todos os beneficiários importados para o sistema.</p>

        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nome do Associado</th>
                        <th>E-mail</th>
                        <th>Cidade</th>
                        <th>Plano (Cód)</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($beneficiaries)): ?>
                        <tr>
                            <td colspan="5">Nenhum beneficiário encontrado. Verifique se a importação foi concluída.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($beneficiaries as $beneficiary): ?>
                            <tr>
                                <td><?= htmlspecialchars($beneficiary['nome_associado']) ?></td>
                                <td><?= htmlspecialchars($beneficiary['endereco_email']) ?></td>
                                <td><?= htmlspecialchars($beneficiary['cidade']) ?> - <?= htmlspecialchars($beneficiary['estado']) ?></td>
                                <td><?= htmlspecialchars($beneficiary['codigo_plano']) ?></td>
                                <td>
                                    <a href="admin_beneficiary_detail.php?id=<?= $beneficiary['id'] ?>" class="btn-action view">Ver Ficha</a>
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