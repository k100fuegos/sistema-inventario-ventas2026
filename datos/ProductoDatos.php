<?php

require_once __DIR__ . '/Conexion.php';

class ProductoDatos
{

    public function listarProductos()
    {

        $conexion = new Conexion();
        $conexion->query = "SELECT
                            productos.id_producto, productos.codigo_producto, productos.nombre_producto, productos.modelo_producto,
                            productos.descripcion_producto, productos.imagen_producto, marcas.nombre_marca, categorias.nombre_categoria,
                            productos.precio_producto, productos.stock_producto, productos.estado_producto
                            FROM productos
                            INNER JOIN categorias
                                ON productos.id_categoria = categorias.id_categoria
                            INNER JOIN marcas
                                ON productos.id_marca = marcas.id_marca
                            WHERE productos.estado_producto = 1
                            ORDER BY productos.nombre_producto;";

        return $conexion->get_records();
    }

    private function valorNulo($valor)
    {
        return trim($valor) == '' ? null : trim($valor);
    }

    public function insertarProducto($producto)
    {
        $conexion = new Conexion();

        $conexion->query = "INSERT INTO productos (codigo_producto, nombre_producto, modelo_producto, descripcion_producto, imagen_producto, id_marca, id_categoria, precio_producto, stock_producto, estado_producto)
                            VALUES (:codigoProducto, :nombreProducto, :modeloProducto, :descripcionProducto, :imagenProducto, :idMarca, :idCategoria, :precioProducto, :stockProducto, 1)";

        return $conexion->execute_query([
            ':codigoProducto'       => $producto['codigo_producto'],
            ':nombreProducto'       => $producto['nombre_producto'],
            ':modeloProducto'       => $this->valorNulo($producto['modelo_producto']),
            ':descripcionProducto'  => $this->valorNulo($producto['descripcion_producto']),
            ':imagenProducto'       => $this->valorNulo($producto['imagen_producto']),
            ':idMarca'              => $producto['id_marca'],
            ':idCategoria'          => $producto['id_categoria'],
            ':precioProducto'       => $producto['precio_producto'],
            ':stockProducto'        => $producto['stock_producto'],
        ]);
    }

    public function obtenerProductoPorId($idProducto)
    {
        $conexion = new Conexion();

        $conexion->query = "SELECT productos.id_producto, productos.codigo_producto, productos.nombre_producto, productos.modelo_producto, productos.descripcion_producto, 
                            productos.imagen_producto, productos.id_marca, marcas.nombre_marca, productos.id_categoria, categorias.nombre_categoria, productos.precio_producto, productos.stock_producto, 
                            productos.estado_producto 
                            FROM productos
                            INNER JOIN categorias ON productos.id_categoria = categorias.id_categoria
                            INNER JOIN marcas ON productos.id_marca = marcas.id_marca
                            WHERE productos.id_producto = :idProducto";

        return $conexion->get_record([':idProducto' => $idProducto]);
    }

    public function actualizarProducto($producto)
    {
        $conexion = new Conexion();

        $conexion->query = "UPDATE productos
                            SET codigo_producto = :codigoProducto,
                                nombre_producto = :nombreProducto,
                                modelo_producto = :modeloProducto,
                                descripcion_producto = :descripcionProducto,
                                imagen_producto = :imagenProducto,
                                id_marca = :idMarca,
                                id_categoria = :idCategoria,
                                precio_producto = :precioProducto,
                                stock_producto = :stockProducto
                            WHERE id_producto = :idProducto";

        return $conexion->execute_query([
            ':codigoProducto'       => $producto['codigo_producto'],
            ':nombreProducto'       => $producto['nombre_producto'],
            ':modeloProducto'       => $this->valorNulo($producto['modelo_producto']),
            ':descripcionProducto'  => $this->valorNulo($producto['descripcion_producto']),
            ':imagenProducto'       => $this->valorNulo($producto['imagen_producto']),
            ':idMarca'              => $producto['id_marca'],
            ':idCategoria'          => $producto['id_categoria'],
            ':precioProducto'       => $producto['precio_producto'],
            ':stockProducto'        => $producto['stock_producto'],
            ':idProducto'           => $producto['id_producto'],
        ]);
    }

    public function eliminarProducto($idProducto)
    {
        $conexion = new Conexion();

        $conexion->query = "UPDATE productos
                            SET estado_producto = '0'
                            WHERE id_producto = :idProducto";

        return $conexion->execute_query([':idProducto' => $idProducto]);
    }
}
