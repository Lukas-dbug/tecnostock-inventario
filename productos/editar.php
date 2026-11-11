<?php
// productos/editar.php - Modificación de un producto (RF-05)
require __DIR__ . '/../includes/auth.php';
requerir_login();
require __DIR__ . '/../config/conexion.php';
require __DIR__ . '/../includes/productos_funciones.php';

// Producto a editar (el id viene por la URL)
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM productos WHERE id_producto = :id');
$stmt->execute([':id' => $id]);
$producto = $stmt->fetch();

if (!$producto) {
    $_SESSION['flash'] = ['tipo' => 'danger', 'mensaje' => 'El producto no existe.'];
    header('Location: index.php');
    exit;
}
if ((int) $producto['estado'] !== 1) {
    $_SESSION['flash'] = ['tipo' => 'warning', 'mensaje' => 'No se puede editar un producto inactivo.'];
    header('Location: index.php');
    exit;
}

$categorias = obtener_categorias($pdo);
$errores    = [];
$datos      = [
    'codigo'       => $producto['codigo'],
    'nombre'       => $producto['nombre'],
    'descripcion'  => $producto['descripcion'] ?? '',
    'id_categoria' => $producto['id_categoria'],
    'precio'       => $producto['precio'],
    'stock_actual' => $producto['stock_actual'],
    'stock_minimo' => $producto['stock_minimo'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // El código no se modifica: lo que llegue por POST se ignora
    $campos = ['nombre', 'descripcion', 'id_categoria', 'precio', 'stock_actual', 'stock_minimo'];
    foreach ($campos as $campo) {
        $datos[$campo] = trim($_POST[$campo] ?? '');
    }

    $errores = validar_producto($pdo, $datos, $id);

    if (!$errores) {
        try {
            $pdo->beginTransaction();

            // Se lee el stock vigente bloqueando la fila, por si cambió mientras se editaba
            $stmt = $pdo->prepare(
                'SELECT stock_actual FROM productos
                  WHERE id_producto = :id AND estado = 1 FOR UPDATE'
            );
            $stmt->execute([':id' => $id]);
            $stockAnterior = $stmt->fetchColumn();

            if ($stockAnterior === false) {
                $pdo->rollBack();
                $_SESSION['flash'] = ['tipo' => 'danger', 'mensaje' => 'El producto ya no está disponible.'];
                header('Location: index.php');
                exit;
            }

            $stmt = $pdo->prepare(
                'UPDATE productos
                    SET nombre = :nombre, descripcion = :descripcion, precio = :precio,
                        stock_actual = :stock_actual, stock_minimo = :stock_minimo,
                        id_categoria = :id_categoria
                  WHERE id_producto = :id AND estado = 1'
            );
            $stmt->execute([
                ':nombre'       => $datos['nombre'],
                ':descripcion'  => $datos['descripcion'] !== '' ? $datos['descripcion'] : null,
                ':precio'       => $datos['precio'],
                ':stock_actual' => (int) $datos['stock_actual'],
                ':stock_minimo' => (int) $datos['stock_minimo'],
                ':id_categoria' => (int) $datos['id_categoria'],
                ':id'           => $id,
            ]);

            // Si cambió el stock, se deja constancia como movimiento de ajuste (RN-03)
            $diferencia = (int) $datos['stock_actual'] - (int) $stockAnterior;
            if ($diferencia !== 0) {
                $stmt = $pdo->prepare(
                    'INSERT INTO movimientos (tipo, cantidad, observacion, id_producto, id_usuario)
                     VALUES (:tipo, :cantidad, :observacion, :id_producto, :id_usuario)'
                );
                $stmt->execute([
                    ':tipo'        => $diferencia > 0 ? 'ENTRADA' : 'SALIDA',
                    ':cantidad'    => abs($diferencia),
                    ':observacion' => 'Ajuste manual de stock',
                    ':id_producto' => $id,
                    ':id_usuario'  => (int) $_SESSION['id_usuario'],
                ]);
            }

            $pdo->commit();
            $_SESSION['flash'] = ['tipo' => 'success', 'mensaje' => 'Producto actualizado correctamente.'];
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('Error al actualizar producto: ' . $e->getMessage());
            $errores['general'] = 'No se pudo guardar los cambios. Intenta nuevamente.';
        }
    }
}

$titulo = 'Editar producto';
require __DIR__ . '/../includes/header.php';
?>

<h1 class="h4 mb-3">Editar producto</h1>

<?php if (isset($errores['general'])): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($errores['general']) ?></div>
<?php endif; ?>

<?php $esEdicion = true; require __DIR__ . '/_form.php'; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>