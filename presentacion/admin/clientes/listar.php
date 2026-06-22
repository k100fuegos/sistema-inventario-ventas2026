<?php
require_once __DIR__ . '/../../../config/control_acceso.php';
requerirRol([ROL_ADMIN, ROL_SUPERVISOR, ROL_VENDEDOR]);

require_once __DIR__  . '/../../../negocio/ClienteNegocio.php';

$buscar = trim($_GET['buscar_cliente'] ?? '');
$clienteNegocio = new ClienteNegocio();
$clientes = $clienteNegocio->listarClientes($buscar);

$mensaje = $_GET['mensaje'] ?? null;

function mostrarValor($valor)
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Clientes - Tecnobyte</title>
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
                <li><a href="../ventas/crear.php"><i class="fa-solid fa-cart-shopping"></i> Nueva Venta</a></li>
                <li><a href="../ventas/listar.php"><i class="fa-solid fa-file-invoice-dollar"></i> Historial Ventas</a></li>
                <li><a href="../categorias/listar.php"><i class="fa-solid fa-tags"></i> Categorías</a></li>
                <li><a href="../productos/listar.php"><i class="fa-solid fa-cubes"></i> Productos</a></li>
                <li class="active"><a href="listar.php"><i class="fa-solid fa-users"></i> Clientes</a></li>
                <li><a href="../usuarios/listar.php"><i class="fa-solid fa-user-shield"></i> Usuarios</a></li>
            </ul>
        </nav>

        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-dorado"><i class="fa-solid fa-bars"></i></button>
                    <div class="ms-auto d-flex align-items-center">
                        <span class="me-3 fw-bold"><i class="fa-solid fa-circle-user"></i> Administrador</span>
                        <a href="../../../logout.php" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
                    </div>
                </div>
            </nav>

            <div class="container-fluid p-4">
                <h2 class="mb-4 text-dark fw-bold"><i class="fa-solid fa-users text-secondary"></i> Gestión de Clientes</h2>
                <div class="row mb-4 align-items-center">
                    <div class="col-md-6">
                        <form action="" method="GET" class="d-flex">
                            <input type="text" class="form-control me-2" name="buscar_cliente" placeholder="Buscar por nombre, DUI, NIT o NRC..." value="<?php echo mostrarValor($buscar); ?>">
                            <button type="submit" class="btn btn-outline-primary me-2"><i class="fa-solid fa-magnifying-glass"></i></button>
                            <button type="button" class="btn btn-outline-secondary btn-reset-search" title="Limpiar búsqueda"><i class="fa-solid fa-arrows-rotate"></i></button>
                        </form>
                    </div>

                    <div class="col-md-6 text-md-end">
                        <a href="crear.php" class="btn btn-primary fw-bold"><i class="fa-solid fa-plus"></i> Nuevo Cliente</a>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 align-middle">
                            <thead class="table-dark">
                                <tr class="text-center">
                                    <th>Cliente</th>
                                    <th>Tipo</th>
                                    <th>DUI</th>
                                    <th>NIT</th>
                                    <th>NRC</th>
                                    <th>Telefono</th>
                                    <th>Correo</th>
                                    <th>Dirección</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($clientes)):
                                    foreach ($clientes as $cliente): ?>
                                        <tr class="text-center">
                                            <td><?php echo mostrarValor($cliente['nombre_cliente']); ?></td>
                                            <td><?php echo mostrarValor($cliente['tipo_cliente']); ?></td>
                                            <td><?php echo mostrarValor($cliente['dui_cliente']); ?></td>
                                            <td><?php echo mostrarValor($cliente['nit_cliente']); ?></td>
                                            <td><?php echo mostrarValor($cliente['nrc_cliente']); ?></td>
                                            <td><?php echo mostrarValor($cliente['telefono_cliente']); ?></td>
                                            <td><?php echo mostrarValor($cliente['correo_cliente']); ?></td>
                                            <td><?php echo mostrarValor($cliente['direccion_cliente']); ?></td>
                                            <td>
                                                <?php if ($cliente['estado_cliente'] == 1): ?>
                                                    <span class="badge bg-success">Activo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactivo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="editar.php?id=<?php echo mostrarValor($cliente['id_cliente']); ?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></a>
                                                <a href="eliminar.php?id=<?php echo mostrarValor($cliente['id_cliente']); ?>" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach;
                                else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            No se encontraron registros
                                        </td>
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

    <?php

    $mensajeToast = '';
    $tipoToast = '';

    switch ($mensaje) {

        case 'creado':
            $mensajeToast = 'Cliente registrada correctamente.';
            $tipoToast = 'success';
            break;

        case 'actualizado':
            $mensajeToast = 'Cliente actualizada correctamente.';
            $tipoToast = 'success';
            break;

        case 'eliminado':
            $mensajeToast = 'Cliente eliminada correctamente.';
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