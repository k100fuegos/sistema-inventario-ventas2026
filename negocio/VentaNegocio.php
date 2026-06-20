<?php

require_once dirname(__DIR__) . '/datos/VentaDatos.php';

class VentaNegocio {

    private $ventaDatos;

    public function __construct() {
        $this->ventaDatos = new VentaDatos();
    }

    public function listarVentas() {
        return $this->ventaDatos->listarVentas();
    }

    private function limpiarDatos($datos) {
        return [
            'fecha_venta' => isset($datos['fecha_venta']) ? trim($datos['fecha_venta']) : date('Y-m-d H:i:s'),
            'id_cliente' => isset($datos['id_cliente']) ? (int) $datos['id_cliente'] : 0,
            'id_usuario' => isset($datos['id_usuario']) ? (int) $datos['id_usuario'] : 0,
            'total_venta' => isset($datos['total_venta']) ? number_format((float) $datos['total_venta'], 2, '.', '') : 0,
            'estado' => isset($datos['estado']) ? trim($datos['estado']) : 'Pendiente',
            'observaciones' => isset($datos['observaciones']) ? trim($datos['observaciones']) : ''
        ];
    }

    private function validarVenta($datos) {
        $errores = [];

        if (!isset($datos['id_cliente']) || empty($datos['id_cliente']) || !is_numeric($datos['id_cliente'])) {
            $errores[] = "Debe seleccionar un cliente válido";
        }

        if (!isset($datos['id_usuario']) || empty($datos['id_usuario']) || !is_numeric($datos['id_usuario'])) {
            $errores[] = "Debe seleccionar un vendedor válido";
        }

        if (!isset($datos['fecha_venta']) || empty($datos['fecha_venta'])) {
            $errores[] = "La fecha de la venta es obligatoria";
        }

        return $errores;
    }

    public function crearVenta($datos, $productosArray) {
        $errores = $this->validarVenta($datos);

        if (empty($productosArray)) {
            $errores[] = "Debe agregar al menos un producto a la venta.";
        }

        if (!empty($errores)) {
            return ['exito' => false, 'errores' => $errores];
        }

        $venta = $this->limpiarDatos($datos);
        
        // Generar número de factura único (Máximo 20 caracteres)
        $venta['numero_factura'] = 'FAC-' . date('YmdHis'); // 4 + 14 = 18 chars

        // Recalcular el total por seguridad (Back-end validation)
        require_once dirname(__DIR__) . '/negocio/ProductoNegocio.php';
        $productoNegocio = new ProductoNegocio();
        
        $subtotalVenta = 0;
        $detallesProcesados = [];

        foreach ($productosArray as $prod) {
            $idProd = (int)$prod['id_producto'];
            $cant = (int)$prod['cantidad'];
            
            if ($cant <= 0) continue;

            $infoProd = $productoNegocio->obtenerProductoPorId($idProd);
            
            if (!$infoProd) {
                return ['exito' => false, 'errores' => ["El producto ID {$idProd} no existe o no está activo."]];
            }

            if ($infoProd['stock_producto'] < $cant) {
                return ['exito' => false, 'errores' => ["El producto '{$infoProd['nombre_producto']}' no tiene stock suficiente (Stock actual: {$infoProd['stock_producto']})."]];
            }

            $precioUnitario = (float)$infoProd['precio_producto'];
            $subtotalLinea = $cant * $precioUnitario;
            $subtotalVenta += $subtotalLinea;

            $detallesProcesados[] = [
                'id_producto' => $idProd,
                'cantidad_producto' => $cant,
                'precio_unitario' => $precioUnitario,
                'subtotal_detalle' => $subtotalLinea
            ];
        }

        if (empty($detallesProcesados)) {
            return ['exito' => false, 'errores' => ["Debe agregar cantidades válidas para los productos."]];
        }

        // Aplicar el IVA (13%)
        $iva = $subtotalVenta * 0.13;
        $total = $subtotalVenta + $iva;

        $venta['subtotal_venta'] = number_format($subtotalVenta, 2, '.', '');
        $venta['iva_venta'] = number_format($iva, 2, '.', '');
        $venta['total_venta'] = number_format($total, 2, '.', '');
        
        // Usar el estado que venga del formulario, o Realizada por defecto
        $venta['estado'] = isset($datos['estado_venta']) ? $datos['estado_venta'] : 'Realizada';

        $idNuevaVenta = $this->ventaDatos->insertarVenta($venta);

        if (!$idNuevaVenta) {
             return ['exito' => false, 'errores' => ['Error al guardar la venta principal.']];
        }

        // Insertar detalles y reducir stock
        require_once dirname(__DIR__) . '/datos/ProductoDatos.php';
        $productoDatos = new ProductoDatos();

        foreach ($detallesProcesados as $detalle) {
            $detalle['id_venta'] = $idNuevaVenta;
            $this->ventaDatos->insertarDetalleVenta($detalle);
            $productoDatos->reducirStock($detalle['id_producto'], $detalle['cantidad_producto']);
        }

        return [
            'exito' => true,
            'mensaje' => 'Venta registrada correctamente. Factura: ' . $venta['numero_factura']
        ];
    }

    public function obtenerVentaPorId($idVenta) {
        if (!is_numeric($idVenta) || $idVenta <= 0) {
            return null;
        }

        return $this->ventaDatos->obtenerVentaPorId($idVenta);
    }

    public function actualizarVenta($datos) {
        $errores = $this->validarVenta($datos);

        if (!isset($datos['id_venta']) || empty($datos['id_venta'])) {
            $errores[] = "El identificador de la venta es obligatorio";
        }

        if (!empty($errores)) {
            return [
                'exito' => false,
                'errores' => $errores
            ];
        }

        $venta = $this->limpiarDatos($datos);
        $venta['id_venta'] = (int) $datos['id_venta'];

        $resultado = $this->ventaDatos->actualizarVenta($venta);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Venta actualizada correctamente' : 'No se pudo actualizar la venta'
        ];
    }

    public function anularVenta($idVenta) {
        if (!is_numeric($idVenta) || $idVenta <= 0) {
            return [
                'exito' => false,
                'mensaje' => 'El identificador de la venta no es válido'
            ];
        }

        $ventaActual = $this->ventaDatos->obtenerVentaPorId($idVenta);
        if (!$ventaActual) {
            return ['exito' => false, 'mensaje' => 'La venta no existe.'];
        }
        
        if ($ventaActual['estado_venta'] === 'Anulada') {
            return ['exito' => false, 'mensaje' => 'La venta ya está anulada.'];
        }

        $resultado = $this->ventaDatos->anularVenta($idVenta);

        if ($resultado) {
            // Restaurar stock de los productos
            require_once dirname(__DIR__) . '/datos/ProductoDatos.php';
            $productoDatos = new ProductoDatos();
            
            $detalles = $this->ventaDatos->obtenerDetalleVenta($idVenta);
            foreach ($detalles as $detalle) {
                $productoDatos->aumentarStock($detalle['id_producto'], $detalle['cantidad_producto']);
            }
        }

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Venta anulada correctamente y stock devuelto a bodega' : 'No se pudo anular la venta'
        ];
    }

    public function obtenerDetalleVenta($idVenta) {
        if (!is_numeric($idVenta) || $idVenta <= 0) {
            return [];
        }

        return $this->ventaDatos->obtenerDetalleVenta($idVenta);
    }

    public function insertarDetalleVenta($detalle) {
        return $this->ventaDatos->insertarDetalleVenta($detalle);
    }
}
