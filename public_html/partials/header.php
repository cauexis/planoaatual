<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$page_title = $page_title ?? 'Administradora Plano A';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <meta name="description" content="<?= isset($meta_description) ? htmlspecialchars($meta_description) : 'Encontre o plano de saúde ideal com a Top Prime Seguros.' ?>">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="main-header-duplo">
        <div class="header-top-bar">
            <div class="container-header">
                 <div class="top-bar-contact">
                    <span><i class="fas fa-phone"></i> +55 69 9272-5666</span>
                    <span><i class="fas fa-envelope"></i> contato@admplanoa.com</span>
                </div>
                <div class="top-bar-social">
                    <a href="https://www.instagram.com/planoa_saude" target="_blank"><img src="img/icons/insta.png" alt="Instagram"></a>
                    <a href="https://wa.me/556992725666?text=Olá! Gostaria de mais informações sobre os planos." target="_blank"><img src="img/icons/whats.png" alt="WhatsApp"></a>
                </div>
            </div>
        </div>
        <div class="header-main">
            <div class="container-header">
                <a href="index.php" class="header-logo-link">
                    <img src="img/planoa.png" alt="Logo PLANO A" class="logo">
                </a>
                <nav class="main-nav">
                    <a href="quem-somos.php">Quem Somos</a>
                    <a href="planos.php">Planos</a>
                    <a href="blog.php">Blog</a>
                    <a href="rede_credenciada.php">Rede Credenciada</a>
                    <a href="contato.php">Contato</a>
                </nav>
                <div class="header-actions">
                    <a href="login.php" class="btn-header primary">Área do Cliente</a>
                </div>
                <button class="menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
             <div class="mobile-nav-container">
                <nav>
                    <a href="quem-somos.php">Quem Somos</a>
                    <a href="planos.php">Planos</a>
                    <a href="blog.php">Blog</a>
                    <a href="rede_credenciada.php">Rede Credenciada</a>
                    <a href="contato.php">Contato</a>
                    <a href="login.php">Área do Cliente</a>
                </nav>
            </div>
        </div>
    </header>