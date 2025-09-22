<?php
// admin_email_lists.php (VERSÃO ATUALIZADA)
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: admin_login.php"); exit(); }
include 'config/db.php';
$message = '';

// Lógica para criar uma nova lista
if (isset($_POST['create_list'])) {
    $list_name = trim($_POST['list_name']);
    if (!empty($list_name)) {
        $stmt = $conn->prepare("INSERT INTO email_lists (list_name) VALUES (?)");
        $stmt->execute([$list_name]);
        $message = "Lista '{$list_name}' criada com sucesso!";
    }
}

// Lógica para importar USUÁRIOS LOGADOS (da tabela users)
if (isset($_POST['import_users'])) {
    $list_id = $_POST['list_id'];
    $conn->exec("INSERT IGNORE INTO email_contacts (list_id, email, full_name) SELECT {$list_id}, email, full_name FROM users WHERE email IS NOT NULL AND email != ''");
    $message = "Usuários do portal importados para a lista.";
}

// NOVA LÓGICA: Importar VIDAS (da tabela beneficiaries)
if (isset($_POST['import_beneficiaries'])) {
    $list_id = $_POST['list_id'];
    // Insere todos os beneficiários da tabela 'beneficiaries', ignorando duplicatas e e-mails vazios
    $conn->exec("INSERT IGNORE INTO email_contacts (list_id, email, full_name) SELECT {$list_id}, endereco_email, nome_associado FROM beneficiaries WHERE endereco_email IS NOT NULL AND endereco_email != ''");
    $message = "Beneficiários (vidas) importados para a lista com sucesso!";
}

$lists_stmt = $conn->query("SELECT * FROM email_lists ORDER BY list_name");
$lists = $lists_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'partials/header.php'; ?>
<section class="admin-section">
    <div class="container-section">
        <h2>Gerenciar Listas de E-mail</h2>
        <?php if($message): ?> <div class="form-success"><?= $message ?></div> <?php endif; ?>
        
        <div class="admin-card">
            <h3>Criar Nova Lista</h3>
            <form action="admin_email_lists.php" method="POST">
                <input type="text" name="list_name" placeholder="Nome da Lista (ex: Comunicado Importante)" required>
                <button type="submit" name="create_list">Criar Lista</button>
            </form>
        </div>

        <div class="admin-card">
            <h3>Listas Existentes</h3>
            <table class="admin-table">
                <thead><tr><th>Nome da Lista</th><th>Ações de Importação</th></tr></thead>
                <tbody>
                    <?php if(empty($lists)): ?>
                        <tr><td colspan="2">Nenhuma lista criada.</td></tr>
                    <?php else: ?>
                        <?php foreach($lists as $list): ?>
                        <tr>
                            <td><?= htmlspecialchars($list['list_name']) ?></td>
                            <td>
                                <form action="admin_email_lists.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="list_id" value="<?= $list['id'] ?>">
                                    <button type="submit" name="import_users" class="btn-action view">Importar Usuários do Portal</button>
                                </form>
                                <form action="admin_email_lists.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="list_id" value="<?= $list['id'] ?>">
                                    <button type="submit" name="import_beneficiaries" class="btn-action view" style="background-color: #e3bd20; color: #212121;">Importar Beneficiários (Vidas)</button>
                                </form>
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