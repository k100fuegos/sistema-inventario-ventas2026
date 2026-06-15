<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Venta - Tecnobyte</title>
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
                <li class="active"><a href="crear.php"><i class="fa-solid fa-cart-shopping"></i> Nueva Venta</a></li>
                <li><a href="listar.php"><i class="fa-solid fa-file-invoice-dollar"></i> Historial Ventas</a></li>
                <li><a href="../categorias/listar.php"><i class="fa-solid fa-tags"></i> Categorías</a></li>
                <li><a href="../productos/listar.php"><i class="fa-solid fa-cubes"></i> Productos</a></li>
                <li><a href="../clientes/listar.php"><i class="fa-solid fa-users"></i> Clientes</a></li>
                <li><a href="../usuarios/listar.php"><i class="fa-solid fa-user-shield"></i> Usuarios</a></li>
            </ul>
        </nav>

        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-dorado"><i class="fa-solid fa-bars"></i></button>
                </div>
            </nav>

            <div class="container-fluid p-4">
               
                <form action="" method="POST">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white pt-4 pb-3">
                            <h5 class="fw-bold">Nueva Venta</h5>
                            <div class="input-group mt-3">
                                
                                <input type="text" class="form-control" name="buscar_codigo" placeholder="Escriba o escanee el codigo del producto..." required>
                                <button class="btn btn-primary fw-bold" type="button">Agregar al Detalle</button>
                            </div>
                        </div>
                        
                        <div class="card-body p-0">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-3">producto (nombre)</th>
                                        <th class="text-center">precio_unitario</th>
                                        <th class="text-center">cantidad</th>
                                        <th class="text-center">Subtotal</th>
                                        <th class="text-center">Quitar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    <tr>
                                        <td class="px-3 fw-bold">Teclado Mecánico</td>
                                        <td class="text-center">$ 45.00</td>
                                        <td class="text-center"><input type="number" class="form-control form-control-sm mx-auto" name="cantidad_vender" value="1" style="width: 70px;"></td>
                                        <td class="text-center fw-bold">$ 45.00</td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        
                        <div class="card-footer bg-white p-4 border-top">
                            <div class="row align-items-center">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label fw-bold">id_cliente (Relación)</label>
                                    <select class="form-select" name="id_cliente" required>
                                        <!-- LÓGICA PHP: Bucle de tabla clientes (Por defecto id_cliente 1 Consumidor Final) -->
                                        <option value="1">Consumidor Final</option>
                                    </select>
                                    <!-- Input oculto obligatorio para rastrear qué usuario ejecutó la acción (id_usuario de tu BD) -->
                                    <input type="hidden" name="id_usuario" value="LÓGICA PHP: $_SESSION['id_usuario']">
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <p class="text-muted mb-1">subtotal: <span class="fw-bold text-dark">$ 398.23</span></p>
                                    <p class="text-muted mb-2">iva (13%): <span class="fw-bold text-dark">$ 51.77</span></p>
                                    <h3 class="fw-bold text-success mb-3">total: $ 450.00</h3>
                                    
                                    <!-- Ocultos para mandar las variables limpias al INSERT de SQL -->
                                    <input type="hidden" name="subtotal" value="">
                                    <input type="hidden" name="iva" value="">
                                    <input type="hidden" name="total" value="">
                                    <input type="hidden" name="estado" value="1">
                                    
                                    <button type="submit" class="btn btn-success btn-lg fw-bold px-5"><i class="fa-solid fa-money-bill-wave me-2"></i> Procesar Venta</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../public/js/main.js"></script>
</body>
</html>