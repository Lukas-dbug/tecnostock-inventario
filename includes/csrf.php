<?php
// includes/csrf.php - Protección contra CSRF (formularios enviados desde otra página)

// Token único por sesión
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

// Campo oculto que se agrega a cada formulario
function csrf_campo(): string
{
    return '<input type="hidden" name="csrf" value="' . htmlspecialchars(csrf_token()) . '">';
}

// Comprueba que el token recibido por POST coincida con el de la sesión
function csrf_validar(): bool
{
    $enviado = $_POST['csrf'] ?? '';
    return isset($_SESSION['csrf'])
        && is_string($enviado)
        && hash_equals($_SESSION['csrf'], $enviado);
}