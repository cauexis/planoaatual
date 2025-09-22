<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Verifica se o usuário tem a permissão de 'admin'
if ($_SESSION['admin_role'] !== 'admin') {
    // Se não for admin, mostra uma mensagem de erro e impede o acesso.
    die("Acesso negado. Você não tem permissão para acessar esta página.");
}
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include 'config/db.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $conn->prepare("INSERT INTO network_partners (name, type, specialty, address, city, state, phone) VALUES (:name, :type, :specialty, :address, :city, :state, :phone)");
        $stmt->execute([
            'name' => $_POST['name'],
            'type' => $_POST['type'],
            'specialty' => $_POST['specialty'],
            'address' => $_POST['address'],
            'city' => $_POST['city'],
            'state' => $_POST['state'],
            'phone' => $_POST['phone']
        ]);
        $message = "Parceiro da rede credenciada adicionado com sucesso!";
    } catch (PDOException $e) {
        $message = "Erro ao adicionar parceiro: " . $e->getMessage();
    }
}
?>
<?php include 'partials/header.php'; ?>

<section>
    <div class="container-section">
        <h2>Adicionar à Rede Credenciada</h2>
        <?php if (!empty($message)): ?>
            <p style="background-color: lightyellow; padding: 10px; border-radius: 5px;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form action="admin_network.php" method="POST">
            <label>Nome do Parceiro:</label>
            <input type="text" name="name" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
            
            <label>Tipo:</label>
            <select name="type" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
                <option value="Hospital">Hospital</option>
                <option value="Laboratório">Laboratório</option>
                <option value="Clínica Médica">Clínica Médica</option>
                <option value="Médico Especialista">Médico Especialista</option>
            </select>

            <label>Especialidade (se aplicável):</label>
            <input type="text" name="specialty" style="width: 100%; padding: 8px; margin-bottom: 10px;">

            <label>Endereço:</label>
            <input type="text" name="address" style="width: 100%; padding: 8px; margin-bottom: 10px;">

            <label>Cidade:</label>
            <input type="text" name="city" required value="Manaus" style="width: 100%; padding: 8px; margin-bottom: 10px;">
            
            <label>Estado:</label>
            <input type="text" name="state" required value="Amazonas" style="width: 100%; padding: 8px; margin-bottom: 10px;">

            <label>Telefone:</label>
            <input type="text" name="phone" style="width: 100%; padding: 8px; margin-bottom: 10px;">

            <button type="submit">Adicionar Parceiro</button>
        </form>
    </div>
</section>

<?php include 'partials/footer.php'; ?>