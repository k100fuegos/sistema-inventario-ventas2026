<?php
require_once __DIR__ . '/../../../config/control_acceso.php';
requerirLogin();
requerirRol([ROL_ADMIN, ROL_SUPERVISOR]);


require_once __DIR__ . '/../../../negocio/CategoriaNegocio.php';

$categoriaNegocio = new CategoriaNegocio();
$errores = [];
$datos = [
    'nombre_categoria' => '',
    'descripcion_categoria' => '',
    'estado_categoria' => 1
    ];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre_categoria' => $_POST['nombre_categoria'] ?? '',
        'descripcion_categoria' => $_POST['descripcion_categoria'] ?? '',
        'estado_categoria' => $_POST['estado_categoria'] ?? 1
    ];

    $resultado = $categoriaNegocio->crearCategoria($datos);

    if ($resultado['exito']) {
        header('Location: listar.php?mensaje=creado');
        exit;
    }

    $errores = $resultado['errores'];
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
    <title>Nueva Categoría - Tecnobyte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/style.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class="d-flex">
        <!-- SIDEBAR -->
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>

        <div id="content">
            <?php include __DIR__ . '/../../includes/header.php'; ?>

            <div class="container-fluid p-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white pt-4 pb-3">
                        <h4 class="fw-bold mb-0 text-primary">Registrar Nueva Categoría</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($errores)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errores as $error): ?>
                                        <li><?php echo mostrarValor($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nombre de la categoria:</label>
                                <input type="text" class="form-control" name="nombre_categoria" value="<?php echo mostrarValor($datos['nombre_categoria']); ?>" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Descripcion:</label>
                                <textarea class="form-control" name="descripcion_categoria" rows="3"><?php echo mostrarValor($datos['descripcion_categoria']); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Estado:</label>
                                <select class="form-select" name="estado_categoria">
                                    <option value="1" <?php echo $datos['estado_categoria'] == '1' ? 'selected' : ''; ?>>Activo</option>
                                    <option value="0" <?php echo $datos['estado_categoria'] == '0' ? 'selected' : ''; ?>>Inactivo</option>
                                </select>
                                <i class="fa-solid fa-circle-info me-1"></i>
                                <small class="text-muted">
                                    Las categorías inactivas no podrán seleccionarse al registrar productos.
                                </small>
                            </div>
                            <hr>
                            <div class="text-end">
                                <a href="listar.php" class="btn btn-secondary px-4 fw-bold">Cancelar</a>
                                <button type="submit" class="btn btn-primary px-4 fw-bold">Guardar Categoría</button>
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