<?php
// config/db.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$db_name = 'u639134460_admplanoa';
$username = 'u639134460_admplanoa';
$password = '4~DknWM*g;eW';

// Nome do arquivo CSV
$csvFile = '10.csv';

// Verificar se o arquivo CSV existe
if (!file_exists($csvFile)) {
    die("Erro: O arquivo CSV '$csvFile' não foi encontrado.");
}

try {
    // Conexão com o banco de dados - CORREÇÃO: usar $db_name em vez de $dbname
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Conexão com o banco de dados estabelecida com sucesso.<br>";

    // Preparar a query de inserção
    $stmt = $pdo->prepare("INSERT INTO beneficiaries (nome_associado, data_nascimento, endereco_email, imported_at) VALUES (?, ?, ?, NOW())");

    // Abrir e ler o arquivo CSV
    if (($handle = fopen($csvFile, "r")) !== FALSE) {
        // Pular a primeira linha (cabeçalho)
        fgetcsv($handle, 1000, ";");
        
        $contador = 0;
        $erros = 0;
        
        echo "Iniciando importação...<br>";
        
        // Ler cada linha do CSV
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Verificar se a linha tem dados
            if (count($data) >= 3) {
                $nome = trim($data[0]);
                $data_nasc = trim($data[1]);
                $email = trim($data[2]);
                
                // Pular linha se estiver vazia
                if (empty($nome) && empty($data_nasc) && empty($email)) {
                    continue;
                }
                
                // Converter formato de data se necessário
                if (strpos($data_nasc, '/') !== false) {
                    $parts = explode('/', $data_nasc);
                    if (count($parts) === 3) {
                        // Formato MM/DD/YYYY para YYYY-MM-DD
                        $data_nasc = $parts[2] . '-' . $parts[0] . '-' . $parts[1];
                    }
                }
                
                try {
                    // Executar a inserção
                    $stmt->execute([$nome, $data_nasc, $email]);
                    $contador++;
                    
                    if ($contador % 10 === 0) {
                        echo "Importados $contador registros...<br>";
                        flush();
                    }
                } catch (PDOException $e) {
                    $erros++;
                    echo "Erro na linha: " . ($contador + $erros) . " - " . $e->getMessage() . "<br>";
                }
            }
        }
        fclose($handle);
        
        echo "<br>Importação concluída!<br>";
        echo "Registros importados com sucesso: $contador<br>";
        echo "Erros encontrados: $erros<br>";
    } else {
        echo "Erro: Não foi possível abrir o arquivo CSV.";
    }
} catch(PDOException $e) {
    echo "Erro na conexão com o banco de dados: " . $e->getMessage() . "<br>";
    echo "Verifique as configurações de conexão.";
}
?>