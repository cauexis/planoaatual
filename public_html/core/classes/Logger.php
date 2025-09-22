<?php
/**
 * Sistema de Logs da Aplicação
 * Registra eventos, erros e atividades do sistema
 */
class Logger
{
    const EMERGENCY = 'EMERGENCY';
    const ALERT = 'ALERT';
    const CRITICAL = 'CRITICAL';
    const ERROR = 'ERROR';
    const WARNING = 'WARNING';
    const NOTICE = 'NOTICE';
    const INFO = 'INFO';
    const DEBUG = 'DEBUG';
    
    private static $levels = [
        self::EMERGENCY => 0,
        self::ALERT => 1,
        self::CRITICAL => 2,
        self::ERROR => 3,
        self::WARNING => 4,
        self::NOTICE => 5,
        self::INFO => 6,
        self::DEBUG => 7,
    ];
    
    /**
     * Registra uma mensagem de log
     */
    public static function log($level, $message, $context = [])
    {
        if (!Config::get('logging.enabled', true)) {
            return;
        }
        
        $configLevel = Config::get('logging.level', 'INFO');
        $configLevelValue = self::$levels[$configLevel] ?? 6;
        $currentLevelValue = self::$levels[$level] ?? 6;
        
        // Só registra se o nível atual for menor ou igual ao configurado
        if ($currentLevelValue > $configLevelValue) {
            return;
        }
        
        $logEntry = self::formatLogEntry($level, $message, $context);
        self::writeToFile($logEntry);
        
        // Em modo debug, também exibe no erro do PHP
        if (Config::get('app.debug', false) && in_array($level, [self::ERROR, self::CRITICAL, self::EMERGENCY])) {
            error_log($logEntry);
        }
    }
    
    /**
     * Formata a entrada do log
     */
    private static function formatLogEntry($level, $message, $context)
    {
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'CLI';
        $requestUri = $_SERVER['REQUEST_URI'] ?? 'CLI';
        
        $contextString = '';
        if (!empty($context)) {
            $contextString = ' | Context: ' . json_encode($context, JSON_UNESCAPED_UNICODE);
        }
        
        return "[{$timestamp}] [{$level}] [{$ip}] {$message} | URI: {$requestUri} | User-Agent: {$userAgent}{$contextString}" . PHP_EOL;
    }
    
    /**
     * Escreve no arquivo de log
     */
    private static function writeToFile($logEntry)
    {
        try {
            $logPath = Config::get('logging.path', 'logs/');
            
            // Garante que o caminho termine com /
            if (substr($logPath, -1) !== '/') {
                $logPath .= '/';
            }
            
            $logFile = $logPath . 'app-' . date('Y-m-d') . '.log';
            
            // Cria o diretório se não existir
            if (!file_exists($logPath)) {
                @mkdir($logPath, 0755, true);
            }
            
            // Verifica se consegue escrever
            if (!is_writable(dirname($logFile))) {
                return false;
            }
            
            // Escreve no arquivo
            @file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
            
            // Limpa logs antigos (apenas ocasionalmente para não impactar performance)
            if (rand(1, 100) === 1) {
                self::cleanOldLogs();
            }
            
            return true;
        } catch (Exception $e) {
            // Se der erro, tenta escrever no error_log do PHP
            @error_log("Logger error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Remove logs antigos baseado na configuração
     */
    private static function cleanOldLogs()
    {
        try {
            $maxFiles = Config::get('logging.max_files', 30);
            $logPath = Config::get('logging.path', 'logs/');
            
            // Garante que o caminho termine com /
            if (substr($logPath, -1) !== '/') {
                $logPath .= '/';
            }
            
            $pattern = $logPath . 'app-*.log';
            $files = @glob($pattern);
            
            if (!$files || count($files) <= $maxFiles) {
                return;
            }
            
            // Ordena por data de modificação
            @usort($files, function($a, $b) {
                return @filemtime($a) - @filemtime($b);
            });
            
            // Remove os mais antigos
            $filesToRemove = array_slice($files, 0, count($files) - $maxFiles);
            foreach ($filesToRemove as $file) {
                @unlink($file);
            }
        } catch (Exception $e) {
            // Se der erro na limpeza, apenas ignora
            return;
        }
    }
    
    /**
     * Métodos de conveniência para diferentes níveis
     */
    public static function emergency($message, $context = [])
    {
        self::log(self::EMERGENCY, $message, $context);
    }
    
    public static function alert($message, $context = [])
    {
        self::log(self::ALERT, $message, $context);
    }
    
    public static function critical($message, $context = [])
    {
        self::log(self::CRITICAL, $message, $context);
    }
    
    public static function error($message, $context = [])
    {
        self::log(self::ERROR, $message, $context);
    }
    
    public static function warning($message, $context = [])
    {
        self::log(self::WARNING, $message, $context);
    }
    
    public static function notice($message, $context = [])
    {
        self::log(self::NOTICE, $message, $context);
    }
    
    public static function info($message, $context = [])
    {
        self::log(self::INFO, $message, $context);
    }
    
    public static function debug($message, $context = [])
    {
        self::log(self::DEBUG, $message, $context);
    }
    
    /**
     * Registra tentativa de login
     */
    public static function loginAttempt($email, $success, $ip = null)
    {
        $ip = $ip ?? ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $status = $success ? 'SUCCESS' : 'FAILED';
        
        self::info("Login attempt: {$status} for email: {$email} from IP: {$ip}");
    }
    
    /**
     * Registra atividade do usuário
     */
    public static function userActivity($userId, $action, $details = '')
    {
        self::info("User activity: User ID {$userId} performed '{$action}'. Details: {$details}");
    }
    
    /**
     * Registra erro de segurança
     */
    public static function securityEvent($event, $details = [], $level = self::WARNING)
    {
        self::log($level, "Security event: {$event}", $details);
    }
    
    /**
     * Registra performance de queries
     */
    public static function queryPerformance($query, $executionTime, $params = [])
    {
        if ($executionTime > 1.0) { // Queries que demoram mais de 1 segundo
            self::warning("Slow query detected: {$executionTime}s", [
                'query' => $query,
                'params' => $params
            ]);
        } else {
            self::debug("Query executed: {$executionTime}s", [
                'query' => substr($query, 0, 100) . '...',
                'params' => $params
            ]);
        }
    }
}
