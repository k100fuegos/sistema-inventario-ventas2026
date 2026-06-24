<?php
require_once __DIR__ . '/../../../config/control_acceso.php';
requerirRol(ROL_ADMIN);

require_once __DIR__ . '/../../../negocio/UsuarioNegocio.php';
require_once __DIR__ . '/../../../negocio/RolNegocio.php';

$usuarioNegocio = new UsuarioNegocio();
$rolNegocio = new RolNegocio();

$usuarios = $usuarioNegocio->listarUsuarios();
$roles = $rolNegocio->listarRoles();

$errores = [];
$datos = [
    'nombre_usuario' => '',
    'id_rol' => '',
    'correo_usuario' => '',
    'password_usuario' => '',
    'confirmation_password_usuario' => '',
    'estado_usuario' => 1,
    ];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre_usuario' => $_POST['nombre_usuario'] ?? '',
        'id_rol' => $_POST['id_rol'] ?? '',
        'correo_usuario' => $_POST['correo_usuario'] ?? '',
        'password_usuario' => $_POST['password_usuario'] ?? '',
        'confirmation_password_usuario' => $_POST['confirmation_password_usuario'] ?? '',
        'estado_usuario' => $_POST['estado_usuario'] ?? 1
    ];


    $resultado = $usuarioNegocio->crearUsuario($datos);

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
    <title>Nuevo Usuario - Tecnobyte</title>
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
                <li><a href="../ventas/crear.php"><i class="fa-solid fa-cart-shopping"></i> Nueva Venta</a></li>
                <?php if(tieneRol([ROL_ADMIN, ROL_SUPERVISOR])): ?>
                <li><a href="../ventas/listar.php"><i class="fa-solid fa-file-invoice-dollar"></i> Historial Ventas</a></li>
                <?php endif; ?>
                <?php if(tieneRol([ROL_ADMIN, ROL_SUPERVISOR])): ?>
                <li><a href="../categorias/listar.php"><i class="fa-solid fa-tags"></i> Categorías</a></li>
                <?php endif; ?>
                <?php if(tieneRol([ROL_ADMIN, ROL_SUPERVISOR])): ?>
                <li><a href="../marcas/listar.php"><i class="fa-solid fa-tags"></i> Marcas</a></li>
                <?php endif; ?>
                <li><a href="../productos/listar.php"><i class="fa-solid fa-cubes"></i> Productos</a></li>
                <li><a href="../clientes/listar.php"><i class="fa-solid fa-users"></i> Clientes</a></li>
                <?php if(tieneRol([ROL_ADMIN])): ?>
                <li class="active"><a href="listar.php"><i class="fa-solid fa-user-shield"></i> Usuarios</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <div id="content">
            <div class="container-fluid p-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white pt-4 pb-3">
                        <h4 class="fw-bold mb-0 text-primary">Registrar Nuevo Usuario</h4>
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
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Nombre del usuario:</label>
                                    <input type="text" class="form-control" name="nombre_usuario" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Rol: </label>
                                    <select class="form-select" name="id_rol" required>
                                        <option value="">Seleccione un rol...</option>
                                        <?php foreach ($roles as $rol): ?>
                                            <option value="<?php echo mostrarValor($rol['id_rol']); ?>">
                                                <?php echo mostrarValor($rol['nombre_rol']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Correo:</label>
                                    <input type="email" class="form-control" name="correo_usuario" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Estado del usuario:</label>
                                <select class="form-select" name="estado_usuario">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Contraseña:</label>
                                    <input type="password" class="form-control" name="password_usuario" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Confirmar contraseña:</label>
                                    <input type="password" class="form-control" name="confirmation_password_usuario" required>
                                </div>
                            </div>
                            <hr>
                            <div class="text-end">
                                <a href="listar.php" class="btn btn-secondary px-4 fw-bold">Cancelar</a>
                                <button type="submit" class="btn btn-primary px-4 fw-bold">Guardar Usuario</button>
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