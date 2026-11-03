-- TecnoStock - Script de base de datos
-- Se puede ejecutar varias veces: recrea las tablas desde cero.

CREATE DATABASE IF NOT EXISTS tecnostock
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE tecnostock;

DROP TABLE IF EXISTS movimientos;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  nombre     VARCHAR(100) NOT NULL,
  correo     VARCHAR(120) NOT NULL UNIQUE,
  clave      VARCHAR(255) NOT NULL,
  estado     TINYINT(1)   NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE categorias (
  id_categoria INT AUTO_INCREMENT PRIMARY KEY,
  nombre       VARCHAR(60)  NOT NULL UNIQUE,
  descripcion  VARCHAR(255) NULL,
  estado       TINYINT(1)   NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE productos (
  id_producto  INT AUTO_INCREMENT PRIMARY KEY,
  codigo       VARCHAR(30)   NOT NULL UNIQUE,
  nombre       VARCHAR(120)  NOT NULL,
  descripcion  TEXT          NULL,
  precio       DECIMAL(10,2) NOT NULL,
  stock_actual INT           NOT NULL DEFAULT 0,
  stock_minimo INT           NOT NULL DEFAULT 0,
  estado       TINYINT(1)    NOT NULL DEFAULT 1,
  id_categoria INT           NOT NULL,
  CONSTRAINT chk_precio       CHECK (precio >= 0),
  CONSTRAINT chk_stock_actual CHECK (stock_actual >= 0),
  CONSTRAINT chk_stock_minimo CHECK (stock_minimo >= 0),
  CONSTRAINT fk_producto_categoria
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE movimientos (
  id_movimiento INT AUTO_INCREMENT PRIMARY KEY,
  fecha_hora    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  tipo          ENUM('ENTRADA','SALIDA') NOT NULL,
  cantidad      INT          NOT NULL,
  observacion   VARCHAR(255) NULL,
  id_producto   INT          NOT NULL,
  id_usuario    INT          NOT NULL,
  CONSTRAINT chk_cantidad CHECK (cantidad > 0),
  CONSTRAINT fk_mov_producto
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_mov_usuario
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;