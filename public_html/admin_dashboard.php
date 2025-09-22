<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_username = $_SESSION['admin_username'];
$admin_role = $_SESSION['admin_role']; // Pega o 'role' da sessão
?>
<?php include 'partials/header.php'; ?>

<section>
    <div class="container-section">
        <h2>Painel Administrativo</h2>
        <p>Bem-vindo(a), <strong><?= htmlspecialchars($admin_username) ?></strong>! (Perfil: <?= htmlspecialchars(ucfirst($admin_role)) ?>)</p>
        <h3>Gerenciar Cadastros e Vidas:</h3>
        <ul>
        <li><a href="admin_beneficiaries.php">Gerenciar Beneficiários (Vidas)</a></li> <li><a href="admin_registrations.php">Analisar Cadastros Pendentes</a></li>
        </ul>
        <h3>Gerenciar Conteúdo:</h3>
        <ul>
            <li><a href="admin_posts.php">Gerenciar Posts do Blog</a></li>

            <?php if ($admin_role === 'admin'): ?>
                <li><a href="admin_users.php">Gerenciar Beneficiários</a></li>
                <li><a href="admin_plans.php">Gerenciar Planos de Saúde</a></li>
                <li><a href="admin_network.php">Gerenciar Rede Credenciada</a></li>
                <li><a href="admin_email_lists.php">Lista de Emails</a></li> 
                <li><a href="admin_compose_email.php">Envio de Emails</a></li> 
                </ul>
                <?php endif; ?>
        </ul>

        <a href="logout_admin.php" style="color: red; margin-top: 20px; display: inline-block;">Sair (Logout)</a>
    </div>
</section>

<?php include 'partials/footer.php'; ?>