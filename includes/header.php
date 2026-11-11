<?php
// includes/header.php - Encabezado y barra de navegación comunes
require_once __DIR__ . '/auth.php';
requerir_login();
$titulo = $titulo ?? 'TecnoStock';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($titulo) ?> - TecnoStock</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-md navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="<?= BASE_URL ?>/productos/index.php">TecnoStock</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="menu">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link<?= str_contains($_SERVER['SCRIPT_NAME'], '/productos/') ? ' active' : '' ?>"
             href="<?= BASE_URL ?>/productos/index.php">Productos</a>
        </li>
        <li class="nav-item">
          <a class="nav-link<?= str_contains($_SERVER['SCRIPT_NAME'], '/movimientos/') ? ' active' : '' ?>"
             href="<?= BASE_URL ?>/movimientos/index.php">Movimientos</a>
        </li>
      </ul>
      <span class="navbar-text me-3"><?= htmlspecialchars($_SESSION['nombre']) ?></span>
      <a href="<?= BASE_URL ?>/logout.php" class="btn btn-outline-light btn-sm">Cerrar sesión</a>
    </div>
  </div>
</nav>
<main class="container py-4">
<?php if (!empty($_SESSION['flash'])): ?>
  <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['tipo']) ?> alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($_SESSION['flash']['mensaje']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>