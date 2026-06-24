<?php

require_once __DIR__ . '/../../../negocio/MarcaNegocio.php';

$marcaNegocio = new MarcaNegocio();
$errores = [];

$idMarca = $_GET['id'] ?? null;
if (!$idMarca) {
    header('Location: listar.php');
    exit;
}

$marca = $marcaNegocio->obtenerMarcaPorId($idMarca);
if (!$marca) {
    header('Location: listar.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id_marca'     => $idMarca,
        'nombre_marca' => $_POST['nombre_marca'] ?? '',
        'estado_marca' => $_POST['estado_marca'] ?? ''
    ];

    $resultado = $marcaNegocio->actualizarMarca($datos);

    if ($resultado['exito']) {
        header('Location: listar.php?mensaje=actualizado');
        exit;
    }

    $errores = $resultado['errores'];
    $marca = $datos;
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
    <title>Editar Marca - Technobyte</title>
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
                <li class="active"><a href="listar.php"><i class="fa-solid fa-tags"></i> Marcas</a></li>
                <li><a href="../productos/listar.php"><i class="fa-solid fa-cubes"></i> Productos</a></li>
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
                        <h4 class="fw-bold mb-0 text-warning">Editar Marca</h4>
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

                        <form method="post" action="editar.php?id=<?php echo mostrarValor($marca['id_marca']); ?>">

                            <input type="hidden" name="id_marca" value="<?php echo mostrarValor($marca['id_marca']); ?>">

                            <div class="mb-3">
                                <label class="form-label fw-bold">Nombre de la Marca:</label>
                                <input type="text" class="form-control" name="nombre_marca" value="<?php echo mostrarValor($marca['nombre_marca']); ?>" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Estado:</label>
                                <select class="form-select" name="estado_marca">
                                    <option value="1" <?php echo ((int)$marca['estado_marca'] === 1) ? 'selected' : ''; ?>>Activa</option>
                                    <option value="0" <?php echo ((int)$marca['estado_marca'] === 0) ? 'selected' : ''; ?>>Inactiva</option>
                                </select>
                                <small class="text-muted d-block mt-2">
                                    <i class="fa-solid fa-circle-info me-1"></i>
                                    Las marcas inactivas no podrán seleccionarse al registrar productos.
                                </small>
                            </div>
                            <hr>
                            <div class="text-end">
                                <a href="listar.php" class="btn btn-secondary px-4 fw-bold">Cancelar</a>
                                <button type="submit" class="btn btn-warning text-dark px-4 fw-bold">Actualizar Marca</button>
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