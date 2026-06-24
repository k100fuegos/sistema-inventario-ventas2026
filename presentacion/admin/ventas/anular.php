<?php
require_once __DIR__ . '/../../../config/control_acceso.php';
requerirLogin();
requerirRol([ROL_ADMIN, ROL_SUPERVISOR]);

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_venta = $_POST['id_venta'] ?? null;
    if ($id_venta) {
        $resultado = $ventaNegocio->anularVenta($id_venta);
        
        if ($resultado['exito']) {
            header('Location: listar.php?mensaje=anulado');
            exit;
        } else {
            header('Location: listar.php?mensaje=error');
            exit;
        }
    }
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
    <title>Anular Venta - Tecnobyte</title>
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
                <li><a href="crear.php"><i class="fa-solid fa-cart-shopping"></i> Nueva Venta</a></li>
                <?php if(tieneRol([ROL_ADMIN, ROL_SUPERVISOR])): ?><li class="active"><a href="listar.php"><i class="fa-solid fa-file-invoice-dollar"></i> Historial Ventas</a></li><?php endif; ?>
                <?php if(tieneRol([ROL_ADMIN, ROL_SUPERVISOR])): ?><li><a href="../categorias/listar.php"><i class="fa-solid fa-tags"></i> Categorías</a></li><?php endif; ?>
                <?php if(tieneRol([ROL_ADMIN, ROL_SUPERVISOR])): ?><li><a href="../marcas/listar.php"><i class="fa-solid fa-award"></i> Marcas</a></li><?php endif; ?>
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
                <div class="card shadow-sm border-0 border-top border-danger border-4 text-center p-5 w-100" style="max-width: 550px;">
                    <i class="fa-solid fa-ban text-danger mb-4" style="font-size: 4rem;"></i>
                    <h3 class="fw-bold mb-3">¿Anular Venta Seleccionada?</h3>
                    <p class="text-muted mb-4">¿Está seguro de que desea anular la venta <strong><?php echo mostrarValor($venta['numero_factura']); ?></strong>? Esta acción no se puede deshacer y la venta dejará de ser válida en el sistema.</p>

                    <div class="bg-light p-3 rounded mb-4 text-start border">
                        <div class="row text-muted text-center">
                            <div class="col-6 mb-2">
                                <small class="fw-bold d-block text-dark">Código de Venta</small>
                                <?php echo mostrarValor($venta['numero_factura']); ?>
                            </div>
                            <div class="col-6 mb-2">
                                <small class="fw-bold d-block text-dark">Cliente</small>
                                <?php echo mostrarValor($venta['nombre_cliente']); ?>
                            </div>
                            <div class="col-6 mb-2">
                                <small class="fw-bold d-block text-dark">Vendedor</small>
                                <?php echo mostrarValor($venta['nombre_usuario']); ?>
                            </div>
                            <div class="col-6 mb-2">
                                <small class="fw-bold d-block text-dark">Total</small>
                                $<?php echo number_format((float) $venta['total_venta'], 2); ?>
                            </div>
                        </div>
                    </div>

                    <form action="" method="POST">
                        <input type="hidden" name="id_venta" value="<?php echo mostrarValor($venta['id_venta']); ?>">
                        <a href="listar.php" class="btn btn-secondary px-4 fw-bold">Cancelar</a>
                        <button type="submit" class="btn btn-danger px-4 fw-bold">Confirmar Anulación</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../public/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>