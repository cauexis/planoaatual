<?php
// logout.php (Versão Corrigida e Segura)

session_start(); // PASSO 1: Inicia a sessão para poder acessá-la

// PASSO 2: Limpa todas as variáveis da sessão
$_SESSION = array();

// PASSO 3: Destrói a sessão no servidor
session_destroy();

// PASSO 4: Redireciona para a página de login
header("Location: login.php");
exit();
?>