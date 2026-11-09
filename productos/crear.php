<?php
// productos/crear.php - Registro de un producto nuevo (RF-02)
require __DIR__ . '/../includes/auth.php';
requerir_login();
require __DIR__ . '/../config/conexion.php';
require __DIR__ . '/../includes/productos_funciones.php';

$categorias = obtener_categorias($pdo);
$errores    = [];
$datos      = [
    'codigo' => '', 'nombre' => '', 'descripcion' => '', 'id_categoria' => '',
    'precio' => '', 'stock_actual' => '0', 'stock_minimo' => '0',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($datos as $campo => $valor) {
        $datos[$campo] = trim($_POST[$campo] ?? '');
    }

    $errores = validar_producto($pdo, $datos);

    if (!$errores) {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO productos
                   (codigo, nombre, descripcion, precio, stock_actual, stock_minimo, estado, id_categoria)
                 VALUES
                   (:codigo, :nombre, :descripcion, :precio, :stock_actual, :stock_minimo, 1, :id_categoria)'
            );
            $stmt->execute([
                ':codigo'       => $datos['codigo'],
                ':nombre'       => $datos['nombre'],
                ':descripcion'  => $datos['descripcion'] !== '' ? $datos['descripcion'] : null,
                ':precio'       => $datos['precio'],
                ':stock_actual' => (int) $datos['stock_actual'],
                ':stock_minimo' => (int) $datos['stock_minimo'],
                ':id_categoria' => (int) $datos['id_categoria'],
            ]);
            $_SESSION['flash'] = ['tipo' => 'success', 'mensaje' => 'Producto creado correctamente.'];
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                // Restricción UNIQUE de la base de datos (código repetido)
                $errores['codigo'] = 'Ya existe un producto con ese código.';
            } else {
                error_log('Error al crear producto: ' . $e->getMessage());
                $errores['general'] = 'No se pudo guardar el producto. Intenta nuevamente.';
            }
        }
    }
}

$titulo = 'Nuevo producto';
require __DIR__ . '/../includes/header.php';
?>

<h1 class="h4 mb-3">Nuevo producto</h1>

<?php if (isset($errores['general'])): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($errores['general']) ?></div>
<?php endif; ?>

<?php $esEdicion = false; require __DIR__ . '/_form.php'; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>