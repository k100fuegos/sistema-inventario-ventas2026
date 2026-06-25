<?php
require_once '../config/control_acceso.php';
requerirLogin();

require_once '../negocio/DashboardNegocio.php';

try {
    $dashboardNegocio = new DashboardNegocio();
    $resumen = $dashboardNegocio->obtenerResumen();

    $ventasDia = $resumen['ventasDia'];
    $transaccionesDia = $resumen['transaccionesDia'];
    $stockBajo = $resumen['stockBajo'];
    $listaStockBajo = $resumen['listaStockBajo'];
    $nuevosClientes = $resumen['nuevosClientes'];
    $ultimasVentas = $resumen['ultimasVentas'];
    $topProductos = $resumen['topProductos'];
    $ventasPorCategoria = $resumen['ventasPorCategoria'];

} catch (Exception $e) {
    die("Error al cargar el dashboard: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Principal - Tecnobyte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <link rel="stylesheet" href="../public/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="d-flex">
        
        <nav id="sidebar">
        <button type="button" id="sidebarClose" class="btn btn-link text-white d-block d-md-none position-absolute top-0 end-0 mt-3 me-2" style="z-index: 1060; text-decoration: none;">
            <i class="fa-solid fa-xmark fs-3"></i>
        </button>
            <div class="sidebar-header d-flex align-items-center justify-content-center py-3">
                <img src="../public/img/logo-nav.svg" alt="Logo" class="img-fluid me-2" style="max-width: 40px;">
                <h4 class="fw-bold mb-0">Technobyte</h4>
            </div>
            
            <ul class="list-unstyled components">
                <li class="active"><a href="dashboard.php"><i class="fa-solid fa-house"></i> Panel Principal</a></li>
                <li><a href="admin/ventas/crear.php"><i class="fa-solid fa-cart-shopping"></i> Nueva Venta</a></li>
                <?php if(tieneRol([ROL_ADMIN, ROL_SUPERVISOR, ROL_VENDEDOR])): ?><li><a href="admin/ventas/listar.php"><i class="fa-solid fa-file-invoice-dollar"></i> Historial Ventas</a></li><?php endif; ?>
                <?php if(tieneRol([ROL_ADMIN, ROL_SUPERVISOR])): ?><li><a href="admin/categorias/listar.php"><i class="fa-solid fa-tags"></i> Categorías</a></li><?php endif; ?>
                <?php if(tieneRol([ROL_ADMIN, ROL_SUPERVISOR])): ?><li><a href="admin/marcas/listar.php"><i class="fa-solid fa-award"></i> Marcas</a></li><?php endif; ?>
                <li><a href="admin/productos/listar.php"><i class="fa-solid fa-cubes"></i> Productos</a></li>
                <li><a href="admin/clientes/listar.php"><i class="fa-solid fa-users"></i> Clientes</a></li>
                <?php if(tieneRol([ROL_ADMIN])): ?><li><a href="admin/usuarios/listar.php"><i class="fa-solid fa-user-shield"></i> Usuarios</a></li><?php endif; ?>
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
                                    <h3 class="fw-bold text-success">$ <?php echo number_format($ventasDia, 2); ?></h3>
                                </div>
                                <i class="fa-solid fa-cash-register icon-resumen"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="card card-resumen h-100 p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted">Ventas realizadas</h6>
                                    <h3 class="fw-bold text-primary"><?php echo $transaccionesDia; ?></h3>
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
                                    <h3 class="fw-bold text-danger"><?php echo $stockBajo; ?></h3>
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
                                    <h3 class="fw-bold text-info"><?php echo $nuevosClientes; ?></h3>
                                </div>
                                <i class="fa-solid fa-users icon-resumen"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-8 mb-3">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-white fw-bold py-3">
                                <i class="fa-solid fa-chart-column text-primary"></i> Top 5 Productos Más Vendidos
                            </div>
                            <div class="card-body" style="position: relative; height: 300px;">
                                <canvas id="graficaProductos"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-white fw-bold py-3">
                                <i class="fa-solid fa-chart-pie text-success"></i> Ventas por Categoría
                            </div>
                            <div class="card-body" style="position: relative; height: 300px;">
                                <canvas id="graficaCategorias"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-7 mb-3">
                        <div class="card shadow-sm border-0 h-100">
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
                                            <?php if (count($ultimasVentas) > 0): ?>
                                                <?php foreach ($ultimasVentas as $venta): ?>
                                                    <tr>
                                                        <td class="fw-bold text-primary"><?php echo $venta['numero_factura']; ?></td>
                                                        <td><?php echo htmlspecialchars($venta['nombre_cliente']); ?></td>
                                                        <td class="fw-bold text-success">$ <?php echo number_format($venta['total_venta'], 2); ?></td>
                                                        <td>
                                                            <?php if ($venta['estado_venta'] === 'Realizada'): ?>
                                                                <span class="badge bg-success">Realizada</span>
                                                            <?php elseif ($venta['estado_venta'] === 'Pendiente'): ?>
                                                                <span class="badge bg-warning text-dark">Pendiente</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-danger">Anulada</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center py-4 text-muted">Aún no hay ventas registradas en el sistema.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-5 mb-3">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-white fw-bold py-3">
                                <i class="fa-solid fa-triangle-exclamation text-danger"></i> <span>Productos con Stock Bajo (5 o menos)</span> 
                            </div>
                            <div class="card-body p-0" style="max-height: 350px; overflow-y: auto;">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 text-center align-middle">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Código</th>
                                                <th>Producto</th>
                                                <th>Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($listaStockBajo) > 0): ?>
                                                <?php foreach ($listaStockBajo as $prodBajo): ?>
                                                    <tr>
                                                        <td class="fw-bold text-muted"><?php echo htmlspecialchars($prodBajo['codigo_producto']); ?></td>
                                                        <td><?php echo htmlspecialchars($prodBajo['nombre_producto']); ?></td>
                                                        <td class="fw-bold text-danger"><?php echo $prodBajo['stock_producto']; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="3" class="text-center py-4 text-success"><i class="fa-solid fa-check-circle"></i> Todos los productos tienen buen stock.</td>
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
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../public/js/main.js?v=<?php echo time(); ?>"></script>

    <script>
       
        // 2. RECIBIR LOS DATOS DE PHP Y CONVERTIRLOS A JAVASCRIPT
        const datosProductos = <?php echo json_encode($topProductos); ?>;
        const datosCategorias = <?php echo json_encode($ventasPorCategoria); ?>;

        // Extraer las etiquetas y cantidades mapeando los arreglos
        const etiquetasProductos = datosProductos.map(item => item.nombre_producto);
        const cantidadesProductos = datosProductos.map(item => item.total_vendido);

        const etiquetasCategorias = datosCategorias.map(item => item.nombre_categoria);
        const cantidadesCategorias = datosCategorias.map(item => item.cantidad_por_categoria);

        
        // 3. RENDERIZAR GRÁFICA DE BARRAS (TOP PRODUCTOS)
        const ctxProductos = document.getElementById('graficaProductos').getContext('2d');
        new Chart(ctxProductos, {
            type: 'bar',
            data: {
                labels: etiquetasProductos.length > 0 ? etiquetasProductos : ['Sin Datos'],
                datasets: [{
                    label: 'Unidades Vendidas',
                    data: cantidadesProductos.length > 0 ? cantidadesProductos : [0],
                    backgroundColor: 'rgba(0, 143, 191, 0.8)', /* Azul claro Technobyte */
                    borderColor: '#008fbf',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: { 
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: { 
                    y: { beginAtZero: true } 
                }
            }
        });

       
        // 4. RENDERIZAR GRÁFICA DE ANILLO (CATEGORÍAS)
        const ctxCategorias = document.getElementById('graficaCategorias').getContext('2d');
        new Chart(ctxCategorias, {
            type: 'doughnut',
            data: {
                labels: etiquetasCategorias.length > 0 ? etiquetasCategorias : ['Sin Datos'],
                datasets: [{
                    data: cantidadesCategorias.length > 0 ? cantidadesCategorias : [1],
                    backgroundColor: [
                        '#00143C', 
                        '#008fbf', 
                        '#28a745', 
                        '#ffc107', 
                        '#dc3545', 
                        '#17a2b8'  
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: { 
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    </script>
</body>
</html>