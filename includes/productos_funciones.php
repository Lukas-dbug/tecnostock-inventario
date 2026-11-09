<?php
// includes/productos_funciones.php - Funciones compartidas de productos

// Categorías activas para el <select> del formulario
function obtener_categorias(PDO $pdo): array
{
    return $pdo->query(
        'SELECT id_categoria, nombre FROM categorias WHERE estado = 1 ORDER BY nombre'
    )->fetchAll();
}

// Validaciones del servidor. Repiten las que hace JavaScript (RNF-03).
// Devuelve un arreglo [campo => mensaje]; vacío si todo está correcto.
function validar_producto(PDO $pdo, array $d, ?int $idActual = null): array
{
    $errores = [];

    // Código (RN-01: no puede repetirse)
    $codigo = trim($d['codigo'] ?? '');
    if ($codigo === '') {
        $errores['codigo'] = 'El código es obligatorio.';
    } elseif (!preg_match('/^[A-Za-z0-9-]{1,30}$/', $codigo)) {
        $errores['codigo'] = 'Usa solo letras, números y guiones (máximo 30).';
    } else {
        $stmt = $pdo->prepare('SELECT id_producto FROM productos WHERE codigo = :codigo');
        $stmt->execute([':codigo' => $codigo]);
        $existente = $stmt->fetchColumn();
        if ($existente !== false && (int) $existente !== $idActual) {
            $errores['codigo'] = 'Ya existe un producto con ese código.';
        }
    }

    // Nombre
    $nombre = trim($d['nombre'] ?? '');
    if ($nombre === '') {
        $errores['nombre'] = 'El nombre es obligatorio.';
    } elseif (mb_strlen($nombre) > 120) {
        $errores['nombre'] = 'Máximo 120 caracteres.';
    }

    // Descripción (opcional)
    if (mb_strlen(trim($d['descripcion'] ?? '')) > 500) {
        $errores['descripcion'] = 'Máximo 500 caracteres.';
    }

    // Categoría: debe existir y estar activa
    $idCategoria = (string) ($d['id_categoria'] ?? '');
    if (!ctype_digit($idCategoria)) {
        $errores['id_categoria'] = 'Selecciona una categoría.';
    } else {
        $stmt = $pdo->prepare(
            'SELECT 1 FROM categorias WHERE id_categoria = :id AND estado = 1'
        );
        $stmt->execute([':id' => $idCategoria]);
        if ($stmt->fetchColumn() === false) {
            $errores['id_categoria'] = 'La categoría seleccionada no es válida.';
        }
    }

    // Precio (RN-02: no negativo)
    $precio = trim((string) ($d['precio'] ?? ''));
    if ($precio === '' || !is_numeric($precio)) {
        $errores['precio'] = 'El precio debe ser un número.';
    } elseif ((float) $precio < 0) {
        $errores['precio'] = 'El precio no puede ser negativo.';
    } elseif ((float) $precio > 99999999.99) {
        $errores['precio'] = 'El precio es demasiado alto.';
    }

    // Stocks (RN-02: enteros no negativos)
    $enteros = ['stock_actual' => 'El stock actual', 'stock_minimo' => 'El stock mínimo'];
    foreach ($enteros as $campo => $etiqueta) {
        $valor = trim((string) ($d[$campo] ?? ''));
        if ($valor === '' || filter_var($valor, FILTER_VALIDATE_INT) === false) {
            $errores[$campo] = "$etiqueta debe ser un número entero.";
        } elseif ((int) $valor < 0) {
            $errores[$campo] = "$etiqueta no puede ser negativo.";
        } elseif ((int) $valor > 1000000) {
            $errores[$campo] = "$etiqueta no puede superar 1.000.000.";
        }
    }

    return $errores;
}