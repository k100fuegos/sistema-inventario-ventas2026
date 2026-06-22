<?php
// =============================================================
// control_acceso.php — Autenticación y control de roles
// Incluir al inicio de cada página protegida del sistema.
// =============================================================

// Roles (coinciden con id_rol en la tabla `roles`)
define('ROL_ADMIN',      1); // Administrador
define('ROL_SUPERVISOR', 2); // Encargado de Inventario
define('ROL_VENDEDOR',   3); // Vendedor / Cajero

// Inicia sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirige al login si el usuario no ha iniciado sesión
function requerirLogin(): void
{
    if (!isset($_SESSION['id_usuario'])) {
        header('Location: ' . _rutaLogin());
        exit();
    }
}

// Verifica que el usuario tenga uno de los roles permitidos.
// Si no está autenticado → redirige al login.
// Si está autenticado pero sin permiso → muestra acceso denegado.
// $rolesPermitidos puede ser un int o un array de ints.
function requerirRol($rolesPermitidos): void
{
    requerirLogin();

    $rolActual = (int) ($_SESSION['id_rol'] ?? 0);

    if (!is_array($rolesPermitidos)) {
        $rolesPermitidos = [$rolesPermitidos];
    }

    if (!in_array($rolActual, $rolesPermitidos, true)) {
        _mostrarAccesoDenegado();
    }
}

// Devuelve true si el usuario tiene alguno de los roles dados.
// No redirige — útil para mostrar/ocultar elementos en la vista.
function tieneRol($roles): bool
{
    if (!isset($_SESSION['id_rol'])) {
        return false;
    }
    if (!is_array($roles)) {
        $roles = [$roles];
    }
    return in_array((int) $_SESSION['id_rol'], $roles, true);
}

// =============================================================
// Funciones internas (prefijo _ = no llamar desde las vistas)
// =============================================================

// Muestra la pantalla de Acceso Denegado y detiene la ejecución
function _mostrarAccesoDenegado(): void
{
    $usuario = htmlspecialchars($_SESSION['nombre']     ?? 'Usuario', ENT_QUOTES, 'UTF-8');
    $rol     = htmlspecialchars($_SESSION['nombre_rol'] ?? '',        ENT_QUOTES, 'UTF-8');

    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Acceso Denegado — Technobyte</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    </head>
    <body class="bg-dark d-flex align-items-center justify-content-center min-vh-100">

        <div class="card border-0 border-top border-danger border-4 shadow-lg text-center p-5"
             style="max-width: 460px; width: 90%; background-color: #1e2130;">

            <div class="mb-4">
                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10 border border-danger p-4">
                    <i class="fa-solid fa-shield-halved text-danger fa-3x"></i>
                </span>
            </div>

            <h1 class="fs-3 fw-bold text-danger mb-1">Acceso Denegado</h1>
            <p class="text-secondary mb-4">
                No tienes los permisos necesarios para acceder a esta sección.
            </p>

            <div class="bg-black bg-opacity-25 rounded-3 p-3 mb-4 text-start border border-secondary border-opacity-25">
                <small class="text-uppercase text-secondary fw-bold d-block mb-1" style="font-size:0.7rem;letter-spacing:1px;">
                    Usuario autenticado
                </small>
                <p class="text-white fw-semibold mb-3"><?= $usuario ?></p>

                <small class="text-uppercase text-secondary fw-bold d-block mb-1" style="font-size:0.7rem;letter-spacing:1px;">
                    Tu rol actual
                </small>
                <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill mb-3">
                    <?= $rol ?>
                </span>

                <small class="text-uppercase text-secondary fw-bold d-block mb-1" style="font-size:0.7rem;letter-spacing:1px;">
                    Módulos permitidos para ti
                </small>
                <p class="text-white fw-semibold mb-1" style="font-size: 0.85rem;">
                    <?php
                    $idRol = (int)($_SESSION['id_rol'] ?? 0);
                    if ($idRol === ROL_VENDEDOR) {
                        echo "&bull; Panel Principal<br>&bull; Nueva Venta<br>&bull; Productos<br>&bull; Clientes";
                    } elseif ($idRol === ROL_SUPERVISOR) {
                        echo "&bull; Panel Principal<br>&bull; Ventas<br>&bull; Categorías y Marcas<br>&bull; Productos<br>&bull; Clientes";
                    } else {
                        echo "&bull; Todos los módulos (Admin)";
                    }
                    ?>
                </p>
            </div>

            <p class="text-secondary mb-4" style="font-size:0.88rem;">
                <strong class="text-danger">Acceso denegado por Rol o rango.</strong><br>
                Solo puedes ver los lugares indicados arriba. Si crees que deberías tener acceso, contacta al Administrador.
            </p>

            <div class="d-flex flex-wrap justify-content-center gap-2">
                <a href="javascript:history.back()" class="btn btn-danger fw-bold px-4">
                    <i class="fa-solid fa-arrow-left me-1"></i> Regresar
                </a>
                <a href="<?= _rutaDashboard() ?>" class="btn btn-outline-secondary fw-semibold px-4">
                    <i class="fa-solid fa-house me-1"></i> Ir al Panel
                </a>
            </div>

            <hr class="border-secondary border-opacity-25 my-4">

            <p class="text-secondary mb-0" style="font-size:0.75rem;">
                Technobyte &mdash; Sistema de Inventario y Ventas &copy; <?= date('Y') ?>
            </p>

        </div>

    </body>
    </html>
    <?php
    exit();
}

// Calcula la ruta relativa al login desde el script actual (compatible Windows)
function _rutaLogin(): string
{
    $raiz   = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');
    $actual = realpath(dirname($_SERVER['SCRIPT_FILENAME'] ?? ''));

    if ($raiz && $actual) {
        $relativo = str_replace('\\', '/', substr($actual, strlen($raiz)));
        $niveles  = substr_count(ltrim($relativo, '/'), '/');
        return str_repeat('../', $niveles) . 'login.php';
    }

    return '/ActividadFinal/login.php';
}

// Calcula la ruta relativa al dashboard desde el script actual (compatible Windows)
function _rutaDashboard(): string
{
    $raiz   = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');
    $actual = realpath(dirname($_SERVER['SCRIPT_FILENAME'] ?? ''));

    if ($raiz && $actual) {
        $relativo = str_replace('\\', '/', substr($actual, strlen($raiz)));
        $niveles  = substr_count(ltrim($relativo, '/'), '/');
        return str_repeat('../', $niveles) . 'presentacion/dashboard.php';
    }

    return '/ActividadFinal/presentacion/dashboard.php';
}
