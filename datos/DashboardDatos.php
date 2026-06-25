<?php
require_once __DIR__ . '/Conexion.php';

class DashboardDatos {
    public function obtenerVentasDelDia() {
        $conexion = new Conexion();
        $conexion->query = "SELECT IFNULL(SUM(total_venta), 0) as total_dia FROM ventas WHERE DATE(fecha_venta) = CURDATE() AND estado_venta = 'Realizada'";
        $resultado = $conexion->get_record();
        return $resultado ? $resultado['total_dia'] : 0;
    }

    public function obtenerTransaccionesDelDia() {
        $conexion = new Conexion();
        $conexion->query = "SELECT COUNT(id_venta) as transacciones FROM ventas WHERE DATE(fecha_venta) = CURDATE() AND estado_venta = 'Realizada'";
        $resultado = $conexion->get_record();
        return $resultado ? $resultado['transacciones'] : 0;
    }

    public function obtenerConteoStockBajo() {
        $conexion = new Conexion();
        $conexion->query = "SELECT COUNT(id_producto) as stock_bajo FROM productos WHERE stock_producto <= 5 AND estado_producto = 1 AND eliminado_producto = 0";
        $resultado = $conexion->get_record();
        return $resultado ? $resultado['stock_bajo'] : 0;
    }

    public function obtenerListaStockBajo() {
        $conexion = new Conexion();
        $conexion->query = "SELECT codigo_producto, nombre_producto, stock_producto FROM productos WHERE stock_producto <= 5 AND estado_producto = 1 AND eliminado_producto = 0 ORDER BY stock_producto ASC";
        return $conexion->get_records();
    }

    public function obtenerNuevosClientes() {
        $conexion = new Conexion();
        $conexion->query = "SELECT COUNT(id_cliente) as nuevos_clientes FROM clientes WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) AND estado_cliente = 1";
        $resultado = $conexion->get_record();
        return $resultado ? $resultado['nuevos_clientes'] : 0;
    }

    public function obtenerUltimasVentas() {
        $conexion = new Conexion();
        $conexion->query = "SELECT v.numero_factura, c.nombre_cliente, v.total_venta, v.estado_venta, v.fecha_venta 
                            FROM ventas v 
                            INNER JOIN clientes c ON v.id_cliente = c.id_cliente 
                            ORDER BY v.fecha_venta DESC LIMIT 5";
        return $conexion->get_records();
    }

    public function obtenerTopProductos() {
        $conexion = new Conexion();
        $conexion->query = "SELECT p.nombre_producto, SUM(dv.cantidad_producto) as total_vendido 
                            FROM detalle_ventas dv 
                            INNER JOIN productos p ON dv.id_producto = p.id_producto 
                            INNER JOIN ventas v ON dv.id_venta = v.id_venta 
                            WHERE v.estado_venta = 'Realizada' 
                            GROUP BY p.id_producto 
                            ORDER BY total_vendido DESC LIMIT 5";
        return $conexion->get_records();
    }

    public function obtenerVentasPorCategoria() {
        $conexion = new Conexion();
        $conexion->query = "SELECT c.nombre_categoria, SUM(dv.cantidad_producto) as cantidad_por_categoria 
                            FROM detalle_ventas dv 
                            INNER JOIN productos p ON dv.id_producto = p.id_producto 
                            INNER JOIN categorias c ON p.id_categoria = c.id_categoria 
                            INNER JOIN ventas v ON dv.id_venta = v.id_venta 
                            WHERE v.estado_venta = 'Realizada' 
                            GROUP BY c.id_categoria";
        return $conexion->get_records();
    }
}
