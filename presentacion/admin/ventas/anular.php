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
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>

        <div id="content">
            <?php include __DIR__ . '/../../includes/header.php'; ?>

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