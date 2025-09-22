<?php
/**
 * Classe de Configuração do Sistema
 * Gerencia todas as configurações da aplicação
 */
class Config
{
    private static $config = [
        // Configurações do Banco de Dados
        'database' => [
            'host' => 'localhost',
            'dbname' => 'u668533246_planoa',
            'username' => 'u668533246_planoa',
            'password' => '9H2fg>w^',
            'charset' => 'utf8mb4',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        ],
        
        // Configurações de Segurança
        'security' => [
            'session_name' => 'PLANOA_SESSION',
            'csrf_token_name' => '_token',
            'password_min_length' => 8,
            'session_timeout' => 3600, // 1 hora
            'max_login_attempts' => 5,
            'lockout_time' => 900, // 15 minutos
        ],
        
        // Configurações da Aplicação
        'app' => [
            'name' => 'Plano A',
            'version' => '2.0.0',
            'timezone' => 'America/Manaus',
            'debug' => true,
            'base_url' => '',
            'upload_path' => 'uploads/',
            'max_file_size' => 5242880, // 5MB
        ],
        
        // Configurações de Email
        'email' => [
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => 587,
            'smtp_username' => '',
            'smtp_password' => '',
            'from_email' => 'cadastro@admplanoa.com.br',
            'from_name' => 'Plano A',
        ],
        
        // Configurações de Cache
        'cache' => [
            'enabled' => true,
            'default_ttl' => 3600,
            'path' => 'cache/',
        ],
        
        // Configurações de Log
        'logging' => [
            'enabled' => true,
            'level' => 'INFO',
            'path' => 'logs/',
            'max_files' => 30,
        ]
    ];
    
    /**
     * Obtém uma configuração
     */
    public static function get($key, $default = null)
    {
        $keys = explode('.', $key);
        $value = self::$config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
    
    /**
     * Define uma configuração
     */
    public static function set($key, $value)
    {
        $keys = explode('.', $key);
        $config = &self::$config;
        
        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }
        
        $config = $value;
    }
    
    /**
     * Carrega configurações de arquivo
     */
    public static function loadFromFile($file)
    {
        if (file_exists($file)) {
            $fileConfig = include $file;
            if (is_array($fileConfig)) {
                self::$config = array_merge_recursive(self::$config, $fileConfig);
            }
        }
    }
}
