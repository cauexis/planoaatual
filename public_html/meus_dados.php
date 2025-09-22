<?php
// meus_dados.php

session_start();

// 1. Segurança: Verifica se o usuário está logado. Se não, redireciona para o login.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

$user_id = $_SESSION['user_id'];
$user_data = null; // Inicializa a variável de dados

// 2. Busca no Banco de Dados: Pega os dados do usuário que está na sessão.
try {
    $stmt = $conn->prepare("SELECT full_name, email, cpf FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Em um caso real, logaríamos o erro sem mostrá-lo ao usuário
    die("Erro ao buscar os dados do usuário.");
}

// Se, por algum motivo, o usuário da sessão não for encontrado no banco
if (!$user_data) {
    die("Usuário não encontrado.");
}
?>
<?php include 'partials/header.php'; ?>

<section>
    <div class="container-section">
        <h2>Meus Dados Cadastrais</h2>
        <p>Aqui estão as informações associadas à sua conta.</p>

        <div class="data-card">
            <div class="data-item">
                <span class="data-label">Nome Completo:</span>
                <span class="data-value"><?= htmlspecialchars($user_data['full_name']) ?></span>
            </div>
            <div class="data-item">
                <span class="data-label">E-mail de Contato:</span>
                <span class="data-value"><?= htmlspecialchars($user_data['email']) ?></span>
            </div>
            <div class="data-item">
                <span class="data-label">CPF:</span>
                <span class="data-value"><?= htmlspecialchars($user_data['cpf']) ?></span>
            </div>
        </div>

        <div class="data-actions">
            <p>Se alguma informação estiver incorreta, por favor, entre em contato com nosso suporte.</p>
            <a href="contato.php" class="btn-saiba-mais">Fale Conosco</a>
        </div>
        <a href="dashboard.php" style="margin-top: 30px; display: inline-block;">← Voltar ao Painel</a>
    </div>
</section>

<?php include 'partials/footer.php'; ?>