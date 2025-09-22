<?php
/**
 * Sistema de Segurança da Aplicação
 * Gerencia autenticação, autorização, CSRF, sanitização e outras medidas de segurança
 */
class Security
{
    private static $csrfTokens = [];
    
    /**
     * Gera um token CSRF único
     */
    public static function generateCSRFToken($form = 'default')
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_tokens'][$form] = $token;
        
        // Limita o número de tokens armazenados
        if (count($_SESSION['csrf_tokens']) > 10) {
            array_shift($_SESSION['csrf_tokens']);
        }
        
        Logger::debug("CSRF token generated for form: {$form}");
        return $token;
    }
    
    /**
     * Valida um token CSRF
     */
    public static function validateCSRFToken($token, $form = 'default')
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_tokens'][$form])) {
            Logger::securityEvent('CSRF token validation failed: token not found', ['form' => $form]);
            return false;
        }
        
        $isValid = hash_equals($_SESSION['csrf_tokens'][$form], $token);
        
        if ($isValid) {
            // Remove o token após uso (one-time use)
            unset($_SESSION['csrf_tokens'][$form]);
            Logger::debug("CSRF token validated successfully for form: {$form}");
        } else {
            Logger::securityEvent('CSRF token validation failed: invalid token', ['form' => $form]);
        }
        
        return $isValid;
    }
    
    /**
     * Sanitiza entrada de dados
     */
    public static function sanitizeInput($input, $type = 'string')
    {
        if (is_array($input)) {
            return array_map(function($item) use ($type) {
                return self::sanitizeInput($item, $type);
            }, $input);
        }
        
        switch ($type) {
            case 'email':
                return filter_var(trim($input), FILTER_SANITIZE_EMAIL);
                
            case 'int':
                return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
                
            case 'float':
                return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                
            case 'url':
                return filter_var(trim($input), FILTER_SANITIZE_URL);
                
            case 'html':
                return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
                
            case 'sql':
                // Para uso em queries preparadas - remove caracteres perigosos
                return preg_replace('/[^\w\s\-@.]/', '', trim($input));
                
            case 'filename':
                return preg_replace('/[^a-zA-Z0-9\-_\.]/', '', trim($input));
                
            default: // string
                return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }
    }
    
    /**
     * Valida entrada de dados
     */
    public static function validateInput($input, $rules)
    {
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $input[$field] ?? null;
            
            foreach ($fieldRules as $rule => $ruleValue) {
                switch ($rule) {
                    case 'required':
                        if ($ruleValue && (empty($value) && $value !== '0')) {
                            $errors[$field][] = "O campo {$field} é obrigatório";
                        }
                        break;
                        
                    case 'email':
                        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = "O campo {$field} deve ser um email válido";
                        }
                        break;
                        
                    case 'min_length':
                        if (!empty($value) && strlen($value) < $ruleValue) {
                            $errors[$field][] = "O campo {$field} deve ter pelo menos {$ruleValue} caracteres";
                        }
                        break;
                        
                    case 'max_length':
                        if (!empty($value) && strlen($value) > $ruleValue) {
                            $errors[$field][] = "O campo {$field} deve ter no máximo {$ruleValue} caracteres";
                        }
                        break;
                        
                    case 'numeric':
                        if (!empty($value) && !is_numeric($value)) {
                            $errors[$field][] = "O campo {$field} deve ser numérico";
                        }
                        break;
                        
                    case 'regex':
                        if (!empty($value) && !preg_match($ruleValue, $value)) {
                            $errors[$field][] = "O campo {$field} tem formato inválido";
                        }
                        break;
                        
                    case 'in':
                        if (!empty($value) && !in_array($value, $ruleValue)) {
                            $errors[$field][] = "O campo {$field} contém um valor inválido";
                        }
                        break;
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Hash seguro de senha
     */
    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536, // 64 MB
            'time_cost' => 4,       // 4 iterations
            'threads' => 3,         // 3 threads
        ]);
    }
    
    /**
     * Verifica senha
     */
    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }
    
    /**
     * Gera senha aleatória segura
     */
    public static function generateRandomPassword($length = 12)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        return substr(str_shuffle(str_repeat($chars, ceil($length / strlen($chars)))), 0, $length);
    }
    
    /**
     * Controle de tentativas de login
     */
    public static function checkLoginAttempts($identifier)
    {
        $db = Database::getInstance();
        $maxAttempts = Config::get('security.max_login_attempts', 5);
        $lockoutTime = Config::get('security.lockout_time', 900); // 15 minutos
        
        // Verifica tentativas recentes
        $query = "SELECT COUNT(*) as attempts, MAX(attempted_at) as last_attempt 
                  FROM login_attempts 
                  WHERE identifier = :identifier 
                  AND attempted_at > DATE_SUB(NOW(), INTERVAL :lockout_time SECOND)
                  AND success = 0";
        
        $result = $db->selectOne($query, [
            'identifier' => $identifier,
            'lockout_time' => $lockoutTime
        ]);
        
        if ($result && $result['attempts'] >= $maxAttempts) {
            $timeRemaining = $lockoutTime - (time() - strtotime($result['last_attempt']));
            Logger::securityEvent('Login blocked due to too many attempts', [
                'identifier' => $identifier,
                'attempts' => $result['attempts'],
                'time_remaining' => $timeRemaining
            ]);
            return ['blocked' => true, 'time_remaining' => max(0, $timeRemaining)];
        }
        
        return ['blocked' => false, 'attempts' => $result['attempts'] ?? 0];
    }
    
    /**
     * Registra tentativa de login
     */
    public static function logLoginAttempt($identifier, $success, $userId = null)
    {
        $db = Database::getInstance();
        
        try {
            $query = "INSERT INTO login_attempts (identifier, user_id, success, ip_address, user_agent, attempted_at) 
                      VALUES (:identifier, :user_id, :success, :ip, :user_agent, NOW())";
            
            $db->insert($query, [
                'identifier' => $identifier,
                'user_id' => $userId,
                'success' => $success ? 1 : 0,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
            
            Logger::loginAttempt($identifier, $success);
        } catch (Exception $e) {
            Logger::error('Failed to log login attempt: ' . $e->getMessage());
        }
    }
    
    /**
     * Limpa tentativas de login antigas
     */
    public static function cleanOldLoginAttempts()
    {
        $db = Database::getInstance();
        
        try {
            $query = "DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)";
            $deleted = $db->delete($query);
            
            if ($deleted > 0) {
                Logger::info("Cleaned {$deleted} old login attempts");
            }
        } catch (Exception $e) {
            Logger::error('Failed to clean old login attempts: ' . $e->getMessage());
        }
    }
    
    /**
     * Verifica se o usuário está autenticado
     */
    public static function isAuthenticated()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Verifica se o usuário é admin
     */
    public static function isAdmin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
    
    /**
     * Força logout por segurança
     */
    public static function forceLogout($reason = 'Security logout')
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $userId = $_SESSION['user_id'] ?? 'unknown';
        
        session_destroy();
        session_start();
        session_regenerate_id(true);
        
        Logger::securityEvent("Forced logout: {$reason}", ['user_id' => $userId]);
    }
    
    /**
     * Verifica timeout de sessão
     */
    public static function checkSessionTimeout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $timeout = Config::get('security.session_timeout', 3600);
        
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > $timeout) {
                self::forceLogout('Session timeout');
                return false;
            }
        }
        
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    /**
     * Previne ataques de fixação de sessão
     */
    public static function regenerateSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        session_regenerate_id(true);
        Logger::debug('Session ID regenerated for security');
    }
    
    /**
     * Verifica se o IP mudou (possível sequestro de sessão)
     */
    public static function validateSessionIP()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $currentIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        if (isset($_SESSION['ip_address'])) {
            if ($_SESSION['ip_address'] !== $currentIP) {
                Logger::securityEvent('IP address changed during session', [
                    'original_ip' => $_SESSION['ip_address'],
                    'current_ip' => $currentIP,
                    'user_id' => $_SESSION['user_id'] ?? 'unknown'
                ]);
                self::forceLogout('IP address changed');
                return false;
            }
        } else {
            $_SESSION['ip_address'] = $currentIP;
        }
        
        return true;
    }
}
