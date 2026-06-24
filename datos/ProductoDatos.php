<?php

require_once __DIR__ . '/Conexion.php';

class ProductoDatos
{
    public function listarProductos($buscar = '')
    {
        $conexion = new Conexion();
        if (!empty($buscar)) {
            $conexion->query = "SELECT
                                productos.id_producto, productos.codigo_producto, productos.nombre_producto, productos.modelo_producto,
                                productos.descripcion_producto, productos.imagen_producto, marcas.nombre_marca, categorias.nombre_categoria,
                                productos.precio_producto, productos.stock_producto, productos.estado_producto
                                FROM productos
                                INNER JOIN categorias ON productos.id_categoria = categorias.id_categoria
                                INNER JOIN marcas ON productos.id_marca = marcas.id_marca
                                WHERE productos.eliminado_producto = 0
                                AND (productos.nombre_producto LIKE :buscar 
                                     OR productos.codigo_producto LIKE :buscar 
                                     OR productos.modelo_producto LIKE :buscar 
                                     OR categorias.nombre_categoria LIKE :buscar 
                                     OR marcas.nombre_marca LIKE :buscar)
                                ORDER BY productos.nombre_producto;";
            return $conexion->get_records([':buscar' => '%' . $buscar . '%']);
        } else {
            $conexion->query = "SELECT
                                productos.id_producto, productos.codigo_producto, productos.nombre_producto, productos.modelo_producto,
                                productos.descripcion_producto, productos.imagen_producto, marcas.nombre_marca, categorias.nombre_categoria,
                                productos.precio_producto, productos.stock_producto, productos.estado_producto
                                FROM productos
                                INNER JOIN categorias ON productos.id_categoria = categorias.id_categoria
                                INNER JOIN marcas ON productos.id_marca = marcas.id_marca
                                WHERE productos.eliminado_producto = 0
                                ORDER BY productos.nombre_producto;";
            return $conexion->get_records();
        }
    }

    private function valorNulo($valor)
    {
        return trim($valor) == '' ? null : trim($valor);
    }

    public function insertarProducto($producto)
    {
        $conexion = new Conexion();

        $conexion->query = "INSERT INTO productos 
                            (codigo_producto, nombre_producto, modelo_producto, descripcion_producto, imagen_producto, id_marca, id_categoria, precio_producto, stock_producto, estado_producto, eliminado_producto)
                            VALUES 
                            (:codigoProducto, :nombreProducto, :modeloProducto, :descripcionProducto, :imagenProducto, :idMarca, :idCategoria, :precioProducto, :stockProducto, :estadoProducto, 0)";

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
            ':estadoProducto'       => $producto['estado_producto']
        ]);
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
                            WHERE id_producto = :idProducto
                            AND eliminado_producto = 0";

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
            ':estadoProducto'       => $producto['estado_producto'],
            ':idProducto'           => $producto['id_producto'],
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
                            WHERE productos.id_producto = :idProducto
                            AND productos.eliminado_producto = 0
                            LIMIT 1";

        return $conexion->get_record([':idProducto' => $idProducto]);
    }

    public function obtenerProductoPorCodigo($codigo)
    {
        if (empty($codigo)) return null;
        $conexion = new Conexion();
        $conexion->query = "SELECT id_producto, eliminado_producto FROM productos WHERE codigo_producto = :codigo LIMIT 1";
        return $conexion->get_record([':codigo' => $codigo]);
    }

    public function reactivarProducto($producto)
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
                                estado_producto = :estadoProducto,
                                eliminado_producto = 0
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
            ':estadoProducto'       => $producto['estado_producto'],
            ':idProducto'           => $producto['id_producto'],
        ]);
    }


    public function eliminarProducto($idProducto)
    {
        $conexion = new Conexion();
        $conexion->query = "UPDATE productos SET eliminado_producto = 1 WHERE id_producto = :idProducto";
        return $conexion->execute_query([':idProducto' => $idProducto]);
    }

    public function productoTieneVentas($idProducto)
    {
        $conexion = new Conexion();
        // Validamos en el detalle_ventas
        $conexion->query = "SELECT COUNT(*) AS total FROM detalle_ventas WHERE id_producto = :idProducto";
        $resultado = $conexion->get_record([':idProducto' => $idProducto]);
        return $resultado['total'] > 0;
    }
    // NUEVO MÉTODO: Eliminación física (Hard Delete)
    public function eliminarFisicoProducto($idProducto)
    {
        $conexion = new Conexion();
        $conexion->query = "DELETE FROM productos WHERE id_producto = :idProducto";
        return $conexion->execute_query([':idProducto' => $idProducto]);
    }

    // NUEVO MÉTODO: Traer solo productos para la venta
    public function obtenerProductosActivos()
    {
        $conexion = new Conexion();
        $conexion->query = "SELECT
                            productos.id_producto, productos.codigo_producto, productos.nombre_producto, 
                            productos.precio_producto, productos.stock_producto, marcas.nombre_marca, categorias.nombre_categoria
                            FROM productos
                            INNER JOIN categorias ON productos.id_categoria = categorias.id_categoria
                            INNER JOIN marcas ON productos.id_marca = marcas.id_marca
                            WHERE productos.eliminado_producto = 0 
                            AND productos.estado_producto = 1
                            AND productos.stock_producto > 0
                            ORDER BY productos.nombre_producto;";

        return $conexion->get_records();
    }

    // NUEVO MÉTODO: Reducir stock tras una venta
    public function reducirStock($idProducto, $cantidad)
    {
        $conexion = new Conexion();
        $conexion->query = "UPDATE productos 
                            SET stock_producto = stock_producto - :cantidad 
                            WHERE id_producto = :idProducto 
                            AND stock_producto >= :cantidad";
        return $conexion->execute_query([
            ':idProducto' => $idProducto,
            ':cantidad' => $cantidad
        ]);
    }

    // NUEVO MÉTODO: Aumentar stock tras anular una venta
    public function aumentarStock($idProducto, $cantidad)
    {
        $conexion = new Conexion();
        $conexion->query = "UPDATE productos 
                            SET stock_producto = stock_producto + :cantidad 
                            WHERE id_producto = :idProducto";
        return $conexion->execute_query([
            ':idProducto' => $idProducto,
            ':cantidad' => $cantidad
        ]);
    }
}