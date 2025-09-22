<?php
// config/db.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$db_name = 'u639134460_admplanoa'; // O nome do banco de dados que você criou
$username = 'u639134460_admplanoa';
$password = '4~DknWM*g;eW'; // 

try {
    $conn = new PDO("mysql:host={$host};dbname={$db_name}", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}
?>
