<?php
require_once '../../../config/control_acceso.php';
requerirLogin();
requerirRol([ROL_ADMIN, ROL_SUPERVISOR, ROL_VENDEDOR]);

require_once '../../../negocio/VentaNegocio.php';

$buscar = trim($_GET['buscar_venta'] ?? '');
$ventaNegocio = new VentaNegocio();
$ventas = $ventaNegocio->listarVentas($buscar);

$mensaje = $_GET['mensaje'] ?? '';
$idVentaReciente = $_GET['id_venta'] ?? null;

function mostrarValor($valor)
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}



?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial Ventas - Tecnobyte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/style.css?v=<?php echo time(); ?>">
    <style>
        /* Estilos para congelar la columna de acciones en scroll horizontal */
        .sticky-acciones {
            position: sticky;
            right: 0;
            background-color: #fff;
            z-index: 5;
            box-shadow: -2px 0 5px rgba(0,0,0,0.05);
        }
        .table-dark .sticky-acciones {
            background-color: #212529; /* Color de fondo del thead dark */
            color: #fff;
            z-index: 15;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>

        <div id="content">
            <?php include __DIR__ . '/../../includes/header.php'; ?>

            <div class="container-fluid p-4">
                <h2 class="mb-4 text-dark fw-bold"><i class="fa-solid fa-file-invoice-dollar text-secondary"></i> Historial de Ventas</h2>
                <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
                    <form action="" method="GET" class="flex-grow-1" style="max-width: 600px;">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white text-muted border-end-0"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" name="buscar_venta" placeholder="Buscar por número de factura, cliente o vendedor..." value="<?php echo mostrarValor($buscar); ?>">
                            <button type="button" class="btn btn-outline-secondary btn-reset-search" title="Limpiar búsqueda"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    </form>
                    <div class="d-grid d-md-block">
                        <a href="crear.php" class="btn btn-success fw-bold">
                            <i class="fa-solid fa-cart-plus"></i> Ir al Punto de Venta
                        </a>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="table-responsive rounded-2" style="max-height: 65vh; overflow-y: auto;">
                        <table class="table table-hover table-striped mb-0 align-middle text-center">
                            <thead class="table-dark" style="position: sticky; top: 0; z-index: 10;">
                                <tr>
                                    <th>Factura N°</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Vendedor</th>
                                    <th>Subtotal</th>
                                    <th>IVA</th>
                                    <th>Total</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center sticky-acciones">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($ventas)): ?>
                                    <?php foreach ($ventas as $venta): ?>
                                        <tr>
                                            <td class="fw-bold" style="color: var(--color-secundario);"><?php echo mostrarValor($venta['numero_factura']); ?></td>
                                            <td class="fw-bold text-dark"><?php echo mostrarValor(date('Y-m-d h:i:s A', strtotime($venta['fecha_venta']))); ?></td>
                                            <td><?php echo mostrarValor($venta['nombre_cliente']); ?></td>
                                            <td><?php echo mostrarValor($venta['nombre_usuario']); ?></td>
                                            <td>$ <?php echo mostrarValor(number_format($venta['subtotal_venta'], 2)); ?></td>
                                            <td>$ <?php echo mostrarValor(number_format($venta['iva_venta'], 2)); ?></td>
                                            <td class="fw-bold text-success">$ <?php echo mostrarValor(number_format($venta['total_venta'], 2)); ?></td>
                                             <td class="text-center">
                                                <?php if ($venta['estado_venta'] === 'Realizada'): ?>
                                                    <span class="badge bg-success">Realizada</span>
                                                <?php elseif ($venta['estado_venta'] === 'Pendiente'): ?>
                                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Anulada</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center sticky-acciones">
                                                <a href="ver_detalle.php?id=<?php echo $venta['id_venta']; ?>" class="btn btn-sm btn-outline-info" title="Ver Detalle"><i class="fa-solid fa-eye"></i></a>
                                                <!-- pdf-removed -->
                                                <?php if ($venta['estado_venta'] === 'Realizada' || $venta['estado_venta'] === 'Pendiente'): ?><?php if (tieneRol([ROL_ADMIN, ROL_SUPERVISOR])): ?>
                                                    <a href="editar.php?id=<?php echo $venta['id_venta']; ?>" class="btn btn-sm btn-outline-primary" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a>
                                                    <a href="anular.php?id=<?php echo $venta['id_venta']; ?>" class="btn btn-sm btn-outline-danger" title="Anular"><i class="fa-solid fa-ban"></i></a>
                                                <?php endif; ?><?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                         <td colspan="9" class="text-center">No se encontraron registros</td>
                                     </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../public/js/main.js?v=<?php echo time(); ?>"></script>
    <script src="../../../public/js/notificacion.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if ($mensaje === 'creado' && $idVentaReciente): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                title: '¡Venta Registrada!',
                text: '¿Deseas descargar la factura de esta venta en PDF?',
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: '<i class="fa-solid fa-file-pdf"></i> Descargar PDF',
                cancelButtonText: 'Cerrar',
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open('descargar_pdf.php?id=<?php echo (int)$idVentaReciente; ?>', '_blank');
                }
            });
        });
    </script>
    <?php endif; ?>

    <?php

    $mensajeToast = '';
    $tipoToast = '';

    switch ($mensaje) {

        case 'actualizado':
            $mensajeToast = 'Venta actualizada correctamente.';
            $tipoToast = 'success';
            break;

        case 'anulado':
            $mensajeToast = 'Venta anulada correctamente.';
            $tipoToast = 'success';
            break;

        case 'error':
            $mensajeToast = 'Ha ocurrido un error.';
            $tipoToast = 'error';
            break;
    }

    ?>

    <div class="toast-container position-fixed top-0 end-0 p-3">

        <div
            id="toastMensaje"
            class="toast border-0"
            role="alert"
            data-mensaje="<?php echo $mensajeToast; ?>"
            data-tipo="<?php echo $tipoToast; ?>">

            <div class="toast-header">

                <i id="toastIcono"></i>

                <strong id="toastTitulo" class="me-auto"></strong>

                <small>Ahora</small>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="toast">
                </button>

            </div>

            <div class="toast-body" id="toastCuerpo"></div>

        </div>

    </div>
</body>
</html>