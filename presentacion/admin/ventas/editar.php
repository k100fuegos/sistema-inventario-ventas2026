<?php
require_once __DIR__ . '/../../../config/control_acceso.php';
requerirLogin();
requerirRol([ROL_ADMIN, ROL_SUPERVISOR]);

require_once '../../../negocio/VentaNegocio.php';
require_once '../../../negocio/ClienteNegocio.php';
require_once '../../../negocio/UsuarioNegocio.php';

$id_venta = $_GET['id'] ?? null;

if (!$id_venta) {
    header('Location: listar.php');
    exit;
}

$ventaNegocio = new VentaNegocio();
$clienteNegocio = new ClienteNegocio();
$usuarioNegocio = new UsuarioNegocio();

$venta = $ventaNegocio->obtenerVentaPorId($id_venta);

if (!$venta) {
    header('Location: listar.php');
    exit;
}

$detalles = $ventaNegocio->obtenerDetalleVenta($id_venta);
$clientes = $clienteNegocio->listarClientes();
$usuarios = $usuarioNegocio->listarUsuarios();
$mensajeError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datosFormulario = [
        'id_venta' => $_POST['id_venta'] ?? null,
        'fecha_venta' => $_POST['fecha_venta'] ?? '',
        'id_cliente' => $_POST['id_cliente'] ?? null,
        'id_usuario' => $_POST['id_usuario'] ?? null,
        'estado' => $_POST['estado_venta'] ?? 'Pendiente'
    ];

    $resultado = $ventaNegocio->actualizarVenta($datosFormulario);

    if ($resultado['exito']) {
        header("Location: listar.php?mensaje=actualizado");
        exit;
    } else {
        $mensajeError = is_array($resultado['errores']) ? implode(", ", $resultado['errores']) : $resultado['mensaje'];
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
    <title>Editar Venta - Tecnobyte</title>
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
                <?php if(tieneRol([ROL_ADMIN, ROL_SUPERVISOR, ROL_VENDEDOR])): ?><li class="active"><a href="listar.php"><i class="fa-solid fa-file-invoice-dollar"></i> Historial Ventas</a></li><?php endif; ?>
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

             <div class="container-fluid p-4">
                 <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-dark fw-bold"><i class="fa-solid fa-pen-to-square" style="color: var(--color-primario);"></i> Editar Venta</h2>
                    <a href="listar.php" class="btn btn-secondary fw-bold"><i class="fa-solid fa-arrow-left"></i> Volver al Historial</a>
                 </div>

                 <?php if (!empty($mensajeError)): ?>
                     <div class="alert alert-danger shadow-sm fw-bold">
                         <i class="fa-solid fa-triangle-exclamation"></i> <?php echo mostrarValor($mensajeError); ?>
                     </div>
                 <?php endif; ?>

                 <div class="row">
                     <!-- Left Column: Formulario de edición -->
                     <div class="col-md-5 mb-4">
                         <div class="card shadow-sm border-0">
                             <div class="card-header text-white fw-bold" style="background-color: var(--color-primario);">
                                 <i class="fa-solid fa-gear"></i> Modificar Datos Generales
                             </div>
                             <div class="card-body">
                                 <form action="" method="POST">
                                     <input type="hidden" name="id_venta" value="<?php echo mostrarValor($venta['id_venta']); ?>">
                                     
                                     <div class="mb-3">
                                         <label for="fecha_venta" class="form-label fw-bold">Fecha de Venta</label>
                                         <input type="datetime-local" class="form-control" id="fecha_venta" name="fecha_venta" 
                                             value="<?php echo date('Y-m-d\TH:i', strtotime($venta['fecha_venta'])); ?>" required>
                                     </div>

                                     <div class="mb-3">
                                         <label for="id_cliente" class="form-label fw-bold">Cliente</label>
                                         <select class="form-select" id="id_cliente" name="id_cliente" required>
                                             <option value="">Seleccione un cliente...</option>
                                             <?php foreach ($clientes as $cliente): ?>
                                                 <?php if ($cliente['estado_cliente'] == 1 || $cliente['id_cliente'] == $venta['id_cliente']): ?>
                                                     <option value="<?php echo $cliente['id_cliente']; ?>" 
                                                         <?php echo ($cliente['id_cliente'] == $venta['id_cliente']) ? 'selected' : ''; ?>>
                                                         <?php echo mostrarValor($cliente['nombre_cliente']); ?>
                                                     </option>
                                                 <?php endif; ?>
                                             <?php endforeach; ?>
                                         </select>
                                     </div>

                                     <div class="mb-3">
                                         <label for="id_usuario" class="form-label fw-bold">Vendedor</label>
                                         <select class="form-select" id="id_usuario" name="id_usuario" required>
                                             <option value="">Seleccione un vendedor...</option>
                                             <?php foreach ($usuarios as $usuario): ?>
                                                 <?php if ($usuario['estado_usuario'] == 1 || $usuario['id_usuario'] == $venta['id_usuario']): ?>
                                                     <option value="<?php echo $usuario['id_usuario']; ?>" 
                                                         <?php echo ($usuario['id_usuario'] == $venta['id_usuario']) ? 'selected' : ''; ?>>
                                                         <?php echo mostrarValor($usuario['nombre_usuario']); ?>
                                                     </option>
                                                 <?php endif; ?>
                                             <?php endforeach; ?>
                                         </select>
                                     </div>

                                     <div class="mb-4">
                                         <label for="estado_venta" class="form-label fw-bold">Estado de Venta</label>
                                         <select class="form-select" id="estado_venta" name="estado_venta" required>
                                             <option value="Pendiente" <?php echo ($venta['estado_venta'] === 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                             <option value="Realizada" <?php echo ($venta['estado_venta'] === 'Realizada') ? 'selected' : ''; ?>>Realizada</option>
                                             <option value="Anulada" <?php echo ($venta['estado_venta'] === 'Anulada') ? 'selected' : ''; ?>>Anulada</option>
                                         </select>
                                     </div>

                                     <div class="d-grid">
                                         <button type="submit" class="btn fw-bold text-white" style="background-color: var(--color-secundario);"><i class="fa-solid fa-save"></i> Guardar Cambios</button>
                                     </div>
                                 </form>
                             </div>
                         </div>
                     </div>

                     <!-- Right Column: Detalles Financieros y Productos (Read Only) -->
                     <div class="col-md-7">
                         <div class="card shadow-sm border-0 mb-4">
                             <div class="card-header text-white fw-bold" style="background-color: var(--color-primario);">
                                 <i class="fa-solid fa-file-invoice-dollar"></i> Información Financiera (Solo Lectura)
                             </div>
                             <div class="card-body bg-light">
                                 <h5 class="fw-bold text-secondary mb-3">Código de Venta: <span class="text-dark"><?php echo mostrarValor($venta['numero_factura']); ?></span></h5>
                                 <hr>
                                 <div class="row text-end align-items-center">
                                     <div class="col-8 text-muted fw-bold">Subtotal:</div>
                                     <div class="col-4 fw-bold">$ <?php echo number_format((float) $venta['subtotal_venta'], 2); ?></div>
                                     
                                     <div class="col-8 text-muted fw-bold">IVA:</div>
                                     <div class="col-4 fw-bold text-danger">+ $ <?php echo number_format((float) $venta['iva_venta'], 2); ?></div>
                                     
                                     <div class="col-8 text-dark fw-bold fs-5 mt-2">Total a Pagar:</div>
                                     <div class="col-4 text-success fw-bold fs-4 mt-2">$ <?php echo number_format((float) $venta['total_venta'], 2); ?></div>
                                 </div>
                             </div>
                         </div>

                         <div class="card shadow-sm border-0">
                             <div class="card-header text-white fw-bold" style="background-color: var(--color-secundario);">
                                 <i class="fa-solid fa-boxes-stacked"></i> Productos Vendidos (Solo Lectura)
                             </div>
                             <div class="table-responsive">
                                 <table class="table table-striped table-hover mb-0 text-center align-middle">
                                     <thead class="table-light">
                                         <tr>
                                             <th>Producto</th>
                                             <th>Cant.</th>
                                             <th>Precio U.</th>
                                             <th>Subtotal</th>
                                         </tr>
                                     </thead>
                                     <tbody>
                                         <?php if (!empty($detalles)): ?>
                                             <?php foreach ($detalles as $detalle): ?>
                                                 <tr>
                                                     <td class="text-start"><?php echo mostrarValor($detalle['nombre_producto']); ?></td>
                                                     <td><?php echo mostrarValor($detalle['cantidad_producto']); ?></td>
                                                     <td>$ <?php echo number_format((float) $detalle['precio_unitario'], 2); ?></td>
                                                     <td class="fw-bold">$ <?php echo number_format((float) $detalle['subtotal_detalle'], 2); ?></td>
                                                 </tr>
                                             <?php endforeach; ?>
                                         <?php else: ?>
                                             <tr>
                                                 <td colspan="4" class="py-4">No se encontraron productos en esta venta.</td>
                                             </tr>
                                         <?php endif; ?>
                                     </tbody>
                                 </table>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../public/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
