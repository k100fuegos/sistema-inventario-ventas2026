<?php
require_once __DIR__ . '/../../../config/control_acceso.php';
requerirLogin();
requerirRol([ROL_ADMIN, ROL_SUPERVISOR, ROL_VENDEDOR]);


require_once __DIR__ . '/../../../negocio/ClienteNegocio.php';

$clienteNegocio = new ClienteNegocio();
$mensaje = '';

$id_cliente = $_GET['id'] ?? null;
if (!$id_cliente) {
    header('Location: listar.php');
    exit;
}

$cliente = $clienteNegocio->obtenerClientePorId($id_cliente);
if (!$cliente) {
    header('Location: listar.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = $clienteNegocio->eliminarCliente($_POST['id_cliente'] ?? null);

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

// Formatear el tipo de cliente para la vista
$tipo_texto = '';
if (isset($cliente['tipo_cliente'])) {
    $tipo_texto = ($cliente['tipo_cliente'] === 'PN') ? 'Persona Natural (PN)' : (($cliente['tipo_cliente'] === 'PJ') ? 'Persona Jurídica (PJ)' : $cliente['tipo_cliente']);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Cliente - Technobyte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="d-flex">
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>

        <div id="content">
            <?php include __DIR__ . '/../../includes/header.php'; ?>

            <div class="container-fluid p-4 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
                <div class="card shadow-sm border-0 border-top border-danger border-4 text-center p-5 w-100" style="max-width: 500px;">
                    <i class="fa-solid fa-triangle-exclamation text-danger mb-4" style="font-size: 4rem;"></i>
                    
                    <?php if ($mensaje): ?>
                        <div class="alert alert-danger shadow-sm"><?php echo mostrarValor($mensaje); ?></div>
                    <?php endif; ?>

                    <h3 class="fw-bold mb-3">¿Eliminar Cliente?</h3>
                    
                    <p class="text-muted mb-4 fs-5">
                        Nombre: <b class="text-dark"><?php echo mostrarValor($cliente['nombre_cliente']); ?></b><br>
                        Tipo de Cliente: <b class="text-dark"><?php echo mostrarValor($tipo_texto); ?></b>
                    </p>
                    
                    <form action="eliminar.php?id=<?php echo mostrarValor($cliente['id_cliente']); ?>" method="POST">
                        <input type="hidden" name="id_cliente" value="<?php echo mostrarValor($cliente['id_cliente']); ?>">
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