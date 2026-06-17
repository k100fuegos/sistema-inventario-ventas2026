<?php

require_once __DIR__ . '/../../../negocio/ProductoNegocio.php';
require_once __DIR__ . '/../../../negocio/MarcaNegocio.php';
require_once __DIR__ . '/../../../negocio/CategoriaNegocio.php';

$productoNegocio = new ProductoNegocio();
$marcaNegocio = new MarcaNegocio();
$categoriaNegocio = new CategoriaNegocio();

$categorias = $categoriaNegocio->listarCategorias();
$marcas = $marcaNegocio->listarMarcas();

$errores = [];

$idProducto = $_GET['id'] ?? null;

if (!$idProducto) {
    header("Location: listar.php");
    exit;
}

$producto = $productoNegocio->obtenerProductoPorId($idProducto);

if (!$producto) {
    header("Location: listar.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $imagenActual = $producto['imagen_producto'] ?? 'sin-imagen.png';
    // Se procesa la imagen enviada desde el input name="imagen"
    $imagen = procesarImagen($_FILES['imagen'] ?? null, $errores, $imagenActual);

    $datos = [
        'id_producto'          => $_POST['id_producto'] ?? '',
        'codigo_producto'      => $_POST['codigo_producto'] ?? '',
        'nombre_producto'      => $_POST['nombre_producto'] ?? '',
        'modelo_producto'      => $_POST['modelo_producto'] ?? '',
        'descripcion_producto' => $_POST['descripcion_producto'] ?? '',
        'id_marca'             => $_POST['id_marca'] ?? '',
        'id_categoria'         => $_POST['id_categoria'] ?? '',
        'precio_producto'      => $_POST['precio_producto'] ?? '',
        'stock_producto'       => $_POST['stock_producto'] ?? '',
        'imagen_producto'      => $imagen
    ];

    if (empty($errores)) {
        $resultado = $productoNegocio->actualizarProducto($datos);

        if ($resultado['exito']) {
            header("Location: listar.php?mensaje=actualizado");
            exit;
        } else {
            $errores = $resultado['errores'];
            $producto = $datos;
        }
    } else {
        $producto = $datos;
    }
}

function mostrarValor($valor)
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

function procesarImagen($archivo, &$errores, $imagenActual)
{
    if (!$archivo || $archivo['error'] === UPLOAD_ERR_NO_FILE) {
        return $imagenActual;
    }

    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        $errores[] = "Ocurrió un error al subir la imagen.";
        return $imagenActual;
    }

    $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $extensionesPermitidas)) {
        $errores[] = "La imagen debe tener formato JPG, JPEG, PNG o WEBP.";
        return $imagenActual;
    }

    if ($archivo['size'] > 2 * 1024 * 1024) {
        $errores[] = "La imagen no debe superar los 2 MB.";
        return $imagenActual;
    }

    $directorioDestino = __DIR__ . '/../../../public/img/productos/';

    if (!is_dir($directorioDestino)) {
        mkdir($directorioDestino, 0777, true);
    }

    $nombreArchivo = 'producto_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
    $rutaDestino = $directorioDestino . $nombreArchivo;

    if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
        $errores[] = "No se pudo guardar la imagen del producto.";
        return $imagenActual;
    }

    return $nombreArchivo;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto - Technobyte</title>
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
                        <span class="me-3 fw-bold"><i class="fa-solid fa-circle-user"></i> Administrador</span>
                        <a href="../../../logout.php" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
                    </div>
                </div>
            </nav>

            <div class="container-fluid p-4">
                <div class="card shadow-sm border-0 border-top border-warning border-4">
                    <div class="card-header bg-white pt-4 pb-3">
                        <h4 class="fw-bold mb-0 text-warning">Editar Producto</h4>
                    </div>
                    <div class="card-body p-4">
                        
                        <?php if (!empty($errores)): ?>
                            <div class="alert alert-danger shadow-sm">
                                <ul class="mb-0">
                                    <?php foreach ($errores as $error): ?>
                                        <li><?php echo mostrarValor($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form action="" method="POST" enctype="multipart/form-data">
                            
                            <input type="hidden" name="id_producto" value="<?php echo mostrarValor($producto['id_producto']); ?>">
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Código</label>
                                    <input type="text" class="form-control" name="codigo_producto" value="<?php echo mostrarValor($producto['codigo_producto'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label class="form-label fw-bold">Nombre del Producto</label>
                                    <input type="text" class="form-control" name="nombre_producto" value="<?php echo mostrarValor($producto['nombre_producto'] ?? ''); ?>" required>
                                </div>
                            </div>

                            
                            
                            <div class="row align-items-center mb-4">
                                <div class="col-md-2 text-center">
                                    <?php
                                        $imagenProducto = $producto['imagen_producto'] ?: 'sin-imagen.png';
                                        $rutaImagen = $imagenProducto !== 'sin-imagen.png'
                                            ? "../../../public/img/productos/{$imagenProducto}"
                                            : "../../../public/img/{$imagenProducto}";
                                    ?>
                                    <img src="<?php echo mostrarValor($rutaImagen); ?>" alt="Imagen Actual" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                </div>
                                <div class="col-md-10">
                                    <label class="form-label fw-bold">Actualizar Imagen (Opcional)</label>
                                    <input type="file" class="form-control" name="imagen_producto" accept=".jpg,.jpeg,.png,.webp">
                                    <div class="form-text">Si no selecciona ningún archivo, se mantendrá la imagen actual. Límite de 2MB.</div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Modelo</label>
                                    <input type="text" class="form-control" name="modelo_producto" value="<?php echo mostrarValor($producto['modelo_producto'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Descripción</label>
                                <textarea class="form-control" name="descripcion_producto" rows="3"><?php echo mostrarValor($producto['descripcion_producto'] ?? ''); ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Categoría</label>
                                    <select class="form-select" name="id_categoria" required>
                                        <option value="">Seleccione una categoría...</option>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?php echo mostrarValor($categoria['id_categoria']); ?>" <?php echo (isset($producto['id_categoria']) && $producto['id_categoria'] == $categoria['id_categoria']) ? 'selected' : ''; ?>>
                                                <?php echo mostrarValor($categoria['nombre_categoria']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Marca</label>
                                    <select class="form-select" name="id_marca" required>
                                        <option value="">Seleccione una marca...</option>
                                        <?php foreach ($marcas as $marca): ?>
                                            <option value="<?php echo mostrarValor($marca['id_marca']); ?>" <?php echo (isset($producto['id_marca']) && $producto['id_marca'] == $marca['id_marca']) ? 'selected' : ''; ?>>
                                                <?php echo mostrarValor($marca['nombre_marca']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Precio ($)</label>
                                    <input type="number" step="0.01" min="0" class="form-control" name="precio_producto" value="<?php echo mostrarValor($producto['precio_producto'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Stock</label>
                                    <input type="number" min="0" class="form-control" name="stock_producto" value="<?php echo mostrarValor($producto['stock_producto'] ?? ''); ?>" required>
                                </div>
                            </div>

                            <hr class="mt-4 mb-4">
                            <div class="text-end">
                                <a href="listar.php" class="btn btn-secondary px-4 fw-bold">Cancelar</a>
                                <button type="submit" class="btn btn-warning text-dark px-4 fw-bold">Actualizar Producto</button>
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