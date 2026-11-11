<?php
// productos/desactivar.php - Desactiva un producto (RF-06, RN-05)
// No se elimina físicamente: se cambia su estado para conservar el historial.
require __DIR__ . '/../includes/auth.php';
requerir_login();
require __DIR__ . '/../config/conexion.php';

// Solo se acepta el envío de un formulario (POST), no una dirección escrita a mano
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if ($id) {
    $stmt = $pdo->prepare('UPDATE productos SET estado = 0 WHERE id_producto = :id AND estado = 1');
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() === 1) {
        $_SESSION['flash'] = ['tipo' => 'success',
            'mensaje' => 'Producto desactivado. Su historial se conserva.'];
    } else {
        $_SESSION['flash'] = ['tipo' => 'warning',
            'mensaje' => 'El producto no existe o ya estaba inactivo.'];
    }
} else {
    $_SESSION['flash'] = ['tipo' => 'danger', 'mensaje' => 'Solicitud no válida.'];
}

header('Location: index.php');
exit;