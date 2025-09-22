<?php
session_start(); // A sessão DEVE ser iniciada no topo do arquivo

// Se o admin já estiver logado, redireciona para o painel
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

include 'config/db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT id, username, password, role FROM admins WHERE username = :username"); // Puxa a coluna 'role'
        $stmt->execute(['username' => $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_role'] = $admin['role']; // Salva o 'role' na sessão
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Usuário ou senha inválidos.";
        }
    } catch(PDOException $e) {
        $error = "Erro no servidor.";
    }
}

include 'partials/header.php';
?>

<section>
    <div class="container-section" style="max-width: 500px;">
        <h2>Login Administrativo</h2>
        <?php if ($error): ?>
            <p style="color: red; background-color: #ffcccb; padding: 10px; border-radius: 5px;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form action="admin_login.php" method="POST">
            <div style="margin-bottom: 15px;">
                <label for="username">Usuário:</label>
                <input type="text" id="username" name="username" required style="width: 100%; padding: 10px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" required style="width: 100%; padding: 10px;">
            </div>
            <button type="submit">Entrar</button>
        </form>
    </div>
</section>

<?php include 'partials/footer.php'; ?>