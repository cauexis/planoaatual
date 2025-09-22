<?php
/**
 * Sistema de Cache Simples
 * Melhora a performance armazenando dados temporariamente
 */
class Cache
{
    private static $cachePath;
    
    /**
     * Inicializa o sistema de cache
     */
    public static function init()
    {
        self::$cachePath = Config::get('cache.path', 'cache/');
        
        if (!is_dir(self::$cachePath)) {
            mkdir(self::$cachePath, 0755, true);
        }
    }
    
    /**
     * Armazena um valor no cache
     */
    public static function put($key, $value, $ttl = null)
    {
        if (!Config::get('cache.enabled', true)) {
            return false;
        }
        
        self::init();
        
        $ttl = $ttl ?? Config::get('cache.default_ttl', 3600);
        $expiry = time() + $ttl;
        
        $data = [
            'value' => $value,
            'expiry' => $expiry,
            'created' => time()
        ];
        
        $filename = self::getFilename($key);
        $success = file_put_contents($filename, serialize($data), LOCK_EX) !== false;
        
        if ($success) {
            Logger::debug("Cache stored: {$key} (TTL: {$ttl}s)");
        }
        
        return $success;
    }
    
    /**
     * Obtém um valor do cache
     */
    public static function get($key, $default = null)
    {
        if (!Config::get('cache.enabled', true)) {
            return $default;
        }
        
        self::init();
        
        $filename = self::getFilename($key);
        
        if (!file_exists($filename)) {
            return $default;
        }
        
        $content = file_get_contents($filename);
        if ($content === false) {
            return $default;
        }
        
        $data = unserialize($content);
        if ($data === false) {
            // Cache corrompido, remove o arquivo
            unlink($filename);
            return $default;
        }
        
        // Verifica se expirou
        if (time() > $data['expiry']) {
            unlink($filename);
            Logger::debug("Cache expired: {$key}");
            return $default;
        }
        
        Logger::debug("Cache hit: {$key}");
        return $data['value'];
    }
    
    /**
     * Verifica se uma chave existe no cache
     */
    public static function has($key)
    {
        return self::get($key) !== null;
    }
    
    /**
     * Remove um item do cache
     */
    public static function forget($key)
    {
        self::init();
        
        $filename = self::getFilename($key);
        
        if (file_exists($filename)) {
            $success = unlink($filename);
            if ($success) {
                Logger::debug("Cache removed: {$key}");
            }
            return $success;
        }
        
        return true;
    }
    
    /**
     * Limpa todo o cache
     */
    public static function flush()
    {
        self::init();
        
        $files = glob(self::$cachePath . '*.cache');
        $count = 0;
        
        foreach ($files as $file) {
            if (unlink($file)) {
                $count++;
            }
        }
        
        Logger::info("Cache flushed: {$count} files removed");
        return $count;
    }
    
    /**
     * Obtém ou armazena um valor (remember pattern)
     */
    public static function remember($key, $callback, $ttl = null)
    {
        $value = self::get($key);
        
        if ($value !== null) {
            return $value;
        }
        
        $value = $callback();
        self::put($key, $value, $ttl);
        
        return $value;
    }
    
    /**
     * Limpa cache expirado
     */
    public static function cleanExpired()
    {
        self::init();
        
        $files = glob(self::$cachePath . '*.cache');
        $cleaned = 0;
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            if ($content === false) continue;
            
            $data = unserialize($content);
            if ($data === false || time() > $data['expiry']) {
                if (unlink($file)) {
                    $cleaned++;
                }
            }
        }
        
        if ($cleaned > 0) {
            Logger::info("Cleaned {$cleaned} expired cache files");
        }
        
        return $cleaned;
    }
    
    /**
     * Obtém estatísticas do cache
     */
    public static function getStats()
    {
        self::init();
        
        $files = glob(self::$cachePath . '*.cache');
        $totalSize = 0;
        $validFiles = 0;
        $expiredFiles = 0;
        
        foreach ($files as $file) {
            $size = filesize($file);
            $totalSize += $size;
            
            $content = file_get_contents($file);
            if ($content === false) continue;
            
            $data = unserialize($content);
            if ($data === false) continue;
            
            if (time() > $data['expiry']) {
                $expiredFiles++;
            } else {
                $validFiles++;
            }
        }
        
        return [
            'total_files' => count($files),
            'valid_files' => $validFiles,
            'expired_files' => $expiredFiles,
            'total_size' => $totalSize,
            'total_size_mb' => round($totalSize / 1024 / 1024, 2)
        ];
    }
    
    /**
     * Gera nome do arquivo de cache
     */
    private static function getFilename($key)
    {
        $hash = md5($key);
        return self::$cachePath . $hash . '.cache';
    }
    
    /**
     * Cache específico para consultas de banco
     */
    public static function query($key, $callback, $ttl = 300)
    {
        return self::remember("query_{$key}", $callback, $ttl);
    }
    
    /**
     * Cache específico para planos
     */
    public static function plans($filters = [], $ttl = 600)
    {
        $key = 'plans_' . md5(serialize($filters));
        
        return self::remember($key, function() use ($filters) {
            $db = Database::getInstance();
            
            $query = "SELECT * FROM planos WHERE is_active = 1";
            $params = [];
            
            if (!empty($filters['operadora'])) {
                $query .= " AND operadora = :operadora";
                $params['operadora'] = $filters['operadora'];
            }
            
            if (!empty($filters['tipo_plano'])) {
                $query .= " AND tipo_plano = :tipo_plano";
                $params['tipo_plano'] = $filters['tipo_plano'];
            }
            
            $query .= " ORDER BY nome_plano";
            
            return $db->select($query, $params);
        }, $ttl);
    }
    
    /**
     * Cache específico para rede credenciada
     */
    public static function network($filters = [], $ttl = 1800)
    {
        $key = 'network_' . md5(serialize($filters));
        
        return self::remember($key, function() use ($filters) {
            $db = Database::getInstance();
            
            $query = "SELECT * FROM rede_credenciada WHERE 1=1";
            $params = [];
            
            if (!empty($filters['city'])) {
                $query .= " AND city LIKE :city";
                $params['city'] = '%' . $filters['city'] . '%';
            }
            
            if (!empty($filters['type'])) {
                $query .= " AND type = :type";
                $params['type'] = $filters['type'];
            }
            
            $query .= " ORDER BY name";
            
            return $db->select($query, $params);
        }, $ttl);
    }
    
    /**
     * Invalida cache relacionado a planos
     */
    public static function invalidatePlans()
    {
        self::init();
        
        $files = glob(self::$cachePath . '*plans*.cache');
        $count = 0;
        
        foreach ($files as $file) {
            if (unlink($file)) {
                $count++;
            }
        }
        
        Logger::info("Invalidated {$count} plan cache files");
        return $count;
    }
    
    /**
     * Invalida cache relacionado à rede credenciada
     */
    public static function invalidateNetwork()
    {
        self::init();
        
        $files = glob(self::$cachePath . '*network*.cache');
        $count = 0;
        
        foreach ($files as $file) {
            if (unlink($file)) {
                $count++;
            }
        }
        
        Logger::info("Invalidated {$count} network cache files");
        return $count;
    }
}
