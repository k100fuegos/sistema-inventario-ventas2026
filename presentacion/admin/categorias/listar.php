<?php
require_once __DIR__ . '/../../../config/control_acceso.php';
requerirLogin();
requerirRol([ROL_ADMIN, ROL_SUPERVISOR]);


require_once __DIR__ . '/../../../negocio/CategoriaNegocio.php';

$buscar = trim($_GET['buscar_categoria'] ?? '');
$categoriaNegocio = new CategoriaNegocio();
$categorias = $categoriaNegocio->listarCategorias($buscar);
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
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>

        <div id="content">
            <?php include __DIR__ . '/../../includes/header.php'; ?>

            <div class="container-fluid p-4">
                <h2 class="mb-4 text-dark fw-bold"><i class="fa-solid fa-tags text-secondary"></i> Gestión de Categorías</h2>
                <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
                    <form action="" method="GET" class="flex-grow-1" style="max-width: 600px;">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white text-muted border-end-0"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" name="buscar_categoria" placeholder="Buscar por nombre o estado (Activo/Inactivo)..." value="<?php echo mostrarValor($buscar); ?>">
                            <button type="button" class="btn btn-outline-secondary btn-reset-search" title="Limpiar búsqueda"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    </form>

                    <!--  debería de ir el bloque de mensajes -->

                    <div class="d-grid d-md-block">
                        <a href="crear.php" class="btn btn-primary fw-bold">
                            <i class="fa-solid fa-plus"></i> Nueva Categoría
                        </a>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="table-responsive rounded-2">
                        <table class="table table-hover table-striped mb-0 align-middle text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nombre de Categoría</th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($categorias)): ?>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <tr>
                                            <td><?php echo mostrarValor($categoria['nombre_categoria']); ?></td>
                                            <td><?php echo mostrarValor($categoria['descripcion_categoria']); ?></td>
                                            <td>
                                                <?php if ($categoria['estado_categoria'] == 1): ?>
                                                    <span class="badge bg-success">Activo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactivo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="editar.php?id=<?php echo mostrarValor($categoria['id_categoria']); ?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></a>
                                                <a href="eliminar.php?id=<?php echo mostrarValor($categoria['id_categoria']); ?>" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No se encontraron registros</td>
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

    <?php

    $mensajeToast = '';
    $tipoToast = '';

    switch ($mensaje) {

        case 'creado':
            $mensajeToast = 'Categoría registrada correctamente.';
            $tipoToast = 'success';
            break;

        case 'actualizado':
            $mensajeToast = 'Categoría actualizada correctamente.';
            $tipoToast = 'success';
            break;

        case 'eliminado':
            $mensajeToast = 'Categoría eliminada correctamente.';
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
