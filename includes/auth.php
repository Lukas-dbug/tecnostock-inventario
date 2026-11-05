<?php
// includes/auth.php - Protege las páginas que requieren sesión iniciada
session_start();

define('BASE_URL', '/tecnostock');

function requerir_login(): void
{
    if (!isset($_SESSION['id_usuario'])) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}