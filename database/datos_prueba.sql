-- TecnoStock - Datos de prueba
-- Ejecutar DESPUÉS de tecnostock.sql (con la base vacía).
--
-- Usuario de prueba:
--   correo: encargado@tecnostock.cl
--   clave:  Admin123   (se guarda encriptada con password_hash de PHP)

USE tecnostock;

INSERT INTO usuarios (nombre, correo, clave, estado) VALUES
('Encargado de Inventario', 'encargado@tecnostock.cl',
 '$2y$10$Kizl/s0/RNzPlIYp0q/urexaAxpiMLDJkjB8wLfQG/QYSJ65gygXG', 1);

INSERT INTO categorias (nombre, descripcion, estado) VALUES
('Cables y Adaptadores', 'Cables USB, HDMI y adaptadores', 1),
('Audio', 'Audífonos y parlantes', 1),
('Almacenamiento', 'Pendrives y discos', 1),
('Periféricos', 'Mouse, teclados y cámaras', 1),
('Cargadores', 'Cargadores y baterías', 1);

INSERT INTO productos
(codigo, nombre, descripcion, precio, stock_actual, stock_minimo, estado, id_categoria) VALUES
('CAB-001', 'Cable USB-C 1m', 'Cable de carga y datos', 4990, 40, 10, 1,
  (SELECT id_categoria FROM categorias WHERE nombre='Cables y Adaptadores')),
('CAB-002', 'Adaptador HDMI a VGA', 'Convierte señal digital a analógica', 9990, 4, 5, 1,
  (SELECT id_categoria FROM categorias WHERE nombre='Cables y Adaptadores')),
('AUD-001', 'Audífonos Bluetooth', 'Inalámbricos con micrófono', 19990, 15, 5, 1,
  (SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('AUD-002', 'Parlante portátil', 'Parlante Bluetooth resistente al agua', 24990, 3, 5, 1,
  (SELECT id_categoria FROM categorias WHERE nombre='Audio')),
('ALM-001', 'Pendrive 64GB', 'USB 3.0', 7990, 25, 8, 1,
  (SELECT id_categoria FROM categorias WHERE nombre='Almacenamiento')),
('PER-001', 'Mouse inalámbrico', 'Mouse óptico 2.4GHz', 8990, 12, 5, 1,
  (SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-002', 'Teclado USB', 'Teclado en español', 12990, 5, 5, 1,
  (SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('PER-003', 'Webcam HD', 'Producto descontinuado', 15990, 0, 3, 0,
  (SELECT id_categoria FROM categorias WHERE nombre='Periféricos')),
('CAR-001', 'Cargador USB-C 20W', 'Carga rápida', 11990, 18, 6, 1,
  (SELECT id_categoria FROM categorias WHERE nombre='Cargadores'));

-- Movimientos coherentes con el stock actual de cada producto
INSERT INTO movimientos (fecha_hora, tipo, cantidad, observacion, id_producto, id_usuario) VALUES
('2026-10-01 09:00:00','ENTRADA',50,'Compra inicial',
  (SELECT id_producto FROM productos WHERE codigo='CAB-001'),1),
('2026-10-10 15:30:00','SALIDA',10,'Venta',
  (SELECT id_producto FROM productos WHERE codigo='CAB-001'),1),
('2026-10-01 09:10:00','ENTRADA',10,'Compra inicial',
  (SELECT id_producto FROM productos WHERE codigo='CAB-002'),1),
('2026-10-12 11:00:00','SALIDA',6,'Venta',
  (SELECT id_producto FROM productos WHERE codigo='CAB-002'),1),
('2026-10-02 10:00:00','ENTRADA',8,'Compra inicial',
  (SELECT id_producto FROM productos WHERE codigo='AUD-002'),1),
('2026-10-15 17:00:00','SALIDA',5,'Venta',
  (SELECT id_producto FROM productos WHERE codigo='AUD-002'),1),
('2026-10-03 09:30:00','ENTRADA',15,'Compra inicial',
  (SELECT id_producto FROM productos WHERE codigo='PER-001'),1),
('2026-10-18 12:00:00','SALIDA',3,'Venta',
  (SELECT id_producto FROM productos WHERE codigo='PER-001'),1),
('2026-09-20 10:00:00','ENTRADA',5,'Compra inicial',
  (SELECT id_producto FROM productos WHERE codigo='PER-003'),1),
('2026-10-05 16:00:00','SALIDA',5,'Liquidación',
  (SELECT id_producto FROM productos WHERE codigo='PER-003'),1);
  