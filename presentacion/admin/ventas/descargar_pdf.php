<?php
require_once __DIR__ . '/../../../config/control_acceso.php';
requerirLogin();

require_once __DIR__ . '/../../../negocio/VentaNegocio.php';
require_once __DIR__ . '/../../../negocio/ClienteNegocio.php';
require_once __DIR__ . '/../../../negocio/lib/fpdf/fpdf.php';

$id_venta = $_GET['id'] ?? null;

if (!$id_venta || !is_numeric($id_venta)) {
    die("Identificador de venta no válido.");
}

$ventaNegocio = new VentaNegocio();
$venta = $ventaNegocio->obtenerVentaPorId($id_venta);

if (!$venta) {
    die("La venta especificada no existe.");
}

$clienteNegocio = new ClienteNegocio();
$cliente = $clienteNegocio->obtenerClientePorId($venta['id_cliente']);

$detalles = $ventaNegocio->obtenerDetalleVenta($id_venta);

// Función auxiliar para evitar advertencias de depreciación de utf8_decode en PHP 8.2+
function u8($texto) {
    if (empty($texto)) return '';
    if (function_exists('mb_convert_encoding')) {
        return mb_convert_encoding($texto, 'ISO-8859-1', 'UTF-8');
    } elseif (function_exists('iconv')) {
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $texto);
    }
    return @utf8_decode($texto);
}

// Pasar número de factura de forma global para usarlo en la cabecera del PDF
$GLOBALS['factura_n'] = $venta['numero_factura'];

class PDF_Factura extends FPDF {
    // Cabecera de página
    function Header() {
        // Logo o Nombre de la Empresa
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(19, 51, 81); // Color primario
        $this->Cell(0, 8, u8('TECHNOBYTE'), 0, 1, 'L');
        
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(120, 5, u8('Sistema de Control de Inventario y Ventas'), 0, 0, 'L');
        $this->Cell(0, 5, u8('Fecha Impresión: ' . date('d/m/Y h:i A')), 0, 1, 'R');
        
        // Cuadro de Factura a la derecha
        $this->SetXY(140, 10);
        $this->SetFillColor(240, 240, 240);
        $this->Rect(140, 10, 60, 18, 'F');
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(19, 51, 81);
        $this->SetXY(140, 11);
        $this->Cell(60, 5, u8('FACTURA COMERCIAL'), 0, 1, 'C');
        $this->SetFont('Arial', 'B', 11);
        $this->SetTextColor(220, 53, 69); // Rojo de alerta
        $this->SetX(140);
        $this->Cell(60, 5, $GLOBALS['factura_n'], 0, 1, 'C');
        
        // Línea divisora
        $this->SetXY(10, 28);
        $this->SetDrawColor(19, 51, 81);
        $this->SetLineWidth(0.5);
        $this->Line(10, 31, 200, 31);
        $this->Ln(8);
    }

