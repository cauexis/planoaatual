<?php
/**
 * EXEMPLO: config/db.php MELHORADO
 * MANTÉM A MESMA FUNCIONALIDADE, ADICIONA SEGURANÇA E PERFORMANCE
 * 
 * Este arquivo mostra como melhorar seu config/db.php existente
 * sem quebrar nada que já funciona
 */

// Carrega o sistema melhorado (OPCIONAL - só se quiser usar as melhorias)
if (file_exists(__DIR__ . '/../core/bootstrap.php')) {
    require_once __DIR__ . '/../core/bootstrap.php';
    
    // Usa a classe Database melhorada
    try {
        $db = Database::getInstance();
        $conn = $db->getConnection(); // Mantém compatibilidade com código existente
        
        Logger::debug('Conexão estabelecida via sistema melhorado');
    } catch (Exception $e) {
        Logger::error('Erro na conexão melhorada: ' . $e->getMessage());
        // Fallback para o sistema original abaixo
        $conn = null;
    }
}

// MANTÉM SEU CÓDIGO ORIGINAL como fallback
if (!isset($conn) || $conn === null) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    $host = 'localhost';
    $db_name = 'u639134460_admplanoa';
    $username = 'u639134460_admplanoa';
    $password = '4~DknWM*g;eW';


    try {
        $conn = new PDO("mysql:host={$host};dbname={$db_name}", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Log apenas se o sistema melhorado estiver disponível
        if (class_exists('Logger')) {
            Logger::debug('Conexão estabelecida via sistema original');
        }
    } catch(PDOException $e) {
        $errorMsg = "Erro na conexão: " . $e->getMessage();
        
        // Log do erro se disponível
        if (class_exists('Logger')) {
            Logger::error($errorMsg);
        } else {
            echo $errorMsg;
        }
    }
}

// FUNCIONALIDADES EXTRAS (só funcionam se o sistema melhorado estiver carregado)
if (class_exists('Database')) {
    /**
     * Função helper para consultas seguras
     * Exemplo de uso: $users = db_select("SELECT * FROM users WHERE active = :active", ['active' => 1]);
     */
    function db_select($query, $params = []) {
        try {
            $db = Database::getInstance();
            return $db->select($query, $params);
        } catch (Exception $e) {
            if (class_exists('Logger')) {
                Logger::error('Erro em db_select: ' . $e->getMessage());
            }
            return [];
        }
    }
    
    /**
     * Função helper para inserções seguras
     * Exemplo: $id = db_insert("INSERT INTO users (nome, email) VALUES (:nome, :email)", ['nome' => 'João', 'email' => 'joao@email.com']);
     */
    function db_insert($query, $params = []) {
        try {
            $db = Database::getInstance();
            return $db->insert($query, $params);
        } catch (Exception $e) {
            if (class_exists('Logger')) {
                Logger::error('Erro em db_insert: ' . $e->getMessage());
            }
            return false;
        }
    }
    
    /**
     * Função helper para atualizações seguras
     */
    function db_update($query, $params = []) {
        try {
            $db = Database::getInstance();
            return $db->update($query, $params);
        } catch (Exception $e) {
            if (class_exists('Logger')) {
                Logger::error('Erro em db_update: ' . $e->getMessage());
            }
            return false;
        }
    }
    
    /**
     * Função helper para buscar um único registro
     */
    function db_find($query, $params = []) {
        try {
            $db = Database::getInstance();
            return $db->selectOne($query, $params);
        } catch (Exception $e) {
            if (class_exists('Logger')) {
                Logger::error('Erro em db_find: ' . $e->getMessage());
            }
            return null;
        }
    }
}

/*
EXEMPLO DE USO NO SEU CÓDIGO EXISTENTE:

// Antes (seu código atual):
$stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

// Depois (com as melhorias, mas mantendo compatibilidade):
if (function_exists('db_find')) {
    // Usa a função melhorada se disponível
    $user = db_find("SELECT * FROM users WHERE email = :email", ['email' => $email]);
} else {
    // Fallback para o código original
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();
}

// Ou simplesmente continue usando seu código atual - ele continuará funcionando!
*/
?>
