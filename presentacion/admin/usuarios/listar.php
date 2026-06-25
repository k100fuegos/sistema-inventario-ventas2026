<?php
require_once __DIR__ . '/../../../config/control_acceso.php';
requerirLogin();
requerirRol([ROL_ADMIN]);


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
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>

        <div id="content">
            <?php include __DIR__ . '/../../includes/header.php'; ?>

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