<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Cliente - Tecnobyte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/style.css">
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
                <li><a href="../categorias/listar.php"><i class="fa-solid fa-tags"></i> Categorías</a></li>
                <li><a href="../productos/listar.php"><i class="fa-solid fa-cubes"></i> Productos</a></li>
                <li class="active"><a href="listar.php"><i class="fa-solid fa-users"></i> Clientes</a></li>
                <li><a href="../usuarios/listar.php"><i class="fa-solid fa-user-shield"></i> Usuarios</a></li>
            </ul>
        </nav>

        <div id="content">
            <div class="container-fluid p-4">
                <div class="card shadow-sm border-0 border-top border-warning border-4">
                    <div class="card-header bg-white pt-4 pb-3">
                        <h4 class="fw-bold mb-0 text-warning">Editar Cliente</h4>
                    </div>
                    <div class="card-body p-4">
                        
                        <form action="" method="POST">
                            <input type="hidden" name="id_cliente" value="">
                            <div class="mb-3">
                                <label class="form-label fw-bold">nombre_completo</label>
                                <input type="text" class="form-control" name="nombre_completo" value="LÓGICA PHP" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">telefono</label>
                                    <input type="text" class="form-control" name="telefono" value="LÓGICA PHP">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">correo</label>
                                    <input type="email" class="form-control" name="correo" value="LÓGICA PHP">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">direccion</label>
                                <textarea class="form-control" name="direccion" rows="3">LÓGICA PHP</textarea>
                            </div>
                            <hr>
                            <div class="text-end">
                                <a href="listar.php" class="btn btn-secondary px-4 fw-bold">Cancelar</a>
                                <button type="submit" class="btn btn-warning text-dark px-4 fw-bold">Actualizar Cliente</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../public/js/main.js"></script>
</body>
</html>