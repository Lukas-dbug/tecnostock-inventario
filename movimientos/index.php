<?php
// movimientos/index.php - Historial de movimientos de stock
require __DIR__ . '/../config/conexion.php';
$titulo = 'Movimientos';
require __DIR__ . '/../includes/header.php';

$movimientos = $pdo->query(
    'SELECT m.fecha_hora, m.tipo, m.cantidad, m.observacion,
            p.codigo, p.nombre AS producto, u.nombre AS usuario
       FROM movimientos m
      INNER JOIN productos p ON p.id_producto = m.id_producto
      INNER JOIN usuarios  u ON u.id_usuario  = m.id_usuario
      ORDER BY m.fecha_hora DESC, m.id_movimiento DESC
      LIMIT 100'
)->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Movimientos de stock</h1>
  <a href="registrar.php" class="btn btn-success">+ Registrar movimiento</a>
</div>

<div class="table-responsive">
  <table class="table table-hover align-middle bg-white">
    <thead class="table-light">
      <tr>
        <th>Fecha</th>
        <th>Producto</th>
        <th>Tipo</th>
        <th class="text-end">Cantidad</th>
        <th>Observación</th>
        <th>Responsable</th>
      </tr>
    </thead>
    <tbody>
    <?php if (count($movimientos) === 0): ?>
      <tr>
        <td colspan="6" class="text-center text-muted py-4">Aún no hay movimientos.</td>
      </tr>
    <?php endif; ?>

    <?php foreach ($movimientos as $m): ?>
      <tr>
        <td><?= date('d-m-Y H:i', strtotime($m['fecha_hora'])) ?></td>
        <td><?= htmlspecialchars($m['codigo'] . ' - ' . $m['producto']) ?></td>
        <td>
          <?php if ($m['tipo'] === 'ENTRADA'): ?>
            <span class="badge text-bg-primary">Entrada</span>
          <?php else: ?>
            <span class="badge text-bg-warning">Salida</span>
          <?php endif; ?>
        </td>
        <td class="text-end"><?= $m['tipo'] === 'ENTRADA' ? '+' : '-' ?><?= (int) $m['cantidad'] ?></td>
        <td><?= htmlspecialchars($m['observacion'] ?? '') ?></td>
        <td><?= htmlspecialchars($m['usuario']) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<p class="text-muted small">Se muestran los últimos 100 movimientos.</p>

<?php require __DIR__ . '/../includes/footer.php'; ?>