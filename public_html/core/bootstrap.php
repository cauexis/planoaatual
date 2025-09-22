<?php
/**
 * Bootstrap do Sistema
 * Inicializa todas as classes e configurações necessárias
 */

// Define o timezone
date_default_timezone_set('America/Manaus');

// Configurações de erro para desenvolvimento
if (!defined('PRODUCTION')) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

/**
 * Autoloader simples para as classes do sistema
 */
spl_autoload_register(function ($className) {
    $paths = [
        __DIR__ . '/classes/' . $className . '.php',
        __DIR__ . '/config/' . $className . '.php',
        __DIR__ . '/../app/controllers/' . $className . '.php',
        __DIR__ . '/../app/models/' . $className . '.php',
        __DIR__ . '/../app/middleware/' . $className . '.php',
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Carrega configurações
if (!class_exists('Config')) {
    require_once __DIR__ . '/config/Config.php';
}

// Carrega configurações personalizadas se existirem
$customConfigFile = __DIR__ . '/config/custom.php';
if (file_exists($customConfigFile)) {
    Config::loadFromFile($customConfigFile);
}

// Inicializa o sistema de sessão seguro
if (session_status() === PHP_SESSION_NONE) {
    // Configurações de sessão seguras
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Strict');
    
    $sessionName = Config::get('security.session_name', 'PLANOA_SESSION');
    session_name($sessionName);
    
    session_start();
    
    // Regenera ID da sessão periodicamente para segurança
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > 300) { // 5 minutos
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

// Verifica timeout de sessão
if (Security::isAuthenticated()) {
    Security::checkSessionTimeout();
    Security::validateSessionIP();
}

// Limpa tentativas de login antigas (executa ocasionalmente)
if (rand(1, 100) === 1) {
    Security::cleanOldLoginAttempts();
}

// Limpa cache expirado (executa ocasionalmente)
if (rand(1, 50) === 1) {
    Cache::cleanExpired();
}

// Função helper para debug
if (!function_exists('dd')) {
    function dd(...$vars) {
        foreach ($vars as $var) {
            echo '<pre>';
            var_dump($var);
            echo '</pre>';
        }
        die();
    }
}

// Função helper para logging rápido
if (!function_exists('log_info')) {
    function log_info($message, $context = []) {
        Logger::info($message, $context);
    }
}

// Função helper para validação rápida
if (!function_exists('validate')) {
    function validate($data, $rules) {
        return Validator::make($data, $rules);
    }
}

// Função helper para cache rápido
if (!function_exists('cache')) {
    function cache($key, $callback = null, $ttl = null) {
        if ($callback === null) {
            return Cache::get($key);
        }
        return Cache::remember($key, $callback, $ttl);
    }
}

// Função helper para sanitização
if (!function_exists('clean')) {
    function clean($input, $type = 'string') {
        return Security::sanitizeInput($input, $type);
    }
}

// Função helper para escape de HTML
if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}

// Função helper para URLs
if (!function_exists('url')) {
    function url($path = '') {
        $baseUrl = Config::get('app.base_url', '');
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
}

// Função helper para assets
if (!function_exists('asset')) {
    function asset($path) {
        return url('public/assets/' . ltrim($path, '/'));
    }
}

// Função helper para redirecionamento
if (!function_exists('redirect')) {
    function redirect($url, $statusCode = 302) {
        header("Location: {$url}", true, $statusCode);
        exit();
    }
}

// Função helper para JSON response
if (!function_exists('json_response')) {
    function json_response($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }
}

// Função helper para verificar se é requisição AJAX
if (!function_exists('is_ajax')) {
    function is_ajax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}

// Função helper para obter IP do cliente
if (!function_exists('get_client_ip')) {
    function get_client_ip() {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
}

// Registra handlers de erro personalizados
set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    
    $errorTypes = [
        E_ERROR => 'ERROR',
        E_WARNING => 'WARNING',
        E_NOTICE => 'NOTICE',
        E_USER_ERROR => 'USER_ERROR',
        E_USER_WARNING => 'USER_WARNING',
        E_USER_NOTICE => 'USER_NOTICE'
    ];
    
    $type = $errorTypes[$severity] ?? 'UNKNOWN';
    Logger::error("PHP {$type}: {$message} in {$file} on line {$line}");
    
    return false;
});

set_exception_handler(function($exception) {
    Logger::critical('Uncaught exception: ' . $exception->getMessage(), [
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ]);
    
    if (Config::get('app.debug', false)) {
        echo '<h1>Erro do Sistema</h1>';
        echo '<p><strong>Mensagem:</strong> ' . $exception->getMessage() . '</p>';
        echo '<p><strong>Arquivo:</strong> ' . $exception->getFile() . '</p>';
        echo '<p><strong>Linha:</strong> ' . $exception->getLine() . '</p>';
        echo '<pre>' . $exception->getTraceAsString() . '</pre>';
    } else {
        echo '<h1>Erro Interno do Servidor</h1>';
        echo '<p>Ocorreu um erro inesperado. Tente novamente mais tarde.</p>';
    }
    
    exit(1);
});

// Log de inicialização do sistema
Logger::info('Sistema inicializado', [
    'php_version' => PHP_VERSION,
    'memory_limit' => ini_get('memory_limit'),
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'CLI',
    'ip' => get_client_ip()
]);

// Verifica se as tabelas necessárias existem
try {
    $db = Database::getInstance();
    
    // Cria tabela de tentativas de login se não existir
    if (!$db->tableExists('login_attempts')) {
        $createTable = "
            CREATE TABLE login_attempts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                identifier VARCHAR(255) NOT NULL,
                user_id INT NULL,
                success BOOLEAN NOT NULL DEFAULT FALSE,
                ip_address VARCHAR(45) NOT NULL,
                user_agent TEXT,
                attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_identifier (identifier),
                INDEX idx_attempted_at (attempted_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $db->query($createTable);
        Logger::info('Tabela login_attempts criada');
    }
    
} catch (Exception $e) {
    Logger::warning('Não foi possível verificar/criar tabelas: ' . $e->getMessage());
}
