<?php
/**
 * Controlador de Autenticação MELHORADO
 * MANTÉM TODO O DESIGN E CORES ORIGINAIS
 * Apenas melhora a segurança e organização do código
 */
class AuthController extends BaseController
{
    protected function requiresAuth()
    {
        $action = $_GET['action'] ?? '';
        return $action === 'logout';
    }
    
    protected function requiresCSRF()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Processa login (MANTÉM O DESIGN ORIGINAL)
     * Usa o mesmo login.php com as mesmas cores e layout
     */
    public function processLogin()
    {
        $this->requireMethod('POST');
        
        $email = $this->sanitize($_POST['email'] ?? '', 'email');
        $password = $_POST['password'] ?? '';
        
        // Validação básica
        if (empty($email) || empty($password)) {
            $_SESSION['error_message'] = 'Email e senha são obrigatórios.';
            redirect('login.php');
        }
        
        // Verifica tentativas de login (NOVA SEGURANÇA)
        $attemptCheck = Security::checkLoginAttempts($email);
        
        if ($attemptCheck['blocked']) {
            $timeRemaining = ceil($attemptCheck['time_remaining'] / 60);
            $_SESSION['error_message'] = "Muitas tentativas de login. Tente novamente em {$timeRemaining} minutos.";
            redirect('login.php');
        }
        
        try {
            // Busca usuário (MESMA LÓGICA, MAIS SEGURA)
            $user = $this->db->selectOne(
                "SELECT id, email, password, nome FROM users WHERE email = :email",
                ['email' => $email]
            );
            
            if ($user && Security::verifyPassword($password, $user['password'])) {
                // Login bem-sucedido (MANTÉM A MESMA FUNCIONALIDADE)
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['nome'];
                $_SESSION['last_activity'] = time();
                
                Security::logLoginAttempt($email, true, $user['id']);
                Logger::userActivity($user['id'], 'login', 'Login realizado com sucesso');
                
                // Redireciona para o mesmo lugar de sempre
                redirect('area_cliente_dashboard.php');
                
            } else {
                // Login falhou (MESMA MENSAGEM DE ERRO)
                Security::logLoginAttempt($email, false);
                $_SESSION['error_message'] = 'E-mail ou senha inválidos.';
                redirect('login.php');
            }
            
        } catch (Exception $e) {
            Logger::error('Erro no login: ' . $e->getMessage());
            $_SESSION['error_message'] = 'Erro no servidor. Tente novamente mais tarde.';
            redirect('login.php');
        }
    }
    
    /**
     * Processa logout (MANTÉM A MESMA FUNCIONALIDADE)
     */
    public function logout()
    {
        $userId = $_SESSION['user_id'] ?? null;
        
        if ($userId) {
            Logger::userActivity($userId, 'logout', 'Logout realizado');
        }
        
        // Destrói sessão (MESMO COMPORTAMENTO)
        session_destroy();
        session_start();
        session_regenerate_id(true);
        
        redirect('index.php');
    }
    
    /**
     * Processa registro (MANTÉM O DESIGN ORIGINAL)
     */
    public function processRegister()
    {
        $this->requireMethod('POST');
        
        // Coleta dados (MESMOS CAMPOS)
        $nome = $this->sanitize($_POST['nome'] ?? '');
        $email = $this->sanitize($_POST['email'] ?? '', 'email');
        $password = $_POST['password'] ?? '';
        $telefone = $this->sanitize($_POST['telefone'] ?? '');
        $cpf = $this->sanitize($_POST['cpf'] ?? '');
        
        // Validação básica
        if (empty($nome) || empty($email) || empty($password)) {
            $_SESSION['error_message'] = 'Todos os campos são obrigatórios.';
            redirect('register.php');
        }
        
        if (strlen($password) < 6) {
            $_SESSION['error_message'] = 'A senha deve ter pelo menos 6 caracteres.';
            redirect('register.php');
        }
        
        try {
            // Verifica se email já existe (MESMA LÓGICA)
            $existingUser = $this->db->selectOne(
                "SELECT id FROM users WHERE email = :email",
                ['email' => $email]
            );
            
            if ($existingUser) {
                $_SESSION['error_message'] = 'Este email já está cadastrado.';
                redirect('register.php');
            }
            
            // Cria usuário (SENHA MAIS SEGURA)
            $hashedPassword = Security::hashPassword($password);
            
            $userId = $this->db->insert(
                "INSERT INTO users (nome, email, password, telefone, cpf, created_at) VALUES (:nome, :email, :password, :telefone, :cpf, NOW())",
                [
                    'nome' => $nome,
                    'email' => $email,
                    'password' => $hashedPassword,
                    'telefone' => $telefone,
                    'cpf' => preg_replace('/[^0-9]/', '', $cpf)
                ]
            );
            
            if ($userId) {
                Logger::info('Novo usuário registrado', ['user_id' => $userId, 'email' => $email]);
                $_SESSION['success_message'] = 'Cadastro realizado com sucesso! Faça login para continuar.';
                redirect('login.php');
            }
            
        } catch (Exception $e) {
            Logger::error('Erro no registro: ' . $e->getMessage());
            $_SESSION['error_message'] = 'Erro ao criar conta. Tente novamente.';
            redirect('register.php');
        }
    }
}

// EXEMPLO DE USO NO SEU login.php EXISTENTE:
// Você pode adicionar apenas estas linhas no início do seu login.php:
/*
require_once 'core/bootstrap.php';
$auth = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth->processLogin();
}

// O resto do seu código HTML permanece EXATAMENTE IGUAL
// Mesmas cores, mesmo design, mesma estrutura
*/
