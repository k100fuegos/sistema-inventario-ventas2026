<?php

require_once __DIR__ . '/../../../negocio/ProductoNegocio.php';

$productoNegocio = new ProductoNegocio();
$productos = $productoNegocio->listarProductos();



$mensaje = $_GET['mensaje'] ?? '';

function mostrarValor($valor)
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Productos - Tecnobyte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/style.css?v=<?php echo time(); ?>">
</head>

<body>
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
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-dorado"><i class="fa-solid fa-bars"></i></button>
                    <div class="ms-auto d-flex align-items-center">
                        <span class="me-3 fw-bold"><i class="fa-solid fa-circle-user"></i> LÓGICA PHP</span>
                        <a href="../../../logout.php" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
                    </div>
                </div>
            </nav>

            <div class="container-fluid p-4">
                <h2 class="mb-4 text-dark fw-bold"><i class="fa-solid fa-cubes text-secondary"></i> Gestión de Productos</h2>

                <div class="row mb-4 align-items-center">
                    <div class="col-md-6">
                        <form action="" method="GET" class="d-flex">
                            <input type="text" class="form-control me-2" name="buscar_producto" placeholder="Buscar por código o nombre...">
                            <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form>
                    </div>

                    <?php if ($mensaje === 'creado'): ?>
                        <div class="alert alert-success">Producto registrado correctamente</div>
                    <?php elseif ($mensaje === 'actualizado'): ?>
                        <div class="alert alert-success">Producto actualizado correctamente</div>
                    <?php elseif ($mensaje === 'eliminado'): ?>
                        <div class="alert alert-success">Producto eliminado correctamente</div>
                    <?php endif; ?>

                    <div class="col-md-6 text-md-end">
                        <a href="crear.php" class="btn btn-primary fw-bold"><i class="fa-solid fa-plus"></i> Nuevo Producto</a>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="table-responsive rounded-2">
                        <table class="table table-hover table-striped mb-0 align-middle text-center">
                            <thead class="table-dark">
                                <tr">
                                    <th>Código</th>
                                    <th>Imagen</th>
                                    <th>Nombre</th>
                                    <th>Modelo</th>
                                    <th>Descripcion</th>
                                    <th>Categoría</th>
                                    <th>Marca</th>
                                    <th>Stock</th>
                                    <th>Precio</th>
                                    <th>Acciones</th>
                                    </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($productos)):
                                    foreach ($productos as $producto): ?>
                                        <tr>
                                            <td class="py-5"><?php echo mostrarValor($producto['codigo_producto']); ?></td>
                                            <td>
                                                <?php
                                                $imagenProducto = $producto['imagen_producto'] ?: 'sin-imagen.png';
                                                $rutaImagen = $producto['imagen_producto'] !== 'sin-imagen.png'
                                                    ? "../../../public/img/productos/{$imagenProducto}"
                                                    : "../../../public/img/{$imagenProducto}";

                                                if (empty($imagenProducto) || !file_exists($rutaImagen)) {
                                                    $rutaImagen = "../../../public/img/sin-imagen.png";
                                                }
                                                ?>
                                                <img src="<?php echo mostrarValor($rutaImagen); ?>"
                                                    alt="Imagen del producto"
                                                    class="img-thumbnail"
                                                    style="width: 70px; height: 70px; object-fit: cover;">
                                            </td>

                                            <td><?php echo mostrarValor($producto['nombre_producto']); ?></td>
                                            <td><?php echo mostrarValor($producto['modelo_producto']); ?></td>
                                            <td><?php echo mostrarValor($producto['descripcion_producto']); ?></td>
                                            <td><?php echo mostrarValor($producto['nombre_categoria']); ?></td>
                                            <td><?php echo mostrarValor($producto['nombre_marca']); ?></td>
                                            <td><?php echo mostrarValor($producto['stock_producto']); ?></td>
                                            <td>$<?php echo number_format((float) $producto['precio_producto'], 2); ?></td>
                                            <td>
                                                <a href="editar.php?id=<?php echo mostrarValor($producto['id_producto']); ?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></a>
                                                <a href="eliminar.php?id=<?php echo mostrarValor($producto['id_producto']); ?>" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></a>

                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No hay productos registrados</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../public/js/main.js"></script>
</body>

</html>