    // Pie de página
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 10, u8('Technobyte - Gracias por su preferencia - Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Inicialización de FPDF
$pdf = new PDF_Factura('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetMargins(10, 10, 10);

// --- BLOQUE DE INFORMACIÓN GENERAL ---
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(19, 51, 81);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(92, 6, u8('INFORMACIÓN DEL CLIENTE'), 0, 0, 'L', true);
$pdf->Cell(6, 6, '', 0, 0); // Espacio intermedio
$pdf->Cell(92, 6, u8('INFORMACIÓN DE LA VENTA'), 0, 1, 'L', true);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 9);

// Datos del Cliente (Celda Izquierda)
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->Rect($x, $y, 92, 34); // Contorno para el cliente
$pdf->SetXY($x + 2, $y + 2);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(90, 5, u8('Nombre: ') . u8($venta['nombre_cliente']), 0, 1);
$pdf->SetFont('Arial', '', 9);

// Cargar DUI/NIT/NRC
$docs = [];
if ($cliente) {
    if (!empty($cliente['dui_cliente'])) $docs[] = "DUI: " . $cliente['dui_cliente'];
    if (!empty($cliente['nit_cliente'])) $docs[] = "NIT: " . $cliente['nit_cliente'];
    if (!empty($cliente['nrc_cliente'])) $docs[] = "NRC: " . $cliente['nrc_cliente'];
}
$docString = count($docs) > 0 ? implode(" | ", $docs) : "Documento: Ninguno";

$pdf->SetX($x + 2);
$pdf->Cell(90, 5, u8($docString), 0, 1);

$pdf->SetX($x + 2);
$pdf->Cell(90, 5, u8('Teléfono: ') . ($cliente && !empty($cliente['telefono_cliente']) ? $cliente['telefono_cliente'] : 'N/A'), 0, 1);
$pdf->SetX($x + 2);
$pdf->Cell(90, 5, u8('Dirección: ') . ($cliente && !empty($cliente['direccion_cliente']) ? u8($cliente['direccion_cliente']) : 'N/A'), 0, 1);

// Datos de la Venta (Celda Derecha)
$pdf->SetXY($x + 98, $y);
$pdf->Rect($x + 98, $y, 92, 34); // Contorno para la venta
$pdf->SetXY($x + 100, $y + 2);
$pdf->Cell(90, 5, u8('Fecha Venta: ') . $venta['fecha_venta'], 0, 1);
$pdf->SetX($x + 100);
$pdf->Cell(90, 5, u8('Vendedor: ') . u8($venta['nombre_usuario']), 0, 1);
$pdf->SetX($x + 100);
$pdf->Cell(90, 5, u8('Estado Pago: ') . u8($venta['estado_venta']), 0, 1);
$pdf->SetX($x + 100);
$pdf->Cell(90, 5, u8('Tipo de Moneda: USD ($)'), 0, 1);

$pdf->SetXY(10, $y + 38);
$pdf->Ln(2);

// --- TABLA DE DETALLES ---
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(120, 120, 120);
$pdf->SetTextColor(255, 255, 255);

// Cabecera de la tabla
$pdf->Cell(25, 7, u8('Código'), 1, 0, 'C', true);
$pdf->Cell(95, 7, u8('Descripción del Producto'), 1, 0, 'L', true);
$pdf->Cell(25, 7, u8('Precio Unit.'), 1, 0, 'C', true);
$pdf->Cell(20, 7, u8('Cant.'), 1, 0, 'C', true);
$pdf->Cell(25, 7, u8('Subtotal'), 1, 1, 'C', true);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 9);

$fill = false;
$pdf->SetFillColor(245, 245, 245);

foreach ($detalles as $detalle) {
    $pdf->Cell(25, 6, u8($detalle['codigo_producto']), 1, 0, 'C', $fill);
    $pdf->Cell(95, 6, u8($detalle['nombre_producto']), 1, 0, 'L', $fill);
    $pdf->Cell(25, 6, '$' . number_format($detalle['precio_unitario'], 2), 1, 0, 'C', $fill);
    $pdf->Cell(20, 6, $detalle['cantidad_producto'], 1, 0, 'C', $fill);
    $pdf->Cell(25, 6, '$' . number_format($detalle['subtotal_detalle'], 2), 1, 1, 'C', $fill);
    $fill = !$fill;
}

$pdf->Ln(2);

// --- BLOQUE DE TOTALES ---
$yTotal = $pdf->GetY();
// Evitar que el cuadro de totales se desborde al pie de página
if ($yTotal > 240) {
    $pdf->AddPage();
    $yTotal = $pdf->GetY();
}

$pdf->SetXY(130, $yTotal);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 6, u8('Subtotal Venta:'), 0, 0, 'R');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(35, 6, '$' . number_format($venta['subtotal_venta'], 2), 1, 1, 'R');

$pdf->SetX(130);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(35, 6, u8('IVA (13%):'), 0, 0, 'R');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(35, 6, '$' . number_format($venta['iva_venta'], 2), 1, 1, 'R');

$pdf->SetX(130);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230, 245, 230);
$pdf->Cell(35, 7, u8('Total a Pagar:'), 0, 0, 'R');
$pdf->Cell(35, 7, '$' . number_format($venta['total_venta'], 2), 1, 1, 'R', true);

// Cuadro de firmas / términos a la izquierda de los totales
$pdf->SetXY(10, $yTotal);
$pdf->SetFont('Arial', 'I', 8);
$pdf->SetTextColor(100, 100, 100);
$pdf->MultiCell(110, 4, u8("Términos y condiciones:\n1. Toda reclamación debe presentarse en un plazo máximo de 15 días hábiles.\n2. La mercancía viaja por cuenta y riesgo del comprador.\n3. Este documento constituye una representación impresa de una venta comercial."), 0, 'L');

$pdf->SetTextColor(0, 0, 0);

// Salida del PDF en el navegador (Abrir en pestaña nueva)
$pdf->Output('I', 'Factura_' . $venta['numero_factura'] . '.pdf');
