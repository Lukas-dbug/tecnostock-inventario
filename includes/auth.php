<?php
// includes/auth.php - Protege las páginas que requieren sesión iniciada
session_start();

function requerir_login(): void
{
    if (!isset($_SESSION['id_usuario'])) {
        header('Location: /tecnostock/login.php');
        exit;
    }
}
