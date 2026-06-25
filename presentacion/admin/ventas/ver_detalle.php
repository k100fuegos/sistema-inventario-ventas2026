<?php
require_once __DIR__ . '/../../../config/control_acceso.php';
requerirLogin();
requerirRol([ROL_ADMIN, ROL_SUPERVISOR, ROL_VENDEDOR]);

require_once '../../../negocio/VentaNegocio.php';

$id_venta = $_GET['id'] ?? null;

if (!$id_venta) {
    header('Location: listar.php');
    exit;
}

$ventaNegocio = new VentaNegocio();
$venta = $ventaNegocio->obtenerVentaPorId($id_venta);

if (!$venta) {
    header('Location: listar.php');
    exit;
}

$detalles = $ventaNegocio->obtenerDetalleVenta($id_venta);

function mostrarValor($valor)
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles de Venta - Tecnobyte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="d-flex">
        <!-- SIDEBAR -->
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>

        <div id="content">
            <?php include __DIR__ . '/../../includes/header.php'; ?>

            <div class="container-fluid p-4">
                 <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                     <h2 class="text-dark fw-bold mb-0"><i class="fa-solid fa-file-invoice" style="color: var(--color-secundario);"></i> Detalles de Venta</h2>
                     <div class="row gx-2 m-0 w-100 justify-content-lg-end" style="max-width: 500px;">
                         <div class="col-6">
                             <a href="descargar_pdf.php?id=<?php echo (int)$id_venta; ?>" target="_blank" class="btn btn-outline-danger fw-bold w-100 h-100 d-flex justify-content-center align-items-center text-wrap"><i class="fa-solid fa-file-pdf me-2"></i> Descargar PDF</a>
                         </div>
                         <div class="col-6">
                             <a href="listar.php" class="btn btn-secondary fw-bold w-100 h-100 d-flex justify-content-center align-items-center text-wrap"><i class="fa-solid fa-arrow-left me-2"></i> Volver al Historial</a>
                         </div>
                     </div>
                 </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header text-white fw-bold" style="background-color: var(--color-primario);">
                        <i class="fa-solid fa-circle-info"></i> Información General de la Venta
                    </div>
                    <div class="card-body">
                        <div class="row text-center text-md-start">
                            <div class="col-md-3 mb-3">
                                <small class="text-muted d-block fw-bold">Número de Factura</small>
                                <span class="fs-5 text-dark fw-bold"><?php echo mostrarValor($venta['numero_factura']); ?></span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <small class="text-muted d-block fw-bold">Fecha de Venta</small>
                                <span class="fs-5 text-dark"><?php echo mostrarValor($venta['fecha_venta']); ?></span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <small class="text-muted d-block fw-bold">Cliente</small>
                                <span class="fs-5 text-dark"><?php echo mostrarValor($venta['nombre_cliente']); ?></span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <small class="text-muted d-block fw-bold">Estado</small>
                                <?php if ($venta['estado_venta'] === 'Realizada'): ?>
                                    <span class="badge bg-success fs-6">Realizada</span>
                                <?php elseif ($venta['estado_venta'] === 'Pendiente'): ?>
                                    <span class="badge bg-warning text-dark fs-6">Pendiente</span>
                                <?php else: ?>
                                    <span class="badge bg-danger fs-6">Anulada</span>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-3 mb-3">
                                <small class="text-muted d-block fw-bold">Vendedor</small>
                                <span class="fs-5 text-dark"><?php echo mostrarValor($venta['nombre_usuario']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header text-white fw-bold" style="background-color: var(--color-secundario);">
                        <i class="fa-solid fa-boxes-stacked"></i> Productos de la Venta
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Imagen</th>
                                    <th>Producto</th>
                                    <th>Precio Unitario</th>
                                    <th>Cantidad</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($detalles)): ?>
                                    <?php foreach ($detalles as $detalle): ?>
                                        <tr>
                                            <td class="text-muted fw-bold"><?php echo mostrarValor($detalle['codigo_producto']); ?></td>
                                            <td>
                                                <?php if (!empty($detalle['imagen_producto']) && file_exists('../../../public/img/productos/' . $detalle['imagen_producto'])): ?>
                                                    <img src="../../../public/img/productos/<?php echo mostrarValor($detalle['imagen_producto']); ?>" alt="Producto" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php else: ?>
                                                    <img src="../../../public/img/sin-imagen.png" alt="Sin imagen" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo mostrarValor($detalle['nombre_producto']); ?></td>
                                            <td>$ <?php echo number_format((float) $detalle['precio_unitario'], 2); ?></td>
                                            <td><?php echo mostrarValor($detalle['cantidad_producto']); ?></td>
                                            <td class="fw-bold">$ <?php echo number_format((float) $detalle['subtotal_detalle'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">No se encontraron productos en esta venta.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="text-end fw-bold">Subtotal Venta:</td>
                                    <td class="fw-bold">$ <?php echo number_format((float) $venta['subtotal_venta'], 2); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end fw-bold">IVA:</td>
                                    <td class="fw-bold text-danger">+ $ <?php echo number_format((float) $venta['iva_venta'], 2); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end fw-bold fs-5">Total a Pagar:</td>
                                    <td class="fw-bold fs-5 text-success">$ <?php echo number_format((float) $venta['total_venta'], 2); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../public/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
