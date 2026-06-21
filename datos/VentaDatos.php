<?php

require_once __DIR__ . '/Conexion.php';

class VentaDatos
{
    public function listarVentas($buscar = '')
    {
        $conexion = new Conexion();
        if (!empty($buscar)) {
            $conexion->query = "SELECT 
                                    v.id_venta, 
                                    v.numero_factura, 
                                    v.fecha_venta, 
                                    c.nombre_cliente, 
                                    u.nombre_usuario, 
                                    v.subtotal_venta, 
                                    v.iva_venta, 
                                    v.total_venta, 
                                    v.estado_venta 
                                FROM ventas v
                                INNER JOIN clientes c ON v.id_cliente = c.id_cliente
                                INNER JOIN usuarios u ON v.id_usuario = u.id_usuario
                                WHERE v.numero_factura LIKE :buscar 
                                   OR c.nombre_cliente LIKE :buscar 
                                   OR u.nombre_usuario LIKE :buscar
                                ORDER BY v.fecha_venta DESC";
            return $conexion->get_records([':buscar' => '%' . $buscar . '%']);
        } else {
            $conexion->query = "SELECT 
                                    v.id_venta, 
                                    v.numero_factura, 
                                    v.fecha_venta, 
                                    c.nombre_cliente, 
                                    u.nombre_usuario, 
                                    v.subtotal_venta, 
                                    v.iva_venta, 
                                    v.total_venta, 
                                    v.estado_venta 
                                FROM ventas v
                                INNER JOIN clientes c ON v.id_cliente = c.id_cliente
                                INNER JOIN usuarios u ON v.id_usuario = u.id_usuario
                                ORDER BY v.fecha_venta DESC";
            return $conexion->get_records();
        }
    }

    // Insertar la venta principal y recuperar su ID
    public function insertarVenta($venta) { 
        $conexion = new Conexion();
        
        $conexion->query = "INSERT INTO ventas 
                            (numero_factura, fecha_venta, id_cliente, id_usuario, subtotal_venta, iva_venta, total_venta, estado_venta) 
                            VALUES 
                            (:numeroFactura, :fechaVenta, :idCliente, :idUsuario, :subtotal, :iva, :total, :estado)";
        
        $exito = $conexion->execute_query([
            ':numeroFactura' => $venta['numero_factura'],
            ':fechaVenta'    => $venta['fecha_venta'],
            ':idCliente'     => $venta['id_cliente'],
            ':idUsuario'     => $venta['id_usuario'],
            ':subtotal'      => $venta['subtotal_venta'],
            ':iva'           => $venta['iva_venta'],
            ':total'         => $venta['total_venta'],
            ':estado'        => $venta['estado']
        ]);

        if ($exito) {
            // Recuperamos el ID recién insertado de forma segura usando el número de factura único
            $conexion2 = new Conexion();
            $conexion2->query = "SELECT id_venta FROM ventas WHERE numero_factura = :numeroFactura LIMIT 1";
            $resultado = $conexion2->get_record([':numeroFactura' => $venta['numero_factura']]);
            return $resultado ? $resultado['id_venta'] : false;
        }

        return false;
    }
    public function obtenerVentaPorId($idVenta) {
        $conexion = new Conexion();
        $conexion->query = "SELECT 
                                v.id_venta, 
                                v.numero_factura, 
                                v.fecha_venta, 
                                v.id_cliente,
                                c.nombre_cliente, 
                                v.id_usuario,
                                u.nombre_usuario, 
                                v.subtotal_venta, 
                                v.iva_venta, 
                                v.total_venta, 
                                v.estado_venta 
                            FROM ventas v
                            INNER JOIN clientes c ON v.id_cliente = c.id_cliente
                            INNER JOIN usuarios u ON v.id_usuario = u.id_usuario
                            WHERE v.id_venta = :idVenta
                            LIMIT 1";
        return $conexion->get_record([':idVenta' => $idVenta]);
    }
    public function actualizarVenta($venta) {
        $conexion = new Conexion();
        $conexion->query = "UPDATE ventas 
                            SET id_cliente = :idCliente, 
                                id_usuario = :idUsuario, 
                                fecha_venta = :fechaVenta, 
                                estado_venta = :estadoVenta
                            WHERE id_venta = :idVenta";
        
        $parametros = [
            ':idCliente' => $venta['id_cliente'],
            ':idUsuario' => $venta['id_usuario'],
            ':fechaVenta' => $venta['fecha_venta'],
            ':estadoVenta' => $venta['estado'],
            ':idVenta' => $venta['id_venta']
        ];
        
        return $conexion->execute_query($parametros);
    }
    public function anularVenta($idVenta) {
        $conexion = new Conexion();
        $conexion->query = "UPDATE ventas SET estado_venta = 'Anulada' WHERE id_venta = :idVenta";
        return $conexion->execute_query([':idVenta' => $idVenta]);
    }
    public function obtenerDetalleVenta($idVenta) {
        $conexion = new Conexion();
        $conexion->query = "SELECT 
                                dv.id_detalle, 
                                dv.id_venta, 
                                dv.id_producto, 
                                p.nombre_producto,
                                p.codigo_producto,
                                dv.cantidad_producto, 
                                dv.precio_unitario, 
                                dv.subtotal_detalle
                            FROM detalle_ventas dv
                            INNER JOIN productos p ON dv.id_producto = p.id_producto
                            WHERE dv.id_venta = :idVenta";
        return $conexion->get_records([':idVenta' => $idVenta]);
    }
    public function insertarDetalleVenta($detalle) { 
        $conexion = new Conexion();
        $conexion->query = "INSERT INTO detalle_ventas 
                            (id_venta, id_producto, cantidad_producto, precio_unitario, subtotal_detalle) 
                            VALUES 
                            (:idVenta, :idProducto, :cantidad, :precioUnitario, :subtotal)";
        
        return $conexion->execute_query([
            ':idVenta'       => $detalle['id_venta'],
            ':idProducto'    => $detalle['id_producto'],
            ':cantidad'      => $detalle['cantidad_producto'],
            ':precioUnitario'=> $detalle['precio_unitario'],
            ':subtotal'      => $detalle['subtotal_detalle']
        ]);
    }
}
