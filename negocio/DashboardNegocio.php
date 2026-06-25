<?php
require_once dirname(__DIR__) . '/datos/DashboardDatos.php';

class DashboardNegocio {
    private $dashboardDatos;

    public function __construct() {
        $this->dashboardDatos = new DashboardDatos();
    }

    public function obtenerResumen() {
        return [
            'ventasDia' => $this->dashboardDatos->obtenerVentasDelDia(),
            'transaccionesDia' => $this->dashboardDatos->obtenerTransaccionesDelDia(),
            'stockBajo' => $this->dashboardDatos->obtenerConteoStockBajo(),
            'listaStockBajo' => $this->dashboardDatos->obtenerListaStockBajo(),
            'nuevosClientes' => $this->dashboardDatos->obtenerNuevosClientes(),
            'ultimasVentas' => $this->dashboardDatos->obtenerUltimasVentas(),
            'topProductos' => $this->dashboardDatos->obtenerTopProductos(),
            'ventasPorCategoria' => $this->dashboardDatos->obtenerVentasPorCategoria()
        ];
    }
}
