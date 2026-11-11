<?php
// includes/movimientos_funciones.php - Lógica de entradas y salidas de stock

// Validaciones del servidor (se repiten las de JavaScript, RNF-03)
function validar_movimiento(array $d): array
{
    $errores = [];

    $idProducto = (string) ($d['id_producto'] ?? '');
    if (!ctype_digit($idProducto) || (int) $idProducto <= 0) {
        $errores['id_producto'] = 'Selecciona un producto.';
    }

    if (!in_array($d['tipo'] ?? '', ['ENTRADA', 'SALIDA'], true)) {
        $errores['tipo'] = 'Selecciona el tipo de movimiento.';
    }

    // RN-02: la cantidad no puede ser negativa (ni cero, que no tendría sentido)
    $cantidad = trim((string) ($d['cantidad'] ?? ''));
    if ($cantidad === '' || filter_var($cantidad, FILTER_VALIDATE_INT) === false) {
        $errores['cantidad'] = 'La cantidad debe ser un número entero.';
    } elseif ((int) $cantidad <= 0) {
        $errores['cantidad'] = 'La cantidad debe ser mayor a cero.';
    } elseif ((int) $cantidad > 1000000) {
        $errores['cantidad'] = 'La cantidad no puede superar 1.000.000.';
    }

    if (mb_strlen(trim((string) ($d['observacion'] ?? ''))) > 255) {
        $errores['observacion'] = 'Máximo 255 caracteres.';
    }

    return $errores;
}

// Registra el movimiento y actualiza el stock dentro de una transacción.
// Devuelve ['ok' => true, 'stock' => int, 'minimo' => int]
// o ['ok' => false, 'campo' => string, 'error' => string].
function registrar_movimiento(
    PDO $pdo,
    int $idProducto,
    string $tipo,
    int $cantidad,
    ?string $observacion,
    int $idUsuario
): array {
    $pdo->beginTransaction();

    try {
        // FOR UPDATE bloquea la fila para que otra operación no cambie el stock a mitad de camino
        $stmt = $pdo->prepare(
            'SELECT stock_actual, stock_minimo, estado
               FROM productos WHERE id_producto = :id FOR UPDATE'
        );
        $stmt->execute([':id' => $idProducto]);
        $producto = $stmt->fetch();

        if (!$producto) {
            $pdo->rollBack();
            return ['ok' => false, 'campo' => 'id_producto', 'error' => 'El producto no existe.'];
        }
        if ((int) $producto['estado'] !== 1) {
            $pdo->rollBack();
            return ['ok' => false, 'campo' => 'id_producto',
                    'error' => 'No se pueden registrar movimientos de un producto inactivo.'];
        }

        $stockActual = (int) $producto['stock_actual'];
        $nuevoStock  = $tipo === 'ENTRADA' ? $stockActual + $cantidad : $stockActual - $cantidad;

        // RN-04: una salida no puede dejar el stock bajo cero
        if ($nuevoStock < 0) {
            $pdo->rollBack();
            return ['ok' => false, 'campo' => 'cantidad',
                    'error' => "Stock insuficiente: hay $stockActual unidad(es) disponible(s)."];
        }

        // RN-03: fecha, tipo, cantidad, producto y usuario responsable
        $stmt = $pdo->prepare(
            'INSERT INTO movimientos (tipo, cantidad, observacion, id_producto, id_usuario)
             VALUES (:tipo, :cantidad, :observacion, :id_producto, :id_usuario)'
        );
        $stmt->execute([
            ':tipo'        => $tipo,
            ':cantidad'    => $cantidad,
            ':observacion' => $observacion,
            ':id_producto' => $idProducto,
            ':id_usuario'  => $idUsuario,
        ]);

        // RN-06: el stock se actualiza automáticamente
        $stmt = $pdo->prepare(
            'UPDATE productos SET stock_actual = :stock WHERE id_producto = :id'
        );
        $stmt->execute([':stock' => $nuevoStock, ':id' => $idProducto]);

        $pdo->commit();
        return ['ok' => true, 'stock' => $nuevoStock, 'minimo' => (int) $producto['stock_minimo']];

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('Error al registrar movimiento: ' . $e->getMessage());
        return ['ok' => false, 'campo' => 'general',
                'error' => 'No se pudo registrar el movimiento. Intenta nuevamente.'];
    }
}