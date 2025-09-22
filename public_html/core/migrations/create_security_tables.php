<?php
/**
 * Migração: Cria tabelas de segurança
 * SEGURO: Não altera dados existentes, apenas adiciona novas tabelas
 */

require_once '../bootstrap.php';

try {
    $db = Database::getInstance();
    
    echo "Iniciando migração de segurança...\n";
    
    // 1. Tabela de tentativas de login
    if (!$db->tableExists('login_attempts')) {
        $sql = "
            CREATE TABLE login_attempts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                identifier VARCHAR(255) NOT NULL COMMENT 'Email ou username',
                user_id INT NULL COMMENT 'ID do usuário se encontrado',
                success BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Se o login foi bem-sucedido',
                ip_address VARCHAR(45) NOT NULL COMMENT 'IP do cliente',
                user_agent TEXT COMMENT 'User agent do navegador',
                attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Quando foi a tentativa',
                
                INDEX idx_identifier (identifier),
                INDEX idx_attempted_at (attempted_at),
                INDEX idx_ip_address (ip_address)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            COMMENT='Registra tentativas de login para segurança'
        ";
        
        $db->query($sql);
        echo "✓ Tabela 'login_attempts' criada\n";
    } else {
        echo "- Tabela 'login_attempts' já existe\n";
    }
    
    // 2. Tabela de tokens de recuperação de senha
    if (!$db->tableExists('password_resets')) {
        $sql = "
            CREATE TABLE password_resets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL COMMENT 'ID do usuário',
                token VARCHAR(255) NOT NULL COMMENT 'Token de recuperação',
                expires_at TIMESTAMP NOT NULL COMMENT 'Quando o token expira',
                used_at TIMESTAMP NULL COMMENT 'Quando foi usado (se foi)',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                
                INDEX idx_token (token),
                INDEX idx_user_id (user_id),
                INDEX idx_expires_at (expires_at),
                
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            COMMENT='Tokens para recuperação de senha'
        ";
        
        $db->query($sql);
        echo "✓ Tabela 'password_resets' criada\n";
    } else {
        echo "- Tabela 'password_resets' já existe\n";
    }
    
    // 3. Verifica se a tabela users tem as colunas necessárias
    $userColumns = $db->getTableColumns('users');
    $existingColumns = array_column($userColumns, 'Field');
    
    // Adiciona coluna 'active' se não existir
    if (!in_array('active', $existingColumns)) {
        $db->query("ALTER TABLE users ADD COLUMN active BOOLEAN NOT NULL DEFAULT 1 COMMENT 'Se a conta está ativa'");
        echo "✓ Coluna 'active' adicionada à tabela users\n";
    } else {
        echo "- Coluna 'active' já existe na tabela users\n";
    }
    
    // Adiciona coluna 'role' se não existir
    if (!in_array('role', $existingColumns)) {
        $db->query("ALTER TABLE users ADD COLUMN role VARCHAR(50) NOT NULL DEFAULT 'user' COMMENT 'Papel do usuário (user, admin)'");
        echo "✓ Coluna 'role' adicionada à tabela users\n";
    } else {
        echo "- Coluna 'role' já existe na tabela users\n";
    }
    
    // Adiciona coluna 'email_verified_at' se não existir
    if (!in_array('email_verified_at', $existingColumns)) {
        $db->query("ALTER TABLE users ADD COLUMN email_verified_at TIMESTAMP NULL COMMENT 'Quando o email foi verificado'");
        echo "✓ Coluna 'email_verified_at' adicionada à tabela users\n";
    } else {
        echo "- Coluna 'email_verified_at' já existe na tabela users\n";
    }
    
    // 4. Tabela de logs de atividade (opcional)
    if (!$db->tableExists('activity_logs')) {
        $sql = "
            CREATE TABLE activity_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NULL COMMENT 'ID do usuário (NULL para ações anônimas)',
                action VARCHAR(100) NOT NULL COMMENT 'Ação realizada',
                description TEXT COMMENT 'Descrição detalhada',
                ip_address VARCHAR(45) COMMENT 'IP do cliente',
                user_agent TEXT COMMENT 'User agent',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                
                INDEX idx_user_id (user_id),
                INDEX idx_action (action),
                INDEX idx_created_at (created_at),
                
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            COMMENT='Log de atividades dos usuários'
        ";
        
        $db->query($sql);
        echo "✓ Tabela 'activity_logs' criada\n";
    } else {
        echo "- Tabela 'activity_logs' já existe\n";
    }
    
    // 5. Verifica se existe um usuário admin
    $adminExists = $db->selectOne("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
    
    if (!$adminExists) {
        echo "\n⚠️  ATENÇÃO: Nenhum usuário admin encontrado!\n";
        echo "Deseja criar um usuário admin? (s/n): ";
        $handle = fopen("php://stdin", "r");
        $response = trim(fgets($handle));
        fclose($handle);
        
        if (strtolower($response) === 's') {
            echo "Email do admin: ";
            $handle = fopen("php://stdin", "r");
            $adminEmail = trim(fgets($handle));
            fclose($handle);
            
            echo "Nome do admin: ";
            $handle = fopen("php://stdin", "r");
            $adminName = trim(fgets($handle));
            fclose($handle);
            
            $adminPassword = Security::generateRandomPassword(12);
            $hashedPassword = Security::hashPassword($adminPassword);
            
            $adminId = $db->insert(
                "INSERT INTO users (nome, email, password, role, active, created_at) VALUES (:nome, :email, :password, 'admin', 1, NOW())",
                [
                    'nome' => $adminName,
                    'email' => $adminEmail,
                    'password' => $hashedPassword
                ]
            );
            
            if ($adminId) {
                echo "✓ Usuário admin criado com sucesso!\n";
                echo "Email: {$adminEmail}\n";
                echo "Senha: {$adminPassword}\n";
                echo "⚠️  IMPORTANTE: Anote esta senha, ela não será mostrada novamente!\n";
            }
        }
    } else {
        echo "✓ Usuário admin já existe\n";
    }
    
    echo "\n🎉 Migração concluída com sucesso!\n";
    echo "Todas as melhorias de segurança foram aplicadas.\n";
    echo "Seus dados existentes foram preservados.\n\n";
    
    // Estatísticas
    $stats = [
        'Usuários totais' => $db->selectOne("SELECT COUNT(*) as count FROM users")['count'],
        'Usuários ativos' => $db->selectOne("SELECT COUNT(*) as count FROM users WHERE active = 1")['count'],
        'Administradores' => $db->selectOne("SELECT COUNT(*) as count FROM users WHERE role = 'admin'")['count'],
        'Planos ativos' => $db->selectOne("SELECT COUNT(*) as count FROM planos WHERE is_active = 1")['count'] ?? 0
    ];
    
    echo "📊 Estatísticas do sistema:\n";
    foreach ($stats as $label => $value) {
        echo "   {$label}: {$value}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro na migração: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
