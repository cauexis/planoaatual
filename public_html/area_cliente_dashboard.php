<?php
session_start();
include 'partials/header.php';
include 'config/db.php';

// Proteção da página: se o usuário não estiver logado, redireciona para o login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Pega o ID do usuário da sessão para buscar seu nome
$user_id = $_SESSION['user_id'];
$nome_cliente = 'Cliente'; // Nome padrão

try {
    // Busca o nome do usuário no banco para uma saudação personalizada
    $stmt = $conn->prepare("SELECT full_name FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && !empty($user['full_name'])) {
        // Pega o primeiro nome para a saudação
        $nome_completo = explode(' ', $user['full_name']);
        $nome_cliente = $nome_completo[0];
    }
} catch (PDOException $e) {
    // Se houver erro, apenas continua com o nome padrão "Cliente"
}

$page_title = 'Painel do Cliente - Plano A';
?>

<main>
    <section class="dashboard-section">
        <div class="container-section">
            <h2>Painel do Cliente</h2>
            <p>Olá, <strong><?= htmlspecialchars($nome_cliente) ?></strong>! Bem-vindo(a) à sua área exclusiva.</p>

            <div class="dashboard-grid">
                <a href="meus-boletos.php" class="dashboard-card">
                    <img src="img/hospital.png" alt="Ícone de Boleto">
                    <h3>Meus Boletos</h3>
                    <p>2ª via e histórico de pagamentos.</p>
                </a>

                <a href="rede_credenciada.php" class="dashboard-card">
                    <img src="img/hospital.png" alt="Ícone de Rede Credenciada">
                    <h3>Rede Credenciada</h3>
                    <p>Encontre médicos e hospitais.</p>
                </a>
                
                <a href="#" class="dashboard-card disabled">
                    <img src="img/hospital.png" alt="Ícone de Carteirinha">
                    <h3>Carteirinha Virtual</h3>
                    <p>(Em breve)</p>
                </a>

                <a href="meus_dados.php" class="dashboard-card">
                    <img src="img/hospital.png" alt="Ícone de Usuário">
                    <h3>Meus Dados</h3>
                    <p>Sua documentação aqui</p>
                </a>
            </div>

            <div class="logout-container">
                <a href="logout.php" class="btn-logout">Sair do portal</a>
                <a href="dashboard.php" class="btn-logout">Ir para minha conta</a>
            </div>
        </div>
    </section>
</main>

<?php include 'partials/footer.php'; ?>