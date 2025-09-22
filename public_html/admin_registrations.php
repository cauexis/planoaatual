<?php
// admin_registrations.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include 'config/db.php';

try {
    $stmt = $conn->query("SELECT id, full_name, email, cpf, submitted_at FROM pending_registrations WHERE status = 'Pendente' ORDER BY submitted_at ASC");
    $pending_registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar cadastros pendentes: " . $e->getMessage());
}
?>
<?php include 'partials/header.php'; ?>

<section>
    <div class="container-section">
        <h2>Análise de Cadastros Pendentes</h2>
        <p>Abaixo estão os novos cadastros aguardando sua aprovação.</p>

        <table class="boletos-table" style="margin-top: 30px;">
            <thead>
                <tr>
                    <th>Nome Completo</th>
                    <th>E-mail</th>
                    <th>CPF</th>
                    <th>Data de Envio</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pending_registrations)): ?>
                    <tr>
                        <td colspan="5">Nenhum cadastro pendente no momento.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pending_registrations as $reg): ?>
                        <tr>
                            <td><?= htmlspecialchars($reg['full_name']) ?></td>
                            <td><?= htmlspecialchars($reg['email']) ?></td>
                            <td><?= htmlspecialchars($reg['cpf']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($reg['submitted_at'])) ?></td>
                            <td>
                                <a href="review_registration.php?id=<?= $reg['id'] ?>" class="btn-saiba-mais">Analisar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include 'partials/footer.php'; ?>