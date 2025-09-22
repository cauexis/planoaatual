<?php
/**
 * Controlador Base
 * Fornece funcionalidades comuns para todos os controladores
 */
class BaseController
{
    protected $db;
    protected $data = [];
    
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->initializeController();
    }
    
    /**
     * Inicialização do controlador
     */
    protected function initializeController()
    {
        // Verifica se o usuário está autenticado para páginas protegidas
        if ($this->requiresAuth() && !Security::isAuthenticated()) {
            $this->redirectToLogin();
        }
        
        // Verifica se requer privilégios de admin
        if ($this->requiresAdmin() && !Security::isAdmin()) {
            $this->accessDenied();
        }
        
        // Valida token CSRF para requisições POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->requiresCSRF()) {
            $this->validateCSRF();
        }
    }
    
    /**
     * Define se o controlador requer autenticação
     */
    protected function requiresAuth()
    {
        return false;
    }
    
    /**
     * Define se o controlador requer privilégios de admin
     */
    protected function requiresAdmin()
    {
        return false;
    }
    
    /**
     * Define se o controlador requer validação CSRF
     */
    protected function requiresCSRF()
    {
        return true;
    }
    
    /**
     * Valida token CSRF
     */
    protected function validateCSRF()
    {
        $token = $_POST['_token'] ?? '';
        $form = $_POST['_form'] ?? 'default';
        
        if (!Security::validateCSRFToken($token, $form)) {
            Logger::securityEvent('CSRF token validation failed', [
                'form' => $form,
                'ip' => get_client_ip(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
            
            $this->error('Token de segurança inválido. Recarregue a página e tente novamente.', 403);
        }
    }
    
    /**
     * Redireciona para login
     */
    protected function redirectToLogin()
    {
        $currentUrl = $_SERVER['REQUEST_URI'] ?? '';
        $loginUrl = 'login.php';
        
        if (!empty($currentUrl) && $currentUrl !== '/') {
            $loginUrl .= '?redirect=' . urlencode($currentUrl);
        }
        
        redirect($loginUrl);
    }
    
    /**
     * Acesso negado
     */
    protected function accessDenied()
    {
        Logger::securityEvent('Access denied', [
            'user_id' => $_SESSION['user_id'] ?? 'anonymous',
            'requested_url' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'ip' => get_client_ip()
        ]);
        
        $this->error('Acesso negado. Você não tem permissão para acessar esta página.', 403);
    }
    
    /**
     * Renderiza uma view
     */
    protected function view($viewName, $data = [])
    {
        $this->data = array_merge($this->data, $data);
        
        // Adiciona dados globais
        $this->data['csrf_token'] = Security::generateCSRFToken();
        $this->data['current_user'] = $this->getCurrentUser();
        $this->data['is_authenticated'] = Security::isAuthenticated();
        $this->data['is_admin'] = Security::isAdmin();
        
        // Carrega a view
        $viewFile = __DIR__ . '/../views/' . $viewName . '.php';
        
        if (!file_exists($viewFile)) {
            throw new Exception("View não encontrada: {$viewName}");
        }
        
        // Extrai variáveis para o escopo da view
        extract($this->data);
        
        include $viewFile;
    }
    
    /**
     * Retorna resposta JSON
     */
    protected function json($data, $statusCode = 200)
    {
        json_response($data, $statusCode);
    }
    
    /**
     * Retorna erro
     */
    protected function error($message, $statusCode = 500)
    {
        if (is_ajax()) {
            $this->json(['error' => $message], $statusCode);
        } else {
            http_response_code($statusCode);
            $this->view('error', ['message' => $message, 'code' => $statusCode]);
        }
        exit();
    }
    
    /**
     * Retorna sucesso
     */
    protected function success($message, $data = [])
    {
        if (is_ajax()) {
            $this->json(array_merge(['success' => true, 'message' => $message], $data));
        } else {
            $_SESSION['success_message'] = $message;
            return true;
        }
    }
    
    /**
     * Valida dados de entrada
     */
    protected function validate($data, $rules)
    {
        $validator = Validator::make($data, $rules);
        
        if (!$validator->validate()) {
            $errors = $validator->getErrors();
            
            if (is_ajax()) {
                $this->json(['errors' => $errors], 422);
            } else {
                $_SESSION['validation_errors'] = $errors;
                $_SESSION['old_input'] = $data;
                return false;
            }
        }
        
        return $validator->getValidatedData();
    }
    
    /**
     * Obtém usuário atual
     */
    protected function getCurrentUser()
    {
        if (!Security::isAuthenticated()) {
            return null;
        }
        
        $userId = $_SESSION['user_id'];
        
        return cache("user_{$userId}", function() use ($userId) {
            return $this->db->selectOne(
                "SELECT id, nome, email, created_at FROM users WHERE id = :id",
                ['id' => $userId]
            );
        }, 300); // Cache por 5 minutos
    }
    
    /**
     * Log de atividade do usuário
     */
    protected function logActivity($action, $details = '')
    {
        if (Security::isAuthenticated()) {
            Logger::userActivity($_SESSION['user_id'], $action, $details);
        }
    }
    
    /**
     * Sanitiza dados de entrada
     */
    protected function sanitize($data, $type = 'string')
    {
        return Security::sanitizeInput($data, $type);
    }
    
    /**
     * Verifica se o método HTTP é o esperado
     */
    protected function requireMethod($method)
    {
        if ($_SERVER['REQUEST_METHOD'] !== strtoupper($method)) {
            $this->error('Método não permitido', 405);
        }
    }
    
    /**
     * Obtém parâmetro da requisição
     */
    protected function input($key, $default = null, $sanitize = true)
    {
        $value = $_POST[$key] ?? $_GET[$key] ?? $default;
        
        if ($sanitize && $value !== null) {
            $value = $this->sanitize($value);
        }
        
        return $value;
    }
    
    /**
     * Obtém todos os dados da requisição
     */
    protected function all($sanitize = true)
    {
        $data = array_merge($_GET, $_POST);
        
        if ($sanitize) {
            $data = $this->sanitize($data);
        }
        
        return $data;
    }
    
    /**
     * Verifica se há mensagens de sessão
     */
    protected function getSessionMessages()
    {
        $messages = [];
        
        if (isset($_SESSION['success_message'])) {
            $messages['success'] = $_SESSION['success_message'];
            unset($_SESSION['success_message']);
        }
        
        if (isset($_SESSION['error_message'])) {
            $messages['error'] = $_SESSION['error_message'];
            unset($_SESSION['error_message']);
        }
        
        if (isset($_SESSION['validation_errors'])) {
            $messages['validation_errors'] = $_SESSION['validation_errors'];
            unset($_SESSION['validation_errors']);
        }
        
        if (isset($_SESSION['old_input'])) {
            $messages['old_input'] = $_SESSION['old_input'];
            unset($_SESSION['old_input']);
        }
        
        return $messages;
    }
    
    /**
     * Adiciona dados para a view
     */
    protected function with($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
        
        return $this;
    }
}
