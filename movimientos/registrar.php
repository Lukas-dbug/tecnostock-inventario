<?php
// movimientos/registrar.php - Registro de entradas y salidas (RF-07)
require __DIR__ . '/../includes/auth.php';
requerir_login();
require __DIR__ . '/../config/conexion.php';
require __DIR__ . '/../includes/movimientos_funciones.php';

// Solo productos activos
$productos = $pdo->query(
    'SELECT id_producto, codigo, nombre, stock_actual
       FROM productos WHERE estado = 1 ORDER BY nombre'
)->fetchAll();

$errores = [];
$datos   = ['id_producto' => '', 'tipo' => '', 'cantidad' => '', 'observacion' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($datos as $campo => $valor) {
        $datos[$campo] = trim($_POST[$campo] ?? '');
    }

    $errores = validar_movimiento($datos);

    if (!$errores) {
        $res = registrar_movimiento(
            $pdo,
            (int) $datos['id_producto'],
            $datos['tipo'],
            (int) $datos['cantidad'],
            $datos['observacion'] !== '' ? $datos['observacion'] : null,
            (int) $_SESSION['id_usuario']
        );

        if ($res['ok']) {
            $mensaje = 'Movimiento registrado. Stock actual del producto: ' . $res['stock'] . '.';
            $tipoAviso = 'success';
            // RF-08: avisar si el producto quedó bajo el mínimo
            if ($res['stock'] < $res['minimo']) {
                $mensaje .= ' Atención: el producto quedó con stock bajo (mínimo ' . $res['minimo'] . ').';
                $tipoAviso = 'warning';
            }
            $_SESSION['flash'] = ['tipo' => $tipoAviso, 'mensaje' => $mensaje];
            header('Location: index.php');
            exit;
        }
        $errores[$res['campo']] = $res['error'];
    }
} else {
    // Permite llegar con un producto preseleccionado: registrar.php?id=3
    $idUrl = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $datos['id_producto'] = $idUrl ? (string) $idUrl : '';
}

function clase_campo(array $errores, string $campo): string
{
    return isset($errores[$campo]) ? ' is-invalid' : '';
}

$titulo = 'Registrar movimiento';
require __DIR__ . '/../includes/header.php';
?>

<h1 class="h4 mb-3">Registrar movimiento</h1>

<?php if (isset($errores['general'])): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($errores['general']) ?></div>
<?php endif; ?>

<form id="formMovimiento" method="post" novalidate>
  <div class="row g-3">

    <div class="col-md-8">
      <label for="id_producto" class="form-label">Producto *</label>
      <select class="form-select<?= clase_campo($errores, 'id_producto') ?>"
              id="id_producto" name="id_producto" required>
        <option value="">Selecciona un producto</option>
        <?php foreach ($productos as $p): ?>
          <option value="<?= (int) $p['id_producto'] ?>"
                  data-stock="<?= (int) $p['stock_actual'] ?>"
            <?= (string) $datos['id_producto'] === (string) $p['id_producto'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($p['codigo'] . ' - ' . $p['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <div class="invalid-feedback"><?= htmlspecialchars($errores['id_producto'] ?? '') ?></div>
      <div class="form-text" id="infoStock"></div>
    </div>

    <div class="col-md-4">
      <label for="tipo" class="form-label">Tipo *</label>
      <select class="form-select<?= clase_campo($errores, 'tipo') ?>"
              id="tipo" name="tipo" required>
        <option value="">Selecciona el tipo</option>
        <option value="ENTRADA" <?= $datos['tipo'] === 'ENTRADA' ? 'selected' : '' ?>>Entrada</option>
        <option value="SALIDA"  <?= $datos['tipo'] === 'SALIDA'  ? 'selected' : '' ?>>Salida</option>
      </select>
      <div class="invalid-feedback"><?= htmlspecialchars($errores['tipo'] ?? '') ?></div>
    </div>

    <div class="col-md-4">
      <label for="cantidad" class="form-label">Cantidad *</label>
      <input type="number" class="form-control<?= clase_campo($errores, 'cantidad') ?>"
             id="cantidad" name="cantidad" min="1" step="1"
             value="<?= htmlspecialchars($datos['cantidad']) ?>" required>
      <div class="invalid-feedback"><?= htmlspecialchars($errores['cantidad'] ?? '') ?></div>
    </div>

    <div class="col-md-8">
      <label for="observacion" class="form-label">Observación</label>
      <input type="text" class="form-control<?= clase_campo($errores, 'observacion') ?>"
             id="observacion" name="observacion" maxlength="255"
             value="<?= htmlspecialchars($datos['observacion']) ?>"
             placeholder="Ej.: compra a proveedor, venta, ajuste">
      <div class="invalid-feedback"><?= htmlspecialchars($errores['observacion'] ?? '') ?></div>
    </div>

  </div>

  <div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">Registrar</button>
    <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
  </div>
</form>

<script src="<?= BASE_URL ?>/assets/js/movimiento.js"></script>

<?php require __DIR__ . '/../includes/footer.php'; ?>