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
    nombre_rol VARCHAR(50) NOT NULL UNIQUE,
    estado_rol TINYINT(1) DEFAULT 1,
    eliminado_rol TINYINT(1) DEFAULT 0
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
    eliminado_usuario TINYINT(1) DEFAULT 0,
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
    eliminado_cliente TINYINT(1) DEFAULT 0,
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
    eliminado_categoria TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABLA MARCAS
-- ============================================

CREATE TABLE marcas (
    id_marca INT AUTO_INCREMENT PRIMARY KEY,
    nombre_marca VARCHAR(100) NOT NULL UNIQUE,
    estado_marca TINYINT(1) DEFAULT 1,
    eliminado_marca TINYINT(1) DEFAULT 0
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
    eliminado_producto TINYINT(1) DEFAULT 0,

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
    estado_venta ENUM('Realizada','Pendiente','Anulada') NOT NULL DEFAULT 'Pendiente',

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
-- VOLCADO DE DATOS INICIALES
-- ============================================

START TRANSACTION;

INSERT INTO roles (id_rol, nombre_rol, estado_rol, eliminado_rol) VALUES
(1, 'Administrador', 1, 0),
(2, 'Supervisor', 1, 0),
(3, 'Vendedor', 1, 0);

INSERT INTO usuarios (id_usuario, id_rol, nombre_usuario, correo_usuario, password_usuario, estado_usuario, eliminado_usuario, created_at) VALUES
(1, 1, 'Edwin Cruz', 'admin@sistema.com', 'admin123', 1, 0, '2026-06-13 22:38:52'),
(2, 2, 'Maria Lopez', 'supervisor@sistema.com', 'super123', 1, 0, '2026-06-13 22:38:52'),
(3, 3, 'Carlos Perez', 'vendedor@sistema.com', 'vend123', 1, 0, '2026-06-13 22:38:52'),
(6, 1, 'Kelvin Cienfuegos', 'kelvin100fuegos@gmail.com', '$2y$10$GPFkU9KanxOv/OJ6r2V8Qu8hqgqxfGFFiO5F9h2xElJJZiD0F5hha', 1, 0, '2026-06-17 21:40:31');

INSERT INTO clientes (id_cliente, nombre_cliente, tipo_cliente, dui_cliente, nit_cliente, nrc_cliente, telefono_cliente, correo_cliente, direccion_cliente, estado_cliente, eliminado_cliente, created_at) VALUES
(1, 'Consumidor Final', 'PN', '00000000-0', NULL, NULL, NULL, NULL, NULL, 1, 0, '2026-06-13 22:38:52'),
(2, 'Carlos Mendoza', 'PN', '01234567-8', '0614-120390-101-5', NULL, '7012-3456', 'carlos@email.com', 'Colonia El Sitio, San Miguel', 1, 0, '2026-06-13 22:38:52'),
(3, 'Ana Gomez', 'PN', '02345678-9', '0614-250495-102-3', NULL, '7543-2109', 'ana@email.com', 'Jardines de Bolonia, San Miguel', 1, 0, '2026-06-13 22:38:52'),
(4, 'Jose Ramirez', 'PN', '03456789-1', '0614-180188-103-8', NULL, '7123-4567', 'jose@email.com', 'Barrio Concepcion, San Miguel', 1, 0, '2026-06-13 22:38:52'),
(5, 'Andres Paredes', 'PJ', NULL, '456456464', '1234221414', '74777375', 'andreselcapo@gmail.com', 'Colonia Milagro de la Paz, San Miguel', 1, 1, '2026-06-18 00:17:28'),
(6, 'Andres Paredes', 'PN', '13213123-2', NULL, NULL, NULL, NULL, NULL, 1, 1, '2026-06-20 01:12:51');

INSERT INTO categorias (id_categoria, nombre_categoria, descripcion_categoria, estado_categoria, eliminado_categoria, created_at) VALUES
(1, 'Componentes de PC', 'Componentes internos para computadoras', 1, 0, '2026-06-13 22:38:52'),
(2, 'Accesorios de Red', 'Equipos y accesorios para redes', 1, 0, '2026-06-13 22:38:52'),
(3, 'Consolas y Videojuegos', 'Consolas, controles y videojuegos', 1, 0, '2026-06-13 22:38:52'),
(4, 'Almacenamiento', 'Dispositivos de almacenamiento de datos', 1, 0, '2026-06-13 22:38:52'),
(5, 'Herramientas de Servicio Tecnico', 'Herramientas para mantenimiento y reparación', 1, 0, '2026-06-13 22:38:52'),
(6, 'Perifericos', 'Periféricos para computadoras', 1, 0, '2026-06-13 22:38:52'),
(7, 'Edicion Limitada', 'Productos de edicion limitada', 0, 0, '2026-06-17 02:59:37'),
(12, 'Laptops Gamer', 'Laptops de alto rendimiento', 1, 0, '2026-06-17 07:25:45');

INSERT INTO marcas (id_marca, nombre_marca, estado_marca, eliminado_marca) VALUES
(1, 'Kingston', 1, 0),
(2, 'Corsair', 1, 0),
(3, 'Arctic', 1, 0),
(4, 'Sony', 1, 0),
(5, 'Redragon', 1, 0),
(6, 'HyperX', 1, 0),
(7, 'Lonuevo SV', 0, 0),
(10, 'HP', 1, 0);

INSERT INTO productos (id_producto, codigo_producto, nombre_producto, modelo_producto, descripcion_producto, imagen_producto, id_marca, id_categoria, precio_producto, stock_producto, estado_producto, eliminado_producto) VALUES
(1, 'PROD-001', 'Pasta Termica', 'MX-4', 'Pasta termica de alto rendimiento', 'mx4.jpg', 3, 5, 8.50, 25, 1, 0),
(2, 'PROD-002', 'SSD NVMe', 'NV2 1TB', 'Unidad SSD PCIe 4.0', 'ssd.jpg', 1, 4, 65.00, 15, 1, 0),
(3, 'PROD-003', 'Memoria RAM', 'Vengeance LPX 8GB', 'RAM DDR4 3200MHz', 'ram.jpg', 2, 1, 28.00, 24, 1, 0),
(4, 'PROD-004', 'Control PS4', 'DualShock 4', 'Control inalambrico', 'ps4.jpg', 4, 3, 55.00, 8, 1, 0),
(5, 'PROD-005', 'Cautin Regulable', '60W', 'Cautin para electronica', 'producto_1781932916_3956.jpg', 5, 5, 18.50, 12, 1, 0),
(6, 'PROD-006', 'Teclado Mecanico', 'K552 Kumara', 'Teclado RGB Gamer', 'teclado.jpg', 5, 6, 42.50, 20, 1, 0),
(7, 'PROD-007', 'Audifonos Gamer', 'Cloud Stinger', 'Audifonos con microfono', 'audifonos.jpg', 6, 6, 39.99, 18, 1, 0),
(8, 'PROD-008', 'Audifonos sony WH1000XM-4', 'WH1000XM-4', 'Audifonos sony de alta calidad, con cancelacion de sonido, modo ambiente y una alta duracion de carga', '../../../public/img/productos/producto_1780636413_6378.jpg', 4, 6, 300.00, 24, 1, 0),
(9, 'PROD-09', 'Laptop HP Victus 15', 'HP Victus 15-fb0000', 'Procesador: Intel Core i5/i7 (12ª o 13ª Gen) o AMD Ryzen 5/7 (Series 5000 a 7000).\r\nGráfica: NVIDIA RTX 3050, 4050 o 4060 (modelos básicos con RTX 2050 o GTX 1650).\r\nRAM: 8 GB o 16 GB DDR4/DDR5 (expandible hasta 32 GB o 64 GB).\r\nAlmacenamiento: SSD M.2 NVMe de 512 GB o 1 TB (solo tiene una ranura).\r\nPantalla: 15.6\" Full HD (1080p) a 144 Hz (algunas versiones básicas a 60 Hz).\r\nPuertos: 1 USB-C, 2 USB-A, HDMI 2.1, Ethernet (RJ-45) y lector de tarjetas SD.', 'producto_1781927751_3602.png', 10, 12, 800.00, 5, 0, 0);

INSERT INTO ventas (id_venta, numero_factura, id_cliente, id_usuario, fecha_venta, subtotal_venta, iva_venta, total_venta, estado_venta) VALUES
(1, 'FAC-000001', 2, 1, '2026-06-13 16:38:00', 57.52, 7.48, 65.00, 'Realizada'),
(2, 'FAC-000002', 3, 3, '2026-06-13 16:38:52', 63.72, 8.28, 72.00, 'Realizada'),
(3, 'FAC-000003', 4, 3, '2026-06-13 16:38:52', 37.61, 4.89, 42.50, 'Realizada'),
(6, 'FAC-20260620064300', 3, 3, '2026-06-20 06:36:00', 56.00, 7.28, 63.28, 'Realizada'),
(7, 'FAC-20260620064711', 4, 6, '2026-06-20 06:46:00', 56.00, 7.28, 63.28, 'Pendiente'),
(8, 'FAC-20260620065933', 3, 6, '2026-06-20 06:58:00', 356.00, 46.28, 402.28, 'Pendiente');

INSERT INTO detalle_ventas (id_detalle, id_venta, id_producto, cantidad_producto, precio_unitario, subtotal_detalle) VALUES
(1, 1, 2, 1, 65.00, 65.00),
(2, 2, 3, 2, 28.00, 56.00),
(3, 2, 1, 1, 16.00, 16.00),
(4, 3, 6, 1, 42.50, 42.50),
(5, 6, 3, 2, 28.00, 56.00),
(6, 7, 3, 2, 28.00, 56.00),
(7, 8, 8, 1, 300.00, 300.00),
(8, 8, 3, 2, 28.00, 56.00);

COMMIT;