-- ============================================
-- ELIMINAR Y CREAR BASE DE DATOS
-- ============================================

DROP DATABASE IF EXISTS bd_inventario_ventas;
CREATE DATABASE bd_inventario_ventas
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE bd_inventario_ventas;

-- ============================================
-- TABLA ROLES
-- ============================================

CREATE TABLE roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ============================================
-- TABLA USUARIOS
-- ============================================

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    id_rol INT NOT NULL,
    nombre_usuario VARCHAR(100) NOT NULL,
    correo_usuario VARCHAR(100) NOT NULL UNIQUE,
    password_usuario VARCHAR(255) NOT NULL,
    estado_usuario TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_usuario_rol
        FOREIGN KEY (id_rol)
        REFERENCES roles(id_rol)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================
-- TABLA CLIENTES
-- ============================================

CREATE TABLE clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nombre_cliente VARCHAR(150) NOT NULL,
    tipo_cliente ENUM('PN', 'PJ') NOT NULL DEFAULT 'PN',
    dui_cliente VARCHAR(10) UNIQUE,
    nit_cliente VARCHAR(17) UNIQUE,
    nrc_cliente VARCHAR(20) UNIQUE,
    telefono_cliente VARCHAR(20),
    correo_cliente VARCHAR(100),
    direccion_cliente TEXT,
    estado_cliente TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABLA CATEGORIAS
-- ============================================

CREATE TABLE categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(100) NOT NULL UNIQUE,
    descripcion_categoria TEXT,
    estado_categoria TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABLA MARCAS
-- ============================================

