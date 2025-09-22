<?php
/**
 * Configurações Personalizadas do Plano A
 * SOBRESCREVE as configurações padrão conforme necessário
 */

return [
    // Configurações específicas do Plano A
    'app' => [
        'name' => 'Plano A',
        'version' => '2.0.0',
        'timezone' => 'America/Manaus',
        'debug' => false, // Mude para false em produção
        'base_url' => '', // Defina a URL base do seu site
        'upload_path' => 'uploads/',
        'max_file_size' => 5242880, // 5MB
    ],
    
    // Configurações de banco específicas (se diferentes)
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
    
    // Configurações de segurança do Plano A
    'security' => [
        'session_name' => 'PLANOA_SESSION',
        'csrf_token_name' => '_token',
        'password_min_length' => 8,
        'session_timeout' => 7200, // 2 horas para área do cliente
        'max_login_attempts' => 5,
        'lockout_time' => 900, // 15 minutos
    ],
    
    // Configurações de email (configure com seus dados)
    'email' => [
        'smtp_host' => 'smtp.gmail.com',
        'smtp_port' => 587,
        'smtp_username' => '', // Seu email
        'smtp_password' => '', // Sua senha de app
        'from_email' => 'noreply@planoa.com.br',
        'from_name' => 'Plano A',
    ],
    
    // Configurações de cache otimizadas para planos de saúde
    'cache' => [
        'enabled' => true,
        'default_ttl' => 3600, // 1 hora
        'path' => 'cache/',
        'plans_ttl' => 1800, // 30 minutos para planos
        'network_ttl' => 3600, // 1 hora para rede credenciada
        'user_ttl' => 300, // 5 minutos para dados do usuário
    ],
    
    // Configurações de log
    'logging' => [
        'enabled' => true,
        'level' => 'INFO', // DEBUG, INFO, WARNING, ERROR
        'path' => 'logs/',
        'max_files' => 30,
        'log_queries' => false, // true para logar queries SQL
        'log_user_activity' => true,
    ],
    
    // Configurações específicas do negócio
    'business' => [
        'company_name' => 'Plano A',
        'company_phone' => '0800-020-2149',
        'company_whatsapp' => '+55 69 9272-5666',
        'company_email' => 'contato@planoa.com.br',
        'company_address' => 'Manaus, Amazonas',
        
        // Configurações de planos
        'max_plans_comparison' => 4,
        'default_plans_per_page' => 12,
        'featured_operators' => ['Unimed', 'Bradesco Saúde', 'SulAmérica'],
        
        // Configurações de boletos
        'boleto_due_days' => 10, // Dias para vencimento
        'boleto_late_fee' => 2.0, // Taxa de juros (%)
        
        // Configurações da rede credenciada
        'network_search_radius' => 50, // km
        'default_city' => 'Manaus',
        'default_state' => 'Amazonas',
    ],
    
    // Configurações de integração (APIs externas)
    'integrations' => [
        'ans_api' => [
            'enabled' => false,
            'api_key' => '',
            'base_url' => 'https://www.ans.gov.br/anstabnet/cgi-bin/dh',
        ],
        
        'cep_api' => [
            'enabled' => true,
            'provider' => 'viacep', // viacep, postmon
            'timeout' => 5,
        ],
        
        'payment_gateway' => [
            'enabled' => false,
            'provider' => '', // pagseguro, mercadopago, etc.
            'sandbox' => true,
            'api_key' => '',
            'secret_key' => '',
        ],
    ],
    
    // Configurações de SEO
    'seo' => [
        'default_title' => 'Plano A - Sua saúde em primeiro lugar',
        'default_description' => 'Encontre o plano de saúde ideal com a Plano A. Ampla rede credenciada, preços acessíveis e atendimento humanizado.',
        'default_keywords' => 'plano de saúde, convênio médico, Manaus, Amazonas, Unimed, Bradesco Saúde',
        'google_analytics' => '', // ID do Google Analytics
        'google_tag_manager' => '', // ID do GTM
        'facebook_pixel' => '', // ID do Facebook Pixel
    ],
    
    // Configurações de performance
    'performance' => [
        'enable_gzip' => true,
        'enable_browser_cache' => true,
        'minify_html' => false,
        'minify_css' => false,
        'minify_js' => false,
        'lazy_load_images' => true,
    ],
    
    // Configurações de backup (para implementação futura)
    'backup' => [
        'enabled' => false,
        'frequency' => 'daily', // daily, weekly, monthly
        'retention_days' => 30,
        'include_uploads' => true,
        'storage_path' => 'backups/',
    ],
    
    // Configurações de notificações
    'notifications' => [
        'email_enabled' => true,
        'sms_enabled' => false,
        'push_enabled' => false,
        
        // Templates de email
        'email_templates' => [
            'welcome' => 'emails/welcome.php',
            'password_reset' => 'emails/password_reset.php',
            'plan_confirmation' => 'emails/plan_confirmation.php',
        ],
    ],
    
    // Configurações de manutenção
    'maintenance' => [
        'enabled' => false,
        'message' => 'Site em manutenção. Voltamos em breve!',
        'allowed_ips' => ['127.0.0.1'], // IPs que podem acessar durante manutenção
        'end_time' => null, // Timestamp de quando a manutenção termina
    ],
];
