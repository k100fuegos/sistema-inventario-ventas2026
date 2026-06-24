<?php
require_once __DIR__ . '/../../../config/control_acceso.php';
requerirLogin();
requerirRol([ROL_ADMIN, ROL_SUPERVISOR, ROL_VENDEDOR]);


require_once __DIR__ . '/../../../negocio/ProductoNegocio.php';
require_once __DIR__ . '/../../../negocio/MarcaNegocio.php';
require_once __DIR__ . '/../../../negocio/CategoriaNegocio.php';

$productoNegocio = new ProductoNegocio();
$marcaNegocio = new MarcaNegocio();
$categoriaNegocio = new CategoriaNegocio();

$marcas = $marcaNegocio->listarMarcas();
$categorias = $categoriaNegocio->listarCategorias();

$errores = [];
$datos = [
    'codigo_producto'      => '',
    'nombre_producto'      => '',
    'modelo_producto'      => '',
    'descripcion_producto' => '',
    'id_marca'             => '',
    'id_categoria'         => '',
    'precio_producto'      => '',
    'stock_producto'       => '',
    'estado_producto'      => 1
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'codigo_producto'      => $_POST['codigo_producto'] ?? '',
        'nombre_producto'      => $_POST['nombre_producto'] ?? '',
        'modelo_producto'      => $_POST['modelo_producto'] ?? '',
        'descripcion_producto' => $_POST['descripcion_producto'] ?? '',
        'id_marca'             => $_POST['id_marca'] ?? '',
        'id_categoria'         => $_POST['id_categoria'] ?? '',
        'precio_producto'      => $_POST['precio_producto'] ?? '',
        'stock_producto'       => $_POST['stock_producto'] ?? '',
        'estado_producto'      => $_POST['estado_producto'] ?? 1
    ];

    // Enviamos los datos del formulario Y el archivo $_FILES de forma independiente
    $resultado = $productoNegocio->crearProducto($datos, $_FILES['imagen_producto'] ?? null);

    if ($resultado['exito'] || strpos($resultado['mensaje'] ?? '', 'restaurado correctamente') !== false) {
        header("Location: listar.php?mensaje=creado");
        exit;
    } else {
        $errores = isset($resultado['errores']) ? $resultado['errores'] : [$resultado['mensaje']];
    }
}

function mostrarValor($valor) {
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Nuevo Producto - Technobyte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/style.css?v=<?php echo time(); ?>">
</head>

<body class="bg-light">
    <div class="d-flex">
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
                <li><a href="../ventas/crear.php"><i class="fa-solid fa-cart-shopping"></i> Nueva Venta</a></li>
                <?php if(tieneRol([ROL_ADMIN, ROL_SUPERVISOR, ROL_VENDEDOR])): ?><li><a href="../ventas/listar.php"><i class="fa-solid fa-file-invoice-dollar"></i> Historial Ventas</a></li><?php endif; ?>
                <?php if(tieneRol([ROL_ADMIN, ROL_SUPERVISOR])): ?><li><a href="../categorias/listar.php"><i class="fa-solid fa-tags"></i> Categorías</a></li><?php endif; ?>
                <?php if(tieneRol([ROL_ADMIN, ROL_SUPERVISOR])): ?><li><a href="../marcas/listar.php"><i class="fa-solid fa-award"></i> Marcas</a></li><?php endif; ?>
                <li class="active"><a href="listar.php"><i class="fa-solid fa-cubes"></i> Productos</a></li>
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
                <div class="card shadow-sm border-0 border-top border-primary border-4">
                    <div class="card-header bg-white pt-4 pb-3">
                        <h4 class="fw-bold mb-0 text-primary">Registrar Nuevo Producto</h4>
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

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Código:</label>
                                    <input type="text" class="form-control" name="codigo_producto" value="<?php echo mostrarValor($datos['codigo_producto']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Nombre del producto:</label>
                                    <input type="text" class="form-control" name="nombre_producto" value="<?php echo mostrarValor($datos['nombre_producto']); ?>" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Estado:</label>
                                    <select class="form-select" name="estado_producto" required>
                                        <option value="1" <?php echo ($datos['estado_producto'] == 1) ? 'selected' : ''; ?>>Activo</option>
                                        <option value="0" <?php echo ($datos['estado_producto'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold">Imagen del Producto (Opcional):</label>
                                    <div class="input-group">
                                        <input type="file" class="form-control" name="imagen_producto" id="imagen_producto" accept=".jpg,.jpeg,.png,.webp">
                                        <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('imagen_producto').value = ''"><i class="fa-solid fa-eraser"></i> Limpiar</button>
                                    </div>
                                    <div class="form-text text-secondary">
                                        <i class="fa-solid fa-circle-info"></i> Nota: Solo se permite asociar 1 imagen por producto. Formatos permitidos: JPG, JPEG, PNG o WEBP. Tamaño máximo: 2 MB.
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Modelo:</label>
                                    <input type="text" class="form-control" name="modelo_producto" value="<?php echo mostrarValor($datos['modelo_producto']); ?>">
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label class="form-label fw-bold">Descripción del producto:</label>
                                     <textarea name="descripcion_producto" id="descripcion_producto" rows="2" class="form-control"><?php echo mostrarValor($datos['descripcion_producto']); ?></textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Categoría: </label>
                                    <select class="form-select" name="id_categoria" required>
                                        <option value="">Seleccione una categoría...</option>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?php echo mostrarValor($categoria['id_categoria']); ?>" <?php echo ($datos['id_categoria'] == $categoria['id_categoria']) ? 'selected' : ''; ?>>
                                                <?php echo mostrarValor($categoria['nombre_categoria']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                     <label class="form-label fw-bold">Marca: </label>
                                    <select class="form-select" name="id_marca" required>
                                        <option value="">Seleccione una marca...</option>
                                        <?php foreach ($marcas as $marca): ?>
                                            <option value="<?php echo mostrarValor($marca['id_marca']); ?>" <?php echo ($datos['id_marca'] == $marca['id_marca']) ? 'selected' : ''; ?>>
                                                <?php echo mostrarValor($marca['nombre_marca']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Precio ($)</label>
                                    <input type="number" step="0.01" class="form-control" name="precio_producto" value="<?php echo mostrarValor($datos['precio_producto']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Stock Inicial</label>
                                    <input type="number" class="form-control" name="stock_producto" value="<?php echo $datos['stock_producto'] === '' ? '0' : mostrarValor($datos['stock_producto']); ?>" required>
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
    <script src="../../../public/js/main.js?v=<?php echo time(); ?>"></script>
</body>

</html>