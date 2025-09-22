<?php
/**
 * Classe de Gerenciamento de Banco de Dados
 * Implementa padrão Singleton e prepared statements seguros
 */
class Database
{
    private static $instance = null;
    private $connection;
    private $transactionLevel = 0;
    
    private function __construct()
    {
        $this->connect();
    }
    
    /**
     * Obtém instância única da classe (Singleton)
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Estabelece conexão com o banco
     */
    private function connect()
    {
        try {
            $config = Config::get('database');
            
            // Verifica se a configuração é válida
            if (!is_array($config)) {
                throw new Exception('Configuração de banco inválida');
            }
            
            $host = $config['host'] ?? 'localhost';
            $dbname = $config['dbname'] ?? '';
            $username = $config['username'] ?? '';
            $password = $config['password'] ?? '';
            $charset = $config['charset'] ?? 'utf8mb4';
            
            if (empty($dbname)) {
                throw new Exception('Nome do banco não configurado');
            }
            
            $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            // Adiciona opções personalizadas se existirem
            if (isset($config['options']) && is_array($config['options'])) {
                $options = array_merge($options, $config['options']);
            }
            
            $this->connection = new PDO($dsn, $username, $password, $options);
            
            // Log apenas se Logger estiver disponível
            if (class_exists('Logger')) {
                Logger::info('Conexão com banco de dados estabelecida');
            }
        } catch (PDOException $e) {
            // Log apenas se Logger estiver disponível
            if (class_exists('Logger')) {
                Logger::error('Erro na conexão com banco: ' . $e->getMessage());
            }
            throw new Exception('Erro na conexão com o banco de dados: ' . $e->getMessage());
        } catch (Exception $e) {
            // Log apenas se Logger estiver disponível
            if (class_exists('Logger')) {
                Logger::error('Erro na configuração do banco: ' . $e->getMessage());
            }
            throw new Exception('Erro na configuração do banco: ' . $e->getMessage());
        }
    }
    
    /**
     * Obtém a conexão PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }
    
    /**
     * Executa uma query SELECT
     */
    public function select($query, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Logger::error('Erro na query SELECT: ' . $e->getMessage() . ' | Query: ' . $query);
            throw new Exception('Erro ao executar consulta');
        }
    }
    
    /**
     * Executa uma query SELECT retornando apenas um registro
     */
    public function selectOne($query, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            Logger::error('Erro na query SELECT ONE: ' . $e->getMessage() . ' | Query: ' . $query);
            throw new Exception('Erro ao executar consulta');
        }
    }
    
    /**
     * Executa uma query INSERT
     */
    public function insert($query, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($query);
            $result = $stmt->execute($params);
            
            if ($result) {
                Logger::info('INSERT executado com sucesso');
                return $this->connection->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            Logger::error('Erro na query INSERT: ' . $e->getMessage() . ' | Query: ' . $query);
            throw new Exception('Erro ao inserir dados');
        }
    }
    
    /**
     * Executa uma query UPDATE
     */
    public function update($query, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($query);
            $result = $stmt->execute($params);
            
            if ($result) {
                Logger::info('UPDATE executado com sucesso. Linhas afetadas: ' . $stmt->rowCount());
                return $stmt->rowCount();
            }
            return false;
        } catch (PDOException $e) {
            Logger::error('Erro na query UPDATE: ' . $e->getMessage() . ' | Query: ' . $query);
            throw new Exception('Erro ao atualizar dados');
        }
    }
    
    /**
     * Executa uma query DELETE
     */
    public function delete($query, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($query);
            $result = $stmt->execute($params);
            
            if ($result) {
                Logger::info('DELETE executado com sucesso. Linhas afetadas: ' . $stmt->rowCount());
                return $stmt->rowCount();
            }
            return false;
        } catch (PDOException $e) {
            Logger::error('Erro na query DELETE: ' . $e->getMessage() . ' | Query: ' . $query);
            throw new Exception('Erro ao deletar dados');
        }
    }
    
    /**
     * Inicia uma transação
     */
    public function beginTransaction()
    {
        if ($this->transactionLevel === 0) {
            $this->connection->beginTransaction();
        }
        $this->transactionLevel++;
        Logger::info('Transação iniciada. Nível: ' . $this->transactionLevel);
    }
    
    /**
     * Confirma uma transação
     */
    public function commit()
    {
        $this->transactionLevel--;
        if ($this->transactionLevel === 0) {
            $this->connection->commit();
            Logger::info('Transação confirmada');
        }
    }
    
    /**
     * Desfaz uma transação
     */
    public function rollback()
    {
        if ($this->transactionLevel > 0) {
            $this->connection->rollback();
            $this->transactionLevel = 0;
            Logger::info('Transação desfeita');
        }
    }
    
    /**
     * Executa query personalizada
     */
    public function query($query, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            Logger::error('Erro na query personalizada: ' . $e->getMessage() . ' | Query: ' . $query);
            throw new Exception('Erro ao executar query');
        }
    }
    
    /**
     * Verifica se uma tabela existe
     */
    public function tableExists($tableName)
    {
        try {
            $query = "SHOW TABLES LIKE :table";
            $result = $this->selectOne($query, ['table' => $tableName]);
            return !empty($result);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Obtém informações sobre colunas de uma tabela
     */
    public function getTableColumns($tableName)
    {
        try {
            $query = "DESCRIBE `{$tableName}`";
            return $this->select($query);
        } catch (Exception $e) {
            Logger::error('Erro ao obter colunas da tabela: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Previne clonagem da instância
     */
    private function __clone() {}
    
    /**
     * Previne deserialização da instância
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}
