<?php
require_once __DIR__ . '/../../../config/control_acceso.php';
requerirLogin();
requerirRol([ROL_ADMIN, ROL_SUPERVISOR]);


require_once __DIR__ . '/../../../negocio/MarcaNegocio.php';

$marcaNegocio = new MarcaNegocio();
$mensaje = '';

$idMarca = $_GET['id'] ?? null;
if (!$idMarca) {
    header('Location: listar.php');
    exit;
}

$marca = $marcaNegocio->obtenerMarcaPorId($idMarca);
if (!$marca) {
    header('Location: listar.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $marcaNegocio->eliminarMarca($_POST['id_marca'] ?? null);

    if ($resultado['exito']) {
        header('Location: listar.php?mensaje=eliminado');
        exit;
    }

    $mensaje = $resultado['mensaje'];
}

function mostrarValor($valor) {
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Categoría - Tecnobyte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="d-flex">
        <!-- SIDEBAR -->
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
                <?php if(tieneRol([ROL_ADMIN, ROL_SUPERVISOR, ROL_VENDEDOR])): ?><li><a href="../ventas/listar.php"><i class="fa-solid fa-file-invoice-dollar"></i> Historial Ventas</a></li><?php endif; ?>
                <?php if(tieneRol([ROL_ADMIN, ROL_SUPERVISOR])): ?><li><a href="../categorias/listar.php"><i class="fa-solid fa-tags"></i> Categorías</a></li><?php endif; ?>
                <?php if(tieneRol([ROL_ADMIN, ROL_SUPERVISOR])): ?><li class="active"><a href="listar.php"><i class="fa-solid fa-award"></i> Marcas</a></li><?php endif; ?>
                <li><a href="../productos/listar.php"><i class="fa-solid fa-cubes"></i> Productos</a></li>
                <li><a href="../clientes/listar.php"><i class="fa-solid fa-users"></i> Clientes</a></li>
                <?php if(tieneRol([ROL_ADMIN])): ?><li><a href="../usuarios/listar.php"><i class="fa-solid fa-user-shield"></i> Usuarios</a></li><?php endif; ?>
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

            <div class="container-fluid p-4 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
                <div class="card shadow-sm border-0 border-top border-danger border-4 text-center p-5 w-100" style="max-width: 500px;">
                    <i class="fa-solid fa-triangle-exclamation text-danger mb-4" style="font-size: 4rem;"></i>
                    <h3 class="fw-bold mb-3">¿Eliminar Marca?</h3>
                    <?php if ($mensaje): ?>
                    <div class="alert alert-danger"><?php echo mostrarValor($mensaje); ?></div>
                <?php endif; ?>
                    <p class="text-muted mb-4">Nombre de la categoria: <b><?php echo mostrarValor($marca['nombre_marca']); ?></b></p>
                    <form action="eliminar.php?id=<?php echo mostrarValor($marca['id_marca'])?>" method="POST">
                        <input type="hidden" name="id_marca" value="<?php echo mostrarValor($marca['id_marca']); ?>">
                        <a href="listar.php" class="btn btn-secondary px-4 fw-bold">Cancelar</a>
                        <button type="submit" class="btn btn-danger px-4 fw-bold">Confirmar Eliminación</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../public/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>