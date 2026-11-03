# Análisis de requerimientos - TecnoStock

## 1. Problema
TecnoStock comercializa accesorios tecnológicos y registra sus productos
en una planilla. Esto ha provocado códigos duplicados, errores en las
cantidades disponibles y poca claridad sobre el stock real.

## 2. Objetivo
Construir una aplicación web, ejecutable en servidor local, que centralice
el catálogo de productos y controle sus entradas y salidas de existencias,
conservando la integridad de la información.

## 3. Alcance
**Incluye:** inicio de sesión, gestión de productos (crear, listar, buscar,
editar, desactivar), registro de movimientos de stock y alerta de stock bajo.

**No incluye:** gestión de usuarios desde la interfaz (los usuarios se
registran previamente en la base de datos), ventas, facturación, reportes
avanzados ni despliegue en internet.

## 4. Actor
| Actor | Responsabilidad |
|---|---|
| Encargado de inventario | Administra productos, categorías y movimientos de existencias. |

## 5. Requerimientos funcionales
| ID | Requerimiento |
|---|---|
| RF-01 | Iniciar sesión con un usuario previamente registrado. |
| RF-02 | Registrar productos con código, nombre, categoría, descripción, precio, stock actual y stock mínimo. |
| RF-03 | Consultar el listado de productos. |
| RF-04 | Buscar productos por nombre, código o categoría. |
| RF-05 | Modificar precio, descripción, categoría y cantidades de un producto. |
| RF-06 | Desactivar productos que ya no se comercializan. |
| RF-07 | Registrar entradas y salidas de existencias. |
| RF-08 | Mostrar una alerta cuando el stock actual sea menor que el stock mínimo. |

## 6. Reglas de negocio
| ID | Regla |
|---|---|
| RN-01 | El código de producto no puede repetirse. |
| RN-02 | Precio, stock actual, stock mínimo y cantidad de un movimiento no pueden ser negativos. |
| RN-03 | Todo movimiento registra fecha, tipo, cantidad, producto y usuario responsable. |
| RN-04 | Una salida no puede dejar el stock bajo cero. |
| RN-05 | Un producto con movimientos se desactiva; no se elimina físicamente. |
| RN-06 | El stock se actualiza automáticamente tras cada entrada o salida válida. |

## 7. Requerimientos no funcionales
| ID | Requerimiento |
|---|---|
| RNF-01 | Funcionar en servidor local (XAMPP: Apache, PHP 8 y MySQL). |
| RNF-02 | Usar PDO con consultas preparadas. |
| RNF-03 | Validar en JavaScript (cliente) y repetir las validaciones en PHP (servidor). |
| RNF-04 | Interfaz comprensible para un usuario no técnico y responsiva (Bootstrap). |
| RNF-05 | No usar frameworks complejos. |

## 8. Trazabilidad
Se completará a medida que avance la implementación.

| Requisito | Implementación (archivo) | Evidencia de prueba |
|---|---|---|
| RF-01 | Pendiente | Pendiente |
| RF-02 | Pendiente | Pendiente |
| RF-03 | Pendiente | Pendiente |
| RF-04 | Pendiente | Pendiente |
| RF-05 | Pendiente | Pendiente |
| RF-06 | Pendiente | Pendiente |
| RF-07 | Pendiente | Pendiente |
| RF-08 | Pendiente | Pendiente |
| RN-01 a RN-06 | Pendiente | Pendiente |