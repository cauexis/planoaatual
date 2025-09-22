<?php
session_start(); // É preciso iniciar a sessão para poder destruí-la
session_unset();
session_destroy();
header("Location: admin_login.php");
exit();
?>