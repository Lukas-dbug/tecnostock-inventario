<?php
// logout.php - Cierra la sesión
session_start();
$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;