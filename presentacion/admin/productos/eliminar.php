<?php
require_once __DIR__ . '/../../../config/control_acceso.php';
requerirLogin();
requerirRol([ROL_ADMIN, ROL_SUPERVISOR, ROL_VENDEDOR]);


require_once __DIR__ . '/../../../negocio/ProductoNegocio.php';

$productoNegocio = new ProductoNegocio();
$mensajeError = "";

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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idEliminar = $_POST['id_producto'] ?? null;
    $resultado = $productoNegocio->eliminarProducto($idEliminar);

    if ($resultado['exito']) {
        header("Location: listar.php?mensaje=eliminado");
        exit;
    } else {
        $mensajeError = $resultado['mensaje'];
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
    <title>Eliminar Producto - Technobyte</title>
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
                <div class="card shadow-sm border-0 border-top border-danger border-4 text-center p-5 w-100" style="max-width: 550px;">
                    <i class="fa-solid fa-triangle-exclamation text-danger mb-4" style="font-size: 4rem;"></i>
                    <h3 class="fw-bold mb-3">¿Eliminar Producto?</h3>

                    <?php if (!empty($mensajeError)): ?>
                        <div class="alert alert-danger shadow-sm">
                            <?php echo mostrarValor($mensajeError); ?>
                        </div>
                    <?php endif; ?>

                    <p class="text-muted mb-4">¿Está seguro de que desea eliminar el producto <strong><?php echo mostrarValor($producto['nombre_producto']); ?></strong>? Esta acción lo dará de baja en el sistema.</p>

                    <div class="bg-light p-3 rounded mb-4 text-start border">
                        <div class="text-center mb-3">
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
                                class="img-thumbnail shadow-sm"
                                style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        <div class="row text-muted text-center">
                            <div class="col-6 mb-2">
                                <small class="fw-bold d-block text-dark">Código</small>
                                <?php echo mostrarValor($producto['codigo_producto']); ?>
                            </div>
                            <div class="col-6 mb-2">
                                <small class="fw-bold d-block text-dark">Modelo</small>
                                <?php echo mostrarValor($producto['modelo_producto']); ?>
                            </div>
                            <div class="col-6 mb-2">
                                <small class="fw-bold d-block text-dark">Categoría / Marca</small>
                                <?php echo mostrarValor($producto['nombre_categoria']); ?> - <?php echo mostrarValor($producto['nombre_marca']); ?>
                            </div>
                            <div class="col-6 mb-2">
                                <small class="fw-bold d-block text-dark">Precio / Stock</small>
                                $<?php echo number_format((float) $producto['precio_producto'], 2); ?> | <?php echo mostrarValor($producto['stock_producto']); ?>
                            </div>
                        </div>
                    </div>

                    <form action="" method="POST">
                        <input type="hidden" name="id_producto" value="<?php echo mostrarValor($producto['id_producto']); ?>">
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