CREATE TABLE marcas (
    id_marca INT AUTO_INCREMENT PRIMARY KEY,
    nombre_marca VARCHAR(100) NOT NULL UNIQUE,
    estado_marca TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

-- ============================================
-- TABLA PRODUCTOS
-- ============================================

CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    codigo_producto VARCHAR(50) NOT NULL UNIQUE,
    nombre_producto VARCHAR(150) NOT NULL,
    modelo_producto VARCHAR(100),
    descripcion_producto TEXT,
    imagen_producto VARCHAR(255),
    id_marca INT NOT NULL,
    id_categoria INT NOT NULL,
    precio_producto DECIMAL(10,2) NOT NULL,
    stock_producto INT NOT NULL DEFAULT 0,
    estado_producto TINYINT(1) DEFAULT 1,

    CONSTRAINT fk_producto_marca
        FOREIGN KEY (id_marca)
        REFERENCES marcas(id_marca)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_producto_categoria
        FOREIGN KEY (id_categoria)
        REFERENCES categorias(id_categoria)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================
-- TABLA VENTAS
-- ============================================

CREATE TABLE ventas (
    id_venta INT AUTO_INCREMENT PRIMARY KEY,
    numero_factura VARCHAR(20) NOT NULL UNIQUE,
    id_cliente INT NOT NULL,
    id_usuario INT NOT NULL,
    fecha_venta DATETIME DEFAULT CURRENT_TIMESTAMP,
    subtotal_venta DECIMAL(10,2) NOT NULL,
    iva_venta DECIMAL(10,2) NOT NULL,
    total_venta DECIMAL(10,2) NOT NULL,
    estado_venta TINYINT(1) DEFAULT 1,

    CONSTRAINT fk_venta_cliente
        FOREIGN KEY (id_cliente)
        REFERENCES clientes(id_cliente)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_venta_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================
-- TABLA DETALLE_VENTAS
-- ============================================

CREATE TABLE detalle_ventas (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad_producto INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal_detalle DECIMAL(10,2) NOT NULL,

    CONSTRAINT fk_detalle_venta
        FOREIGN KEY (id_venta)
        REFERENCES ventas(id_venta)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_detalle_producto
        FOREIGN KEY (id_producto)
        REFERENCES productos(id_producto)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================
-- DATOS INICIALES
-- ============================================

INSERT INTO roles (id_rol, nombre_rol) VALUES
(1, 'Administrador'),
(2, 'Supervisor'),
(3, 'Vendedor');

INSERT INTO usuarios (id_usuario, id_rol, nombre_usuario, correo_usuario, password_usuario, estado_usuario, created_at) VALUES
(1, 1, 'Edwin Cruz', 'admin@sistema.com', 'admin123', 1, '2026-06-13 22:38:52'),
(2, 2, 'Maria Lopez', 'supervisor@sistema.com', 'super123', 1, '2026-06-13 22:38:52'),
(3, 3, 'Carlos Perez', 'vendedor@sistema.com', 'vend123', 1, '2026-06-13 22:38:52');

INSERT INTO clientes (id_cliente, nombre_cliente, tipo_cliente, dui_cliente, nit_cliente, nrc_cliente, telefono_cliente, correo_cliente, direccion_cliente, estado_cliente, created_at) VALUES
(1, 'Consumidor Final', 'PN', '00000000-0', NULL, NULL, '0000-0000', 'consumidor@email.com', 'Ventas de mostrador', 1, '2026-06-13 22:38:52'),
(2, 'Carlos Mendoza', 'PN', '01234567-8', '0614-120390-101-5', NULL, '7012-3456', 'carlos@email.com', 'Colonia El Sitio, San Miguel', 1, '2026-06-13 22:38:52'),
(3, 'Ana Gomez', 'PN', '02345678-9', '0614-250495-102-3', NULL, '7543-2109', 'ana@email.com', 'Jardines de Bolonia, San Miguel', 1, '2026-06-13 22:38:52'),
(4, 'Jose Ramirez', 'PN', '03456789-1', '0614-180188-103-8', NULL, '7123-4567', 'jose@email.com', 'Barrio Concepcion, San Miguel', 1, '2026-06-13 22:38:52');

INSERT INTO categorias (id_categoria, nombre_categoria, descripcion_categoria, estado_categoria, created_at) VALUES
(1, 'Componentes de PC', 'Componentes internos para computadoras', 1, '2026-06-13 22:38:52'),
(2, 'Accesorios de Red', 'Equipos y accesorios para redes', 1, '2026-06-13 22:38:52'),
(3, 'Consolas y Videojuegos', 'Consolas, controles y videojuegos', 1, '2026-06-13 22:38:52'),
(4, 'Almacenamiento', 'Dispositivos de almacenamiento de datos', 1, '2026-06-13 22:38:52'),
(5, 'Herramientas de Servicio Tecnico', 'Herramientas para mantenimiento y reparación', 1, '2026-06-13 22:38:52'),
(6, 'Perifericos', 'Periféricos para computadoras', 1, '2026-06-13 22:38:52');

INSERT INTO marcas (id_marca, nombre_marca, estado_marca) VALUES
(1, 'Kingston', 1),
(2, 'Corsair', 1),
(3, 'Arctic', 1),
(4, 'Sony', 1),
(5, 'Redragon', 1),
(6, 'HyperX', 1);

INSERT INTO productos (id_producto, codigo_producto, nombre_producto, modelo_producto, descripcion_producto, imagen_producto, id_marca, id_categoria, precio_producto, stock_producto, estado_producto) VALUES
(1, 'PROD-001', 'Pasta Termica', 'MX-4', 'Pasta termica de alto rendimiento', 'mx4.jpg', 3, 5, 8.50, 25, 1),
(2, 'PROD-002', 'SSD NVMe', 'NV2 1TB', 'Unidad SSD PCIe 4.0', 'ssd.jpg', 1, 4, 65.00, 15, 1),
(3, 'PROD-003', 'Memoria RAM', 'Vengeance LPX 8GB', 'RAM DDR4 3200MHz', 'ram.jpg', 2, 1, 28.00, 30, 1),
(4, 'PROD-004', 'Control PS4', 'DualShock 4', 'Control inalambrico', 'ps4.jpg', 4, 3, 55.00, 8, 1),
(5, 'PROD-005', 'Cautin Regulable', '60W', 'Cautin para electronica', 'cautin.jpg', 5, 5, 18.50, 12, 1),
(6, 'PROD-006', 'Teclado Mecanico', 'K552 Kumara', 'Teclado RGB Gamer', 'teclado.jpg', 5, 6, 42.50, 20, 1),
(7, 'PROD-007', 'Audifonos Gamer', 'Cloud Stinger', 'Audifonos con microfono', 'audifonos.jpg', 6, 6, 39.99, 18, 1),
(8, 'PROD-008', 'Audifonos sony WH1000XM-4', 'WH1000XM-4', 'Audifonos sony de alta calidad, con cancelacion de sonido, modo ambiente y una alta duracion de carga', '../../../public/img/productos/producto_1780636413_6378.jpg', 4, 6, 300.00, 20, 1);

INSERT INTO ventas (id_venta, numero_factura, id_cliente, id_usuario, fecha_venta, subtotal_venta, iva_venta, total_venta, estado_venta) VALUES
(1, 'FAC-000001', 2, 1, '2026-06-13 16:38:52', 57.52, 7.48, 65.00, 1),
(2, 'FAC-000002', 3, 3, '2026-06-13 16:38:52', 63.72, 8.28, 72.00, 1),
(3, 'FAC-000003', 4, 3, '2026-06-13 16:38:52', 37.61, 4.89, 42.50, 1);

INSERT INTO detalle_ventas (id_detalle, id_venta, id_producto, cantidad_producto, precio_unitario, subtotal_detalle) VALUES
(1, 1, 2, 1, 65.00, 65.00),
(2, 2, 3, 2, 28.00, 56.00),
(3, 2, 1, 1, 16.00, 16.00),
(4, 3, 6, 1, 42.50, 42.50);

COMMIT;