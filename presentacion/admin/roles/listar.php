<?php

require_once __DIR__ . '/../../../negocio/RolNegocio.php';

$rolNegocio = new RolNegocio();
$roles = $rolNegocio->listarRoles();
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
    <title>Categorías - Tecnobyte</title>
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
                <li class="active"><a href="listar.php"><i class="fa-solid fa-tags"></i> Categorías</a></li>
                <li><a href="../marcas/listar.php"><i class="fa-solid fa-award"></i> Marcas</a></li>
                <li><a href="../productos/listar.php"><i class="fa-solid fa-cubes"></i> Productos</a></li>
                <li><a href="../clientes/listar.php"><i class="fa-solid fa-users"></i> Clientes</a></li>
                <li><a href="../usuarios/listar.php"><i class="fa-solid fa-user-shield"></i> Usuarios</a></li>
            </ul>
        </nav>

        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-dorado"><i class="fa-solid fa-bars"></i></button>
                    <div class="ms-auto d-flex align-items-center">
                        <span class="me-3 fw-bold"><i class="fa-solid fa-circle-user"></i> LÓGICA PHP</span>
                        <a href="../../../logout.php" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
                    </div>
                </div>
            </nav>

            <div class="container-fluid p-4">
                <h2 class="mb-4 text-dark fw-bold"><i class="fa-solid fa-tags text-secondary"></i> Gestión de Roles</h2>
                <div class="row mb-4 align-items-center">
                    <div class="col-md-6">
                        <form action="" method="GET" class="d-flex">
                            <input type="text" class="form-control me-2" name="buscar_categoria" placeholder="Buscar categoría...">
                            <button type="submit" class="btn btn-outline-primary me-2"><i class="fa-solid fa-magnifying-glass"></i></button>
                            <button type="button" class="btn btn-outline-secondary btn-reset-search" title="Limpiar búsqueda"><i class="fa-solid fa-arrows-rotate"></i></button>
                        </form>
                    </div>

                    
<div class="col-md-6 text-md-end">
                        <a href="crear.php" class="btn btn-primary fw-bold"><i class="fa-solid fa-plus"></i> Nuevo Rol</a>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 align-middle text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>Rol</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($roles)): ?>
                                    <?php foreach ($roles as $rol): ?>
                                        <tr>
                                            <td><?php echo mostrarValor($rol['nombre_rol']); ?></td>
                                            <td>
                                                <a href="editar.php?id=<?php echo mostrarValor($rol['id_rol']); ?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></a>
                                                <a href="eliminar.php?id=<?php echo mostrarValor($rol['id_rol']); ?>" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                         <td colspan="2" class="text-center">No se encontraron registros</td>
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

    <?php

    $mensajeToast = '';
    $tipoToast = '';

    switch ($mensaje) {

        case 'creado':
            $mensajeToast = 'Rol registrado correctamente.';
            $tipoToast = 'success';
            break;

        case 'actualizado':
            $mensajeToast = 'Rol actualizado correctamente.';
            $tipoToast = 'success';
            break;

        case 'eliminado':
            $mensajeToast = 'Rol eliminado correctamente.';
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