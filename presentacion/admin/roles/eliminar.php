<?php

require_once __DIR__ . '/../../../negocio/RolNegocio.php';

$rolNegocio = new RolNegocio();
$mensaje = '';

$id_rol = $_GET['id'] ?? null;
if (!$id_rol) {
    header('Location: listar.php');
    exit;
}

$rol = $rolNegocio->obtenerRolPorId($id_rol);
if (!$rol) {
    header('Location: listar.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $rolNegocio->eliminarRol($_POST['id_rol'] ?? null);

    if ($resultado['exito']) {
        header('Location: listar.php?mensaje=eliminado');
        exit;
    }

    $mensaje = $resultado['mensaje'];
}

function mostrarValor($valor)
{
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
            <div class="container-fluid p-4 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
                <div class="card shadow-sm border-0 border-top border-danger border-4 text-center p-5 w-100" style="max-width: 500px;">
                    <i class="fa-solid fa-triangle-exclamation text-danger mb-4" style="font-size: 4rem;"></i>
                    
                    <?php if ($mensaje): ?>
                        <div class="alert alert-danger"><?php echo mostrarValor($mensaje); ?></div>
                    <?php endif; ?>

                    
                    <h3 class="fw-bold mb-3">¿Eliminar Rol?</h3>
                    <p class="text-muted mb-4">Nombre del Rol: <b><?php echo mostrarValor($rol['nombre_rol']); ?></b></p>
                    <form action="eliminar.php?id=<?php echo mostrarValor($rol['id_rol']); ?>" method="POST">
                        <input type="hidden" name="id_rol" value="<?php echo mostrarValor($rol['id_rol']); ?>">
                        <a href="listar.php" class="btn btn-secondary px-4 fw-bold">Cancelar</a>
                        <button type="submit" class="btn btn-danger px-4 fw-bold">Confirmar Eliminación</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../public/js/main.js"></script>
</body>
</html>