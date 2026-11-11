<?php
// productos/index.php - Listado de productos con búsqueda
require __DIR__ . '/../config/conexion.php';
$titulo = 'Productos';
require __DIR__ . '/../includes/header.php';

$busqueda = trim($_GET['q'] ?? '');

$sql = 'SELECT p.id_producto, p.codigo, p.nombre, p.precio,
               p.stock_actual, p.stock_minimo, p.estado,
               c.nombre AS categoria
          FROM productos p
         INNER JOIN categorias c ON c.id_categoria = p.id_categoria';
$params = [];

if ($busqueda !== '') {
    // Consulta preparada: un parámetro distinto por cada LIKE
    $sql .= ' WHERE p.nombre LIKE :q1 OR p.codigo LIKE :q2 OR c.nombre LIKE :q3';
    $like = '%' . $busqueda . '%';
    $params = [':q1' => $like, ':q2' => $like, ':q3' => $like];
}
$sql .= ' ORDER BY p.estado DESC, p.nombre ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll();

// Cantidad de productos activos con stock bajo (RF-08)
$bajoStock = (int) $pdo->query(
    'SELECT COUNT(*) FROM productos WHERE estado = 1 AND stock_actual < stock_minimo'
)->fetchColumn();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Productos</h1>
  <a href="crear.php" class="btn btn-success">+ Nuevo producto</a>
</div>

<?php if ($bajoStock > 0): ?>
  <div class="alert alert-warning" role="alert">
    <strong>Atención:</strong> hay <?= $bajoStock ?> producto(s) con stock bajo.
  </div>
<?php endif; ?>

<form class="row g-2 mb-3" method="get" action="index.php">
  <div class="col-12 col-md-8">
    <input type="search" class="form-control" name="q"
           placeholder="Buscar por nombre, código o categoría"
           value="<?= htmlspecialchars($busqueda) ?>">
  </div>
  <div class="col-12 col-md-4 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Buscar</button>
    <?php if ($busqueda !== ''): ?>
      <a class="btn btn-outline-secondary" href="index.php">Limpiar</a>
    <?php endif; ?>
  </div>
</form>

<div class="table-responsive">
  <table class="table table-hover align-middle bg-white">
    <thead class="table-light">
      <tr>
        <th>Código</th>
        <th>Nombre</th>
        <th>Categoría</th>
        <th class="text-end">Precio</th>
        <th class="text-end">Stock</th>
        <th class="text-end">Mínimo</th>
        <th>Estado</th>
        <th class="text-end">Acciones</th>
      </tr>
    </thead>
    <tbody>
    <?php if (count($productos) === 0): ?>
      <tr>
        <td colspan="8" class="text-center text-muted py-4">
          No se encontraron productos.
        </td>
      </tr>
    <?php endif; ?>

    <?php foreach ($productos as $p):
        $activo = (int) $p['estado'] === 1;
        $stockBajo = $activo && $p['stock_actual'] < $p['stock_minimo'];
    ?>
      <tr class="<?= $stockBajo ? 'table-danger' : '' ?> <?= $activo ? '' : 'text-muted' ?>">
        <td><?= htmlspecialchars($p['codigo']) ?></td>
        <td><?= htmlspecialchars($p['nombre']) ?></td>
        <td><?= htmlspecialchars($p['categoria']) ?></td>
        <td class="text-end">$<?= number_format($p['precio'], 0, ',', '.') ?></td>
        <td class="text-end"><?= (int) $p['stock_actual'] ?></td>
        <td class="text-end"><?= (int) $p['stock_minimo'] ?></td>
        <td>
          <?php if (!$activo): ?>
            <span class="badge text-bg-secondary">Inactivo</span>
          <?php elseif ($stockBajo): ?>
            <span class="badge text-bg-danger">Stock bajo</span>
          <?php else: ?>
            <span class="badge text-bg-success">Normal</span>
          <?php endif; ?>
        </td>
           <td class="text-end">
          <?php if ($activo): ?>
            <a href="editar.php?id=<?= (int) $p['id_producto'] ?>"
               class="btn btn-sm btn-outline-primary">Editar</a>
            <form method="post" action="desactivar.php" class="d-inline"
                  onsubmit="return confirm('¿Desactivar este producto? Dejará de comercializarse.');">
              <input type="hidden" name="id" value="<?= (int) $p['id_producto'] ?>">
              <?= csrf_campo() ?>
              <button type="submit" class="btn btn-sm btn-outline-danger">Desactivar</button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>