<?php
require_once __DIR__ . '/../../../config/control_acceso.php';
requerirRol([ROL_ADMIN, ROL_SUPERVISOR]);

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
</head>
<body>
    <div class="d-flex">
        <nav id="sidebar">
            <div class="sidebar-header d-flex align-items-center justify-content-center py-3">
                <img src="../../../public/img/logo-nav.svg" alt="Logo" class="img-fluid me-2" style="max-width: 40px;">
                <h4 class="fw-bold mb-0">Technobyte</h4>
            </div>
            <ul class="list-unstyled components">
                <li><a href="../../dashboard.php"><i class="fa-solid fa-house"></i> Panel Principal</a></li>
                <li><a href="crear.php"><i class="fa-solid fa-cart-shopping"></i> Nueva Venta</a></li>
                <?php if(tieneRol([ROL_ADMIN, ROL_SUPERVISOR])): ?>
                <li class="active"><a href="listar.php"><i class="fa-solid fa-file-invoice-dollar"></i> Historial Ventas</a></li>
                <?php endif; ?>
                <?php if(tieneRol([ROL_ADMIN, ROL_SUPERVISOR])): ?>
                <li><a href="../categorias/listar.php"><i class="fa-solid fa-tags"></i> Categorías</a></li>
                <?php endif; ?>
                <?php if(tieneRol([ROL_ADMIN, ROL_SUPERVISOR])): ?>
                <li><a href="../marcas/listar.php"><i class="fa-solid fa-tags"></i> Marcas</a></li>
                <?php endif; ?>
                <li><a href="../productos/listar.php"><i class="fa-solid fa-cubes"></i> Productos</a></li>
                <li><a href="../clientes/listar.php"><i class="fa-solid fa-users"></i> Clientes</a></li>
                <?php if(tieneRol([ROL_ADMIN])): ?>
                <li><a href="../usuarios/listar.php"><i class="fa-solid fa-user-shield"></i> Usuarios</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <div id="content">
                        <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-dorado"><i class="fa-solid fa-bars"></i></button>
                    <div class="ms-auto d-flex align-items-center">
                        <span class="me-3 fw-bold"><i class="fa-solid fa-circle-user"></i> <?= htmlspecialchars($_SESSION['nombre'] ?? 'Usuario', ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($_SESSION['nombre_rol'] ?? 'Rol', ENT_QUOTES, 'UTF-8') ?>)</span>
                        <a href="../../../logout.php" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
                    </div>
                </div>
            </nav>

            <div class="container-fluid p-4">
                <h2 class="mb-4 text-dark fw-bold"><i class="fa-solid fa-file-invoice-dollar text-secondary"></i> Historial de Ventas</h2>
                <div class="row mb-4 align-items-center">
                    <div class="col-md-6">
                        <form action="" method="GET" class="d-flex">
                            <input type="text" class="form-control me-2" name="buscar_venta" placeholder="Buscar por número de factura, cliente o vendedor..." value="<?php echo mostrarValor($buscar); ?>">
                            <button type="submit" class="btn btn-outline-primary me-2"><i class="fa-solid fa-magnifying-glass"></i></button>
                            <button type="button" class="btn btn-outline-secondary btn-reset-search" title="Limpiar búsqueda"><i class="fa-solid fa-arrows-rotate"></i></button>
                        </form>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="crear.php" class="btn btn-success fw-bold"><i class="fa-solid fa-cart-plus"></i> Ir al Punto de Venta</a>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Factura N°</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Vendedor</th>
                                    <th>Subtotal</th>
                                    <th>IVA</th>
                                    <th>Total</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($ventas)): ?>
                                    <?php foreach ($ventas as $venta): ?>
                                        <tr>
                                            <td class="fw-bold" style="color: var(--color-secundario);"><?php echo mostrarValor($venta['numero_factura']); ?></td>
                                            <td class="fw-bold text-dark"><?php echo mostrarValor($venta['fecha_venta']); ?></td>
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
                                            <td class="text-center">
                                                <a href="ver_detalle.php?id=<?php echo $venta['id_venta']; ?>" class="btn btn-sm btn-outline-info" title="Ver Detalle"><i class="fa-solid fa-eye"></i></a>
                                                <a href="descargar_pdf.php?id=<?php echo $venta['id_venta']; ?>" target="_blank" class="btn btn-sm btn-outline-danger" title="Descargar PDF"><i class="fa-solid fa-file-pdf"></i></a>
                                                <?php if ($venta['estado_venta'] === 'Realizada' || $venta['estado_venta'] === 'Pendiente'): ?>
                                                    <a href="editar.php?id=<?php echo $venta['id_venta']; ?>" class="btn btn-sm btn-outline-primary" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a>
                                                    <a href="anular.php?id=<?php echo $venta['id_venta']; ?>" class="btn btn-sm btn-outline-danger" title="Anular"><i class="fa-solid fa-ban"></i></a>
                                                <?php endif; ?>
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
    <script src="../../../public/js/main.js"></script>
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

        case 'creado':
            $mensajeToast = 'Venta registrada correctamente.';
            $tipoToast = 'success';
            break;

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