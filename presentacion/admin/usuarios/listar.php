<?php
require_once __DIR__ . '/../../../config/control_acceso.php';
requerirLogin();


require_once __DIR__  . '/../../../negocio/UsuarioNegocio.php';

$buscar = trim($_GET['buscar_usuario'] ?? '');
$usuarioNegocio = new usuarioNegocio();
$usuarios = $usuarioNegocio->listarUsuarios($buscar);

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
    <title>Usuarios - Tecnobyte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/style.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class="d-flex">
        <nav id="sidebar">
        <button type="button" id="sidebarClose" class="btn btn-link text-white d-block d-md-none position-absolute top-0 end-0 mt-3 me-2" style="z-index: 1060; text-decoration: none;">
            <i class="fa-solid fa-xmark fs-3"></i>
        </button>
            <div class="sidebar-header d-flex align-items-center justify-content-center py-3">
                <img src="../../../public/img/logo-nav.svg" alt="Logo" class="img-fluid me-2" style="max-width: 40px;">
                <h4 class="fw-bold mb-0">Technobyte</h4>
            </div>
            <ul class="list-unstyled components">
                <li><a href="../../dashboard.php"><i class="fa-solid fa-house"></i> Panel Principal</a></li>
                <li><a href="../ventas/crear.php"><i class="fa-solid fa-cart-shopping"></i> Nueva Venta</a></li>
                <li><a href="../ventas/listar.php"><i class="fa-solid fa-file-invoice-dollar"></i> Historial Ventas</a></li>
                <li><a href="../categorias/listar.php"><i class="fa-solid fa-tags"></i> Categorías</a></li>
                <li><a href="../marcas/listar.php"><i class="fa-solid fa-award"></i> Marcas</a></li>
                <li><a href="../productos/listar.php"><i class="fa-solid fa-cubes"></i> Productos</a></li>
                <li><a href="../clientes/listar.php"><i class="fa-solid fa-users"></i> Clientes</a></li>
                <li class="active"><a href="listar.php"><i class="fa-solid fa-user-shield"></i> Usuarios</a></li>
            </ul>
        </nav>

        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light sticky-top bg-white shadow-sm" style="z-index: 1020;">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-dorado"><i class="fa-solid fa-bars"></i></button>
                    <div class="ms-auto d-flex align-items-center">
                        <span class="me-3 fw-bold">
                            <i class="fa-solid fa-circle-user"></i> 
                            <?php 
                            $nombreUsr = $_SESSION['nombre'] ?? 'Administrador';
                            $rolUsr = $_SESSION['nombre_rol'] ?? 'Sin rol';
                            echo htmlspecialchars($nombreUsr) . ' (' . htmlspecialchars($rolUsr) . ')';
                            ?>
                        </span>
                        <a href="../../../logout.php" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
                    </div>
                </div>
            </nav>

            <div class="container-fluid p-4">
                <h2 class="mb-4 text-dark fw-bold"><i class="fa-solid fa-user-shield text-secondary"></i> Gestión de Usuarios</h2>
                <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
                    <form action="" method="GET" class="flex-grow-1" style="max-width: 600px;">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white text-muted border-end-0"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" name="buscar_usuario" placeholder="Buscar por nombre, rol o estado (Activo/Inactivo)..." value="<?php echo mostrarValor($buscar); ?>">
                            <button type="button" class="btn btn-outline-secondary btn-reset-search" title="Limpiar búsqueda"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    </form>
                    <div class="d-grid d-md-block">
                        <a href="crear.php" class="btn btn-primary fw-bold">
                            <i class="fa-solid fa-plus"></i> Nuevo Usuario
                        </a>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="table-responsive rounded-2">
                        <table class="table table-hover table-striped mb-0 align-middle text-center">
                            <thead class="table-dark">
                                <tr class="text-center">
                                    <th>Nombre</th>
                                    <th>Rol</th>
                                    <th>Correo</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($usuarios)):
                                    foreach ($usuarios as $usuario): ?>
                                        <tr class="text-center">
                                            <td><?php echo mostrarValor($usuario['nombre_usuario']); ?></td>
                                            <td><?php echo mostrarValor($usuario['nombre_rol']); ?></td>
                                            <td><?php echo mostrarValor($usuario['correo_usuario']); ?></td>
                                            <td>
                                                <?php if ($usuario['estado_usuario'] == 1): ?>
                                                    <span class="badge bg-success">Activo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactivo</span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <a href="editar.php?id=<?php echo mostrarValor($usuario['id_usuario']); ?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></a>
                                                <a href="eliminar.php?id=<?php echo mostrarValor($usuario['id_usuario']); ?>" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach;
                                else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">
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
    <script src="../../../public/js/main.js?v=<?php echo time(); ?>"></script>
    <script src="../../../public/js/notificacion.js"></script>

    <?php

    $mensajeToast = '';
    $tipoToast = '';

    switch ($mensaje) {

        case 'creado':
            $mensajeToast = 'Usuario registrado correctamente.';
            $tipoToast = 'success';
            break;

        case 'actualizado':
            $mensajeToast = 'Usuario actualizado correctamente.';
            $tipoToast = 'success';
            break;

        case 'eliminado':
            $mensajeToast = 'Usuario eliminado correctamente.';
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