<?php
// login.php - Inicio de sesión
session_start();
require __DIR__ . '/config/conexion.php';

// Si ya hay sesión iniciada, no mostrar el login
if (isset($_SESSION['id_usuario'])) {
    header('Location: index.php');
    exit;
}

$error  = '';
$correo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $clave  = $_POST['clave'] ?? '';

    // Validación en el servidor (se repite la del navegador)
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || $clave === '') {
        $error = 'Ingresa un correo válido y tu clave.';
    } else {
        // Consulta preparada: evita inyección SQL
        $stmt = $pdo->prepare(
            'SELECT id_usuario, nombre, clave
               FROM usuarios
              WHERE correo = :correo AND estado = 1'
        );
        $stmt->execute([':correo' => $correo]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($clave, $usuario['clave'])) {
            session_regenerate_id(true);
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre']     = $usuario['nombre'];
            header('Location: index.php');
            exit;
        }
        // Mensaje genérico: no revela si falló el correo o la clave
        $error = 'Correo o clave incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Iniciar sesión - TecnoStock</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <main class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
      <div class="col-11 col-sm-8 col-md-6 col-lg-4">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <h1 class="h4 text-center mb-1">TecnoStock</h1>
            <p class="text-center text-muted mb-4">Sistema de inventario</p>

            <?php if ($error !== ''): ?>
              <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
              </div>
            <?php endif; ?>

            <form id="formLogin" method="post" action="login.php" novalidate>
              <div class="mb-3">
                <label for="correo" class="form-label">Correo</label>
                <input type="email" class="form-control" id="correo" name="correo"
                       value="<?= htmlspecialchars($correo) ?>" required autofocus>
                <div class="invalid-feedback">Ingresa un correo válido.</div>
              </div>
              <div class="mb-3">
                <label for="clave" class="form-label">Clave</label>
                <input type="password" class="form-control" id="clave" name="clave" required>
                <div class="invalid-feedback">Ingresa tu clave.</div>
              </div>
              <button type="submit" class="btn btn-primary w-100">Ingresar</button>
            </form>

          </div>
        </div>
      </div>
    </div>
  </main>
  <script src="assets/js/login.js"></script>
</body>
</html>