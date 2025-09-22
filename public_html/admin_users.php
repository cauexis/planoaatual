<?php
// admin_users.php
session_start();

// Segurança: Apenas administradores podem acessar esta página
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include 'config/db.php';

$page_title = 'Gestão de Beneficiários - Painel Admin';

try {
    // Busca todos os usuários da tabela 'users'
    $stmt = $conn->query("SELECT id, full_name, email, cpf, created_at FROM users ORDER BY full_name ASC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar beneficiários: " . $e->getMessage());
}
?>
<?php include 'partials/header.php'; ?>

<section class="admin-section">
    <div class="container-section">
        <h2>Gestão de Beneficiários</h2>
        <p>Abaixo está a lista de todos os usuários (beneficiários titulares) cadastrados no portal.</p>

        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome Completo</th>
                        <th>E-mail</th>
                        <th>CPF</th>
                        <th>Data de Cadastro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6">Nenhum beneficiário cadastrado no momento.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['id']) ?></td>
                                <td><?= htmlspecialchars($user['full_name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['cpf']) ?></td>
                                <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <a href="admin_user_detail.php?id=<?= $user['id'] ?>" class="btn-action view">Ver Detalhes</a>
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