<?php
// productos/_form.php - Formulario compartido por crear.php y editar.php
// Variables esperadas: $datos, $errores, $categorias, $esEdicion
function clase_campo(array $errores, string $campo): string
{
    return isset($errores[$campo]) ? ' is-invalid' : '';
}
?>
<form id="formProducto" method="post" novalidate>
  <div class="row g-3">

    <div class="col-md-4">
      <label for="codigo" class="form-label">Código *</label>
      <input type="text" class="form-control<?= clase_campo($errores, 'codigo') ?>"
             id="codigo" name="codigo" maxlength="30"
             value="<?= htmlspecialchars($datos['codigo']) ?>"
             <?= $esEdicion ? 'readonly' : 'required' ?>>
      <div class="invalid-feedback"><?= htmlspecialchars($errores['codigo'] ?? '') ?></div>
      <?php if ($esEdicion): ?>
        <div class="form-text">El código no se puede modificar.</div>
      <?php endif; ?>
    </div>

    <div class="col-md-8">
      <label for="nombre" class="form-label">Nombre *</label>
      <input type="text" class="form-control<?= clase_campo($errores, 'nombre') ?>"
             id="nombre" name="nombre" maxlength="120"
             value="<?= htmlspecialchars($datos['nombre']) ?>" required>
      <div class="invalid-feedback"><?= htmlspecialchars($errores['nombre'] ?? '') ?></div>
    </div>

    <div class="col-md-6">
      <label for="id_categoria" class="form-label">Categoría *</label>
      <select class="form-select<?= clase_campo($errores, 'id_categoria') ?>"
              id="id_categoria" name="id_categoria" required>
        <option value="">Selecciona una categoría</option>
        <?php foreach ($categorias as $c): ?>
          <option value="<?= (int) $c['id_categoria'] ?>"
            <?= (string) $datos['id_categoria'] === (string) $c['id_categoria'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <div class="invalid-feedback"><?= htmlspecialchars($errores['id_categoria'] ?? '') ?></div>
    </div>

    <div class="col-md-6">
      <label for="precio" class="form-label">Precio ($) *</label>
      <input type="number" class="form-control<?= clase_campo($errores, 'precio') ?>"
             id="precio" name="precio" min="0" step="0.01"
             value="<?= htmlspecialchars($datos['precio']) ?>" required>
      <div class="invalid-feedback"><?= htmlspecialchars($errores['precio'] ?? '') ?></div>
    </div>

    <div class="col-md-6">
      <label for="stock_actual" class="form-label">Stock actual *</label>
      <input type="number" class="form-control<?= clase_campo($errores, 'stock_actual') ?>"
             id="stock_actual" name="stock_actual" min="0" step="1"
             value="<?= htmlspecialchars($datos['stock_actual']) ?>" required>
      <div class="invalid-feedback"><?= htmlspecialchars($errores['stock_actual'] ?? '') ?></div>
    </div>

    <div class="col-md-6">
      <label for="stock_minimo" class="form-label">Stock mínimo *</label>
      <input type="number" class="form-control<?= clase_campo($errores, 'stock_minimo') ?>"
             id="stock_minimo" name="stock_minimo" min="0" step="1"
             value="<?= htmlspecialchars($datos['stock_minimo']) ?>" required>
      <div class="invalid-feedback"><?= htmlspecialchars($errores['stock_minimo'] ?? '') ?></div>
      <div class="form-text">Se alertará cuando el stock actual sea menor a este valor.</div>
    </div>

    <div class="col-12">
      <label for="descripcion" class="form-label">Descripción</label>
      <textarea class="form-control<?= clase_campo($errores, 'descripcion') ?>"
                id="descripcion" name="descripcion" rows="3" maxlength="500"><?= htmlspecialchars($datos['descripcion']) ?></textarea>
      <div class="invalid-feedback"><?= htmlspecialchars($errores['descripcion'] ?? '') ?></div>
    </div>

  </div>

  <div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
  </div>
</form>

<script src="<?= BASE_URL ?>/assets/js/producto.js"></script>