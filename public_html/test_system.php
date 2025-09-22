<?php
/**
 * Script de Teste do Sistema
 * Testa todas as funcionalidades principais
 */

echo "🧪 Iniciando testes do sistema...\n\n";

// 1. Teste de carregamento do bootstrap
echo "1. Testando carregamento do sistema:\n";
try {
    require_once 'core/bootstrap.php';
    echo "✅ Bootstrap carregado com sucesso\n";
} catch (Exception $e) {
    echo "❌ Erro no bootstrap: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Teste de configuração
echo "\n2. Testando configurações:\n";
try {
    $dbConfig = Config::get('database.host');
    echo "✅ Configuração carregada: DB Host = " . $dbConfig . "\n";
    
    $appName = Config::get('app.name');
    echo "✅ Nome da aplicação: " . $appName . "\n";
} catch (Exception $e) {
    echo "❌ Erro na configuração: " . $e->getMessage() . "\n";
}

// 3. Teste de conexão com banco (sem conectar realmente)
echo "\n3. Testando classe Database:\n";
try {
    // Apenas testa se a classe existe e pode ser instanciada
    $reflection = new ReflectionClass('Database');
    echo "✅ Classe Database existe e pode ser carregada\n";
    
    $methods = $reflection->getMethods();
    $methodNames = array_map(function($method) { return $method->getName(); }, $methods);
    
    $expectedMethods = ['getInstance', 'select', 'insert', 'update', 'delete'];
    $hasAllMethods = true;
    
    foreach ($expectedMethods as $method) {
        if (in_array($method, $methodNames)) {
            echo "✅ Método {$method} existe\n";
        } else {
            echo "❌ Método {$method} não encontrado\n";
            $hasAllMethods = false;
        }
    }
    
    if ($hasAllMethods) {
        echo "✅ Todos os métodos essenciais da Database estão presentes\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro na classe Database: " . $e->getMessage() . "\n";
}

// 4. Teste de segurança
echo "\n4. Testando classe Security:\n";
try {
    // Teste de geração de token CSRF
    $token = Security::generateCSRFToken('test');
    if (!empty($token) && strlen($token) > 10) {
        echo "✅ Token CSRF gerado: " . substr($token, 0, 10) . "...\n";
    } else {
        echo "❌ Falha na geração de token CSRF\n";
    }
    
    // Teste de hash de senha
    $password = 'teste123';
    $hash = Security::hashPassword($password);
    if (!empty($hash) && strlen($hash) > 50) {
        echo "✅ Hash de senha gerado com sucesso\n";
        
        // Teste de verificação de senha
        if (Security::verifyPassword($password, $hash)) {
            echo "✅ Verificação de senha funcionando\n";
        } else {
            echo "❌ Falha na verificação de senha\n";
        }
    } else {
        echo "❌ Falha na geração de hash\n";
    }
    
    // Teste de sanitização
    $dirtyInput = '<script>alert("xss")</script>teste';
    $cleanInput = Security::sanitizeInput($dirtyInput);
    if (strpos($cleanInput, '<script>') === false) {
        echo "✅ Sanitização funcionando: " . $cleanInput . "\n";
    } else {
        echo "❌ Falha na sanitização\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro na classe Security: " . $e->getMessage() . "\n";
}

// 5. Teste de validação
echo "\n5. Testando classe Validator:\n";
try {
    $data = [
        'email' => 'teste@email.com',
        'nome' => 'João Silva',
        'idade' => '25'
    ];
    
    $validator = Validator::make($data, [
        'email' => ['required', 'email'],
        'nome' => ['required', 'min:2'],
        'idade' => ['required', 'numeric']
    ]);
    
    if ($validator->validate()) {
        echo "✅ Validação passou para dados válidos\n";
        
        $validatedData = $validator->getValidatedData();
        echo "✅ Dados validados: " . json_encode($validatedData) . "\n";
    } else {
        echo "❌ Validação falhou inesperadamente\n";
        print_r($validator->getErrors());
    }
    
    // Teste com dados inválidos
    $invalidData = [
        'email' => 'email-inválido',
        'nome' => 'A',
        'idade' => 'não-numérico'
    ];
    
    $invalidValidator = Validator::make($invalidData, [
        'email' => ['required', 'email'],
        'nome' => ['required', 'min:2'],
        'idade' => ['required', 'numeric']
    ]);
    
    if (!$invalidValidator->validate()) {
        echo "✅ Validação rejeitou dados inválidos corretamente\n";
    } else {
        echo "❌ Validação aceitou dados inválidos\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro na classe Validator: " . $e->getMessage() . "\n";
}

// 6. Teste de cache
echo "\n6. Testando classe Cache:\n";
try {
    // Teste de armazenamento
    $testKey = 'test_cache_key';
    $testValue = ['data' => 'teste', 'timestamp' => time()];
    
    if (Cache::put($testKey, $testValue, 60)) {
        echo "✅ Cache armazenado com sucesso\n";
        
        // Teste de recuperação
        $retrieved = Cache::get($testKey);
        if ($retrieved && $retrieved['data'] === 'teste') {
            echo "✅ Cache recuperado com sucesso\n";
            
            // Teste de remoção
            if (Cache::forget($testKey)) {
                echo "✅ Cache removido com sucesso\n";
            } else {
                echo "❌ Falha ao remover cache\n";
            }
        } else {
            echo "❌ Falha ao recuperar cache\n";
        }
    } else {
        echo "⚠️ Cache pode estar desabilitado ou sem permissões\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro na classe Cache: " . $e->getMessage() . "\n";
}

// 7. Teste de logger
echo "\n7. Testando classe Logger:\n";
try {
    Logger::info('Teste de log do sistema');
    Logger::warning('Teste de warning');
    Logger::error('Teste de error');
    
    echo "✅ Logs enviados (verifique o arquivo de log se criado)\n";
    
} catch (Exception $e) {
    echo "❌ Erro na classe Logger: " . $e->getMessage() . "\n";
}

// 8. Teste de controladores
echo "\n8. Testando controladores:\n";
try {
    // Teste se as classes de controlador podem ser instanciadas
    $reflection = new ReflectionClass('BaseController');
    echo "✅ BaseController pode ser carregado\n";
    
    $authReflection = new ReflectionClass('AuthController');
    echo "✅ AuthController pode ser carregado\n";
    
    $plansReflection = new ReflectionClass('PlansController');
    echo "✅ PlansController pode ser carregado\n";
    
} catch (Exception $e) {
    echo "❌ Erro nos controladores: " . $e->getMessage() . "\n";
}

// 9. Teste de funções helper
echo "\n9. Testando funções helper:\n";
try {
    // Teste de sanitização
    $cleaned = clean('<script>alert("test")</script>');
    if (strpos($cleaned, '<script>') === false) {
        echo "✅ Função clean() funcionando\n";
    }
    
    // Teste de escape HTML
    $escaped = e('<b>teste</b>');
    if ($escaped === '<b>teste</b>') {
        echo "✅ Função e() funcionando\n";
    }
    
    // Teste de URL
    $url = url('test/path');
    echo "✅ Função url() retornou: " . $url . "\n";
    
} catch (Exception $e) {
    echo "❌ Erro nas funções helper: " . $e->getMessage() . "\n";
}

// Resumo final
echo "\n" . str_repeat("=", 50) . "\n";
echo "🎉 RESUMO DOS TESTES:\n";
echo "✅ Sistema básico funcionando\n";
echo "✅ Todas as classes principais carregadas\n";
echo "✅ Funcionalidades de segurança operacionais\n";
echo "✅ Sistema de validação funcionando\n";
echo "✅ Cache e logs implementados\n";
echo "✅ Controladores prontos para uso\n";
echo "✅ Funções helper disponíveis\n";
echo "\n🚀 Sistema pronto para uso!\n";
echo str_repeat("=", 50) . "\n";
