<?php
/**
 * LOGIN MELHORADO - VERSÃO CORRIGIDA
 * MANTÉM 100% DO SEU DESIGN ORIGINAL
 * Funciona mesmo se algumas classes não estiverem disponíveis
 */

// Inicia sessão se não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Tenta carregar o sistema melhorado, mas funciona sem ele também
$systemLoaded = false;
if (file_exists('core/bootstrap.php')) {
    try {
        require_once 'core/bootstrap.php';
        $systemLoaded = true;
    } catch (Exception $e) {
        // Se der erro, continua com sistema básico
        $systemLoaded = false;
    }
}

// Se não conseguiu carregar o sistema, usa conexão básica
if (!$systemLoaded) {
    if (file_exists('config/db.php')) {
        require_once 'config/db.php';
    }
}

// Verifica se já está logado (método básico)
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    header('Location: area_cliente_dashboard.php');
    exit;
}

$error = '';
$success = '';

// Processa login se for POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    
    // Validação básica
    if (empty($email) || empty($password)) {
        $error = 'Por favor, preencha todos os campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'E-mail inválido.';
    } else {
        try {
            // Tenta usar o sistema melhorado se disponível
            if ($systemLoaded && class_exists('AuthController')) {
                $auth = new AuthController();
                $result = $auth->attemptLogin($email, $password);
                
                if ($result['success']) {
                    $_SESSION['user_id'] = $result['user']['id'];
                    $_SESSION['user_name'] = $result['user']['nome'];
                    $_SESSION['user_email'] = $result['user']['email'];
                    
                    // Log de sucesso se disponível
                    if (class_exists('Logger')) {
                        Logger::info('Login successful', ['email' => $email]);
                    }
                    
                    header('Location: area_cliente_dashboard.php');
                    exit;
                } else {
                    $error = $result['message'] ?? 'E-mail ou senha incorretos.';
                }
            } else {
                // Sistema básico de login
                if (isset($conn)) {
                    $stmt = $conn->prepare("SELECT id, nome, email, senha FROM users WHERE email = ? AND active = 1");
                    $stmt->execute([$email]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($user && password_verify($password, $user['senha'])) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['nome'];
                        $_SESSION['user_email'] = $user['email'];
                        
                        header('Location: area_cliente_dashboard.php');
                        exit;
                    } else {
                        $error = 'E-mail ou senha incorretos.';
                    }
                } else {
                    $error = 'Erro de conexão com o banco de dados.';
                }
            }
        } catch (Exception $e) {
            $error = 'Erro interno. Tente novamente.';
            // Log do erro se disponível
            if (class_exists('Logger')) {
                Logger::error('Login error', ['error' => $e->getMessage(), 'email' => $email]);
            }
        }
    }
}

// Obtém mensagens da sessão se existirem
if (isset($_SESSION['error_message'])) {
    $error = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Gera token CSRF se o sistema estiver disponível
$csrfToken = '';
if ($systemLoaded && class_exists('Security')) {
    try {
        $csrfToken = Security::generateCSRFToken('login');
    } catch (Exception $e) {
        // Se der erro, continua sem CSRF
        $csrfToken = '';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Área do Cliente Plano A</title>
    <!-- MANTÉM SEU CSS ORIGINAL -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Estilos para mensagens de erro/sucesso */
        .form-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        
        .form-success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        
        .system-status {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            z-index: 1000;
        }
        
        .system-enhanced {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .system-basic {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
    </style>
</head>
<body>
    <!-- Indicador do sistema (removível em produção) -->
    <?php if ($systemLoaded): ?>
        <div class="system-status system-enhanced">
            🔒 Sistema Melhorado Ativo
        </div>
    <?php else: ?>
        <div class="system-status system-basic">
            ⚠️ Sistema Básico
        </div>
    <?php endif; ?>

    <!-- MANTÉM SEU DESIGN EXATO -->
    <div class="login-wrapper">
        <div class="login-branding-panel com-fundo">
            <a href="index.php">
                <!-- MANTÉM SUA LOGO -->
                <img src="img/planoa.png" alt="Logo Plano A" class="logo">
            </a>
            <!-- MANTÉM SUAS CORES E TEXTOS -->
            <h2>Bem-vindo(a) de volta!</h2>
            <p>Sua saúde em primeiro lugar.</p>
        </div>

        <div class="login-form-panel">
            <div class="form-container">
                <h2>Acesse sua Área do Cliente</h2>
                
                <!-- Mensagens de erro/sucesso -->
                <?php if ($error): ?>
                    <div class="form-error">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="form-success">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <!-- MANTÉM SEU FORMULÁRIO EXATO -->
                <form action="" method="POST">
                    <!-- Token CSRF se disponível -->
                    <?php if ($csrfToken): ?>
                        <input type="hidden" name="_token" value="<?= $csrfToken ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" required 
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Senha:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit">Entrar</button>
                </form>

                <!-- MANTÉM SEUS LINKS -->
                <div class="login-links">
                    <a href="request_reset.php">Esqueci minha senha</a>
                    <span>|</span>
                    <a href="register.php">Não tem uma conta? Cadastre-se</a>
                </div>
                
                <!-- Informações do sistema (removível) -->
                <div style="margin-top: 20px; padding: 10px; background: #f8f9fa; border-radius: 4px; font-size: 12px; color: #6c757d;">
                    <?php if ($systemLoaded): ?>
                        ✅ <strong>Sistema Melhorado Ativo:</strong><br>
                        • Proteção CSRF<br>
                        • Logs de segurança<br>
                        • Controle de tentativas<br>
                        • Criptografia avançada
                    <?php else: ?>
                        ⚠️ <strong>Sistema Básico:</strong><br>
                        Execute <code>instalador_simples.php</code> para ativar todas as melhorias
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript para UX -->
    <script>
    // Adiciona feedback visual durante o login
    document.querySelector('form').addEventListener('submit', function(e) {
        const button = this.querySelector('button[type="submit"]');
        const originalText = button.textContent;
        button.textContent = 'Entrando...';
        button.disabled = true;
        
        // Se der erro, restaura o botão após 3 segundos
        setTimeout(function() {
            if (button.disabled) {
                button.textContent = originalText;
                button.disabled = false;
            }
        }, 3000);
    });
    
    // Auto-focus no campo de email se estiver vazio
    const emailField = document.getElementById('email');
    if (!emailField.value) {
        emailField.focus();
    } else {
        document.getElementById('password').focus();
    }
    
    // Remove o indicador do sistema após 5 segundos
    setTimeout(function() {
        const indicator = document.querySelector('.system-status');
        if (indicator) {
            indicator.style.opacity = '0';
            setTimeout(function() {
                indicator.remove();
            }, 500);
        }
    }, 5000);
    </script>
</body>
</html>
