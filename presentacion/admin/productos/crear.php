<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Producto - Tecnobyte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="bg-light">
    <div class="d-flex">
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
                <li class="active"><a href="listar.php"><i class="fa-solid fa-cubes"></i> Productos</a></li>
                <li><a href="../clientes/listar.php"><i class="fa-solid fa-users"></i> Clientes</a></li>
                <li><a href="../usuarios/listar.php"><i class="fa-solid fa-user-shield"></i> Usuarios</a></li>
            </ul>
        </nav>

        <div id="content">
            <div class="container-fluid p-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white pt-4 pb-3">
                        <h4 class="fw-bold mb-0 text-primary">Registrar Nuevo Producto</h4>
                    </div>
                    <div class="card-body p-4">
                        
                        <form action="" method="POST" enctype="multipart/form-data">
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">codigo</label>
                                    <input type="text" class="form-control" name="codigo" required>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label class="form-label fw-bold">nombre</label>
                                    <input type="text" class="form-control" name="nombre" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold">Imagen del Producto (Opcional)</label>
                                    <input type="file" class="form-control" name="imagen" accept="image/*">
                                    <div class="form-text">Formatos permitidos: JPG, PNG, WEBP.</div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">id_categoria (Relación)</label>
                                    <select class="form-select" name="id_categoria" required>
                                        <option value="">Seleccione una categoría...</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">estado</label>
                                    <select class="form-select" name="estado">
                                        <option value="1">1 (Activo)</option>
                                        <option value="0">0 (Inactivo)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">precio ($)</label>
                                    <input type="number" step="0.01" class="form-control" name="precio" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">stock</label>
                                    <input type="number" class="form-control" name="stock" value="0" required>
                                </div>
                            </div>

                            <hr>
                            <div class="text-end">
                                <a href="listar.php" class="btn btn-secondary px-4 fw-bold">Cancelar</a>
                                <button type="submit" class="btn btn-primary px-4 fw-bold">Guardar Producto</button>
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