<?php
require_once __DIR__ . '/../../../config/control_acceso.php';
requerirLogin();


require_once __DIR__ . '/../../../negocio/MarcaNegocio.php';

$marcaNegocio = new MarcaNegocio();
$errores = [];
$datos = [
    'nombre_marca' => '',
    'estado_marca' => 1
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre_marca' => $_POST['nombre_marca'] ?? '',
        'estado_marca' => $_POST['estado_marca'] ?? 1
    ];

    $resultado = $marcaNegocio->crearMarca($datos);

    if ($resultado['exito']) {
        header('Location: listar.php?mensaje=creado');
        exit;
    }

    // Si hubo error de reactivación y se reactivó correctamente, también mandamos éxito
    if (strpos($resultado['mensaje'] ?? '', 'restaurada correctamente') !== false) {
        header('Location: listar.php?mensaje=creado');
        exit;
    }

    $errores = isset($resultado['errores']) ? $resultado['errores'] : [$resultado['mensaje']];
}

function mostrarValor($valor) {
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Marca - Technobyte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
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
                <li><a href="../ventas/listar.php"><i class="fa-solid fa-file-invoice-dollar"></i> Historial Ventas</a></li>
                <li><a href="../categorias/listar.php"><i class="fa-solid fa-tags"></i> Categorías</a></li>
                <li class="active"><a href="listar.php"><i class="fa-solid fa-award"></i> Marcas</a></li>
                <li><a href="../productos/listar.php"><i class="fa-solid fa-cubes"></i> Productos</a></li>
                <li><a href="../clientes/listar.php"><i class="fa-solid fa-users"></i> Clientes</a></li>
                <li><a href="../usuarios/listar.php"><i class="fa-solid fa-user-shield"></i> Usuarios</a></li>
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
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white pt-4 pb-3">
                        <h4 class="fw-bold mb-0 text-primary">Registrar Nueva Marca</h4>
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
                        
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nombre de la Marca:</label>
                                <input type="text" class="form-control" name="nombre_marca" value="<?php echo mostrarValor($datos['nombre_marca']); ?>" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Estado:</label>
                                <select class="form-select" name="estado_marca">
                                    <option value="1" <?php echo $datos['estado_marca'] == '1' ? 'selected' : ''; ?>>Activo</option>
                                    <option value="0" <?php echo $datos['estado_marca'] == '0' ? 'selected' : ''; ?>>Inactivo</option>
                                </select>
                                <i class="fa-solid fa-circle-info me-1 mt-2 text-muted"></i>
                                <small class="text-muted">
                                    Las marcas inactivas no podrán seleccionarse al registrar nuevos productos.
                                </small>
                            </div>
                            <hr>
                            <div class="text-end">
                                <a href="listar.php" class="btn btn-secondary px-4 fw-bold">Cancelar</a>
                                <button type="submit" class="btn btn-primary px-4 fw-bold">Guardar Marca</button>
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