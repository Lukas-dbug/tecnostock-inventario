<?php
// index.php - Página principal (provisional)
require __DIR__ . '/includes/auth.php';
requerir_login();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inicio - TecnoStock</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <nav class="navbar navbar-dark bg-dark">
    <div class="container">
      <span class="navbar-brand">TecnoStock</span>
      <div class="d-flex align-items-center text-white">
        <span class="me-3"><?= htmlspecialchars($_SESSION['nombre']) ?></span>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar sesión</a>
      </div>
    </div>
  </nav>
  <main class="container py-4">
    <h1 class="h4">Bienvenido</h1>
    <p class="text-muted">Aquí irá el listado de productos.</p>
  </main>
</body>
</html>