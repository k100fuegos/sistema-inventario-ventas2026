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

    public function crearVenta($datos) {
        $errores = $this->validarVenta($datos);

        if (!empty($errores)) {
            return [
                'exito' => false,
                'errores' => $errores
            ];
        }

        $venta = $this->limpiarDatos($datos);
        $resultado = $this->ventaDatos->insertarVenta($venta);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Venta registrada correctamente' : 'No se pudo registrar la venta'
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

        $resultado = $this->ventaDatos->anularVenta($idVenta);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Venta anulada correctamente' : 'No se pudo anular la venta'
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
