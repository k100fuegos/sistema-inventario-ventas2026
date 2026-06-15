<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Principal - Tecnobyte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <link rel="stylesheet" href="../public/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="d-flex">
        
        <nav id="sidebar">
            <div class="sidebar-header d-flex align-items-center justify-content-center py-3">
                <img src="../public/img/logo-nav.svg" alt="Logo" class="img-fluid me-2" style="max-width: 40px;">
                <h4 class="fw-bold mb-0">Technobyte</h4>
            </div>
            
            <ul class="list-unstyled components">
                <li class="active"><a href="dashboard.php"><i class="fa-solid fa-house"></i> Panel Principal</a></li>
                <li><a href="admin/ventas/crear.php"><i class="fa-solid fa-cart-shopping"></i> Nueva Venta</a></li>
                <li><a href="admin/ventas/listar.php"><i class="fa-solid fa-file-invoice-dollar"></i> Historial Ventas</a></li>
                <li><a href="admin/categorias/listar.php"><i class="fa-solid fa-tags"></i> Categorías</a></li>
                <li><a href="admin/marcas/listar.php"><i class="fa-solid fa-tags"></i> Marcas</a></li>
                <li><a href="admin/productos/listar.php"><i class="fa-solid fa-cubes"></i> Productos</a></li>
                <li><a href="admin/clientes/listar.php"><i class="fa-solid fa-users"></i> Clientes</a></li>
                <li><a href="admin/usuarios/listar.php"><i class="fa-solid fa-user-shield"></i> Usuarios</a></li>
            </ul>
        </nav>

        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-dorado"><i class="fa-solid fa-bars"></i></button>
                    <div class="ms-auto d-flex align-items-center">
                        <span class="me-3 fw-bold"><i class="fa-solid fa-circle-user"></i> </span>
                        <a href="../logout.php" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
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
                                    <h3 class="fw-bold">LÓGICA PHP: $</h3>
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
                                    <h3 class="fw-bold">LÓGICA PHP: Conteo</h3>
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
                                    <h3 class="fw-bold text-danger">LÓGICA PHP: Conteo</h3>
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
                                    <h3 class="fw-bold">LÓGICA PHP: Conteo</h3>
                                </div>
                                <i class="fa-solid fa-users icon-resumen"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white fw-bold py-3">
                                <i class="fa-solid fa-clock-rotate-left"></i> Últimas Ventas
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>N° Factura</th>
                                                <th>Cliente</th>
                                                <th>Total</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">Aún no hay datos. Esperando backend...</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
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