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
            ':codigoProducto'       => $producto['CodigoProducto'],
            ':nombreProducto'       => $producto['NombreProducto'],
            ':modeloProducto'       => $this->valorNulo($producto['ModeloProducto']),
            ':descripcionProducto'  => $this->valorNulo($producto['DescripcionProducto']),
            ':imagenProducto'       => $this->valorNulo($producto['ImagenProducto']),
            ':idMarca'              => $producto['IdMarca'],
            ':idCategoria'          => $producto['IdCategoria'],
            ':precioProducto'       => $producto['PrecioProducto'],
            ':stockProducto'        => $producto['StockProducto'],
        ]);
    }

    public function obtenerProductoPorId($idProducto)
    {
        $conexion = new Conexion();

        $conexion->query = "SELECT productos.id_producto, productos.codigo_producto, productos.nombre_producto, productos.modelo_producto, productos.descripcion_producto, 
                            productos.imagen_producto, marcas.nombre_marca, categoria.nombre_categoria, productos.precio_producto, productos.stock_producto, 
                            productos.estado_producto 
                            FROM productos
                            INNER JOIN categorias ON productos.id_categoria = categorias.id_categoria
                            INNER JOIN marcas ON productos.id_marca = marcas.id_marca
                            WHERE productos.id_producto = :idProducto
                            AND productos.id_producto = :idProducto";

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
                                stock_producto = :stockProducto,
                                estado_producto = :estadoProducto
                            WHERE id_producto = :idProducto";

        return $conexion->execute_query([
            ':codigoProducto'       => $producto['CodigoProducto'],
            ':nombreProducto'       => $producto['NombreProducto'],
            ':modeloProducto'       => $this->valorNulo($producto['ModeloProducto']),
            ':descripcionProducto'  => $this->valorNulo($producto['DescripcionProducto']),
            ':imagenProducto'       => $this->valorNulo($producto['ImagenProducto']),
            ':idMarca'              => $producto['IdMarca'],
            ':idCategoria'          => $producto['IdCategoria'],
            ':precioProducto'       => $producto['PrecioProducto'],
            ':stockProducto'        => $producto['StockProducto'],
            ':estadoProducto'       => $producto['EstadoProducto'],
            ':idProducto'           => $producto['idProducto']
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
