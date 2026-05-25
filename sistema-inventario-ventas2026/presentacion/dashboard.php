<?php
// Iniciar sesión para obtener los datos del usuario logueado
session_start();

// Redirigir al login si no hay sesión activa (seguridad)
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../index.php");
    exit();
}

// Simulación de los datos que vendrán de la base de datos a futuro.
$ventasHoy = 0.00;
$totalTransacciones = 0;
$productosBajoStock = 0;
$nuevosClientes = 0;
$ultimasVentas = []; // Aquí se guardará el array de la base de datos

// Nombre y rol del usuario activo
$nombreUsuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$rolUsuario = isset($_SESSION['nombre_rol']) ? $_SESSION['nombre_rol'] : 'Sin Asignar';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal - Tecnobyte</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>

    <div class="d-flex">
        
        <nav id="sidebar">
            <div class="sidebar-header d-flex align-items-center justify-content-center py-3">
                <img src="../public/img/logo-tecnobyte.png" alt="Logo Tecnobyte" class="img-fluid me-2" style="max-width: 40px; filter: drop-shadow(0px 2px 4px rgba(0,0,0,0.3));">
                <h4 class="fw-bold mb-0">Tecnobyte</h4>
            </div>

            <ul class="list-unstyled components">
                <li><a href="#"><i class="fa-solid fa-house"></i> Panel Principal</a></li>
                <li><a href="#"><i class="fa-solid fa-cart-shopping"></i> Nueva Venta</a></li>
                <li><a href="#"><i class="fa-solid fa-cubes"></i> Inventario</a></li>
                <li><a href="#"><i class="fa-solid fa-users"></i> Clientes</a></li>
                
                <?php if($_SESSION['id_rol'] == 1): ?>
                    <li><a href="#"><i class="fa-solid fa-user-shield"></i> Usuarios</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-dorado">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    
                    <div class="ms-auto d-flex align-items-center">
                        <span class="me-3 fw-bold">
                            <i class="fa-solid fa-circle-user"></i> 
                            <?= htmlspecialchars($nombreUsuario) ?> (<?= htmlspecialchars($rolUsuario) ?>)
                        </span>
                        <a href="../logout.php" class="btn btn-outline-danger btn-sm">
                            <i class="fa-solid fa-right-from-bracket"></i> Salir
                        </a>
                    </div>
                </div>
            </nav>

            <div class="container-fluid p-4">
                <h2 class="mb-4 text-dark fw-bold">Resumen de Hoy</h2>

                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card card-resumen h-100 p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted">Ventas del Día</h6>
                                    <h3 class="fw-bold">$ <?= number_format($ventasHoy, 2) ?></h3>
                                </div>
                                <i class="fa-solid fa-cash-register icon-resumen"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card card-resumen h-100 p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted">Transacciones</h6>
                                    <h3 class="fw-bold"><?= $totalTransacciones ?></h3>
                                </div>
                                <i class="fa-solid fa-file-invoice-dollar icon-resumen"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card card-resumen h-100 p-3 border-danger">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted">Stock Bajo</h6>
                                    <h3 class="fw-bold text-danger"><?= $productosBajoStock ?></h3>
                                </div>
                                <i class="fa-solid fa-triangle-exclamation icon-resumen text-danger"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card card-resumen h-100 p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted">Nuevos Clientes</h6>
                                    <h3 class="fw-bold"><?= $nuevosClientes ?></h3>
                                </div>
                                <i class="fa-solid fa-users icon-resumen"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white fw-bold">
                                <i class="fa-solid fa-clock-rotate-left"></i> Últimas Ventas
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>N° Factura</th>
                                                <th>Cliente</th>
                                                <th>Total</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(empty($ultimasVentas)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">No hay ventas registradas hoy.</td>
                                            </tr>
                                            <?php else: ?>
                                                <?php foreach($ultimasVentas as $venta): ?>
                                                <tr>
                                                    <td><?= $venta['numero_factura'] ?></td>
                                                    <td><?= $venta['cliente'] ?></td>
                                                    <td><?= $venta['total'] ?></td>
                                                    <td><?= $venta['estado'] ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                         <div class="card shadow-sm">
                            <div class="card-header bg-white fw-bold">
                                Acciones Rápidas
                            </div>
                            <div class="card-body">
                                <a href="#" class="btn btn-dorado w-100 mb-2"><i class="fa-solid fa-plus"></i> Registrar Venta</a>
                                <a href="#" class="btn btn-outline-dark w-100"><i class="fa-solid fa-box"></i> Ir al Inventario</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../public/js/main.js"></script>
</body>
</html>