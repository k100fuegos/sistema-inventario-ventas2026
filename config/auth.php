<?php

//Control de Autenticación y Roles
// ROLES del sistema (id_rol en la tabla `roles`):
//   1 = Administrador
//   2 = Encargado de Inventario (Supervisor)
//   3 = Vendedor / Cajero

// -----------------------------------------------
// Constantes de roles para uso en toda la app
// -----------------------------------------------
define('ROL_ADMIN',      1);
define('ROL_SUPERVISOR', 2);
define('ROL_VENDEDOR',   3);

// -----------------------------------------------
// Inicia sesión si no está activa
// -----------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica que el usuario haya iniciado sesión.
 * Si no, redirige al login.
 */
function requerirLogin(): void
{
    if (!isset($_SESSION['id_usuario'])) {
        header('Location: ' . obtenerRutaLogin());
        exit();
    }
}

/**
 * Verifica que el usuario tenga uno de los roles permitidos.
 * Si el usuario no está logueado, redirige al login.
 * Si está logueado pero no tiene permiso, muestra la pantalla de "Acceso Denegado".
 *
 * @param int|array $rolesPermitidos  Rol o array de roles que tienen acceso.
 */
function requerirRol($rolesPermitidos): void
{
    // Primero, garantizar que esté autenticado
    requerirLogin();

    $idRolActual = (int) ($_SESSION['id_rol'] ?? 0);

    if (!is_array($rolesPermitidos)) {
        $rolesPermitidos = [$rolesPermitidos];
    }

    if (!in_array($idRolActual, $rolesPermitidos, true)) {
        mostrarAccesoDenegado();
    }
}

/**
 * Devuelve true si el usuario tiene alguno de los roles indicados.
 * No redirige ni detiene la ejecución.
 *
 * @param int|array $roles
 * @return bool
 */
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

/**
 * Muestra la pantalla HTML de "Acceso Denegado" y termina la ejecución.
 * Diseño consistente con el sistema.
 */
function mostrarAccesoDenegado(): void
{
    $nombreUsuario = htmlspecialchars($_SESSION['nombre'] ?? 'Usuario', ENT_QUOTES, 'UTF-8');
    $nombreRol     = htmlspecialchars($_SESSION['nombre_rol'] ?? '', ENT_QUOTES, 'UTF-8');

    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Acceso Denegado - Technobyte</title>
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
                No tienes los permisos necesarios para acceder a esta sección del sistema.
            </p>

            <div class="bg-black bg-opacity-25 rounded-3 p-3 mb-4 text-start border border-secondary border-opacity-25">
                <small class="text-uppercase text-secondary fw-bold" style="font-size: 0.7rem; letter-spacing: 1px;">
                    Usuario autenticado
                </small>
                <p class="text-white fw-semibold mb-2"><?= $nombreUsuario ?></p>

                <small class="text-uppercase text-secondary fw-bold" style="font-size: 0.7rem; letter-spacing: 1px;">
                    Tu rol actual
                </small>
                <div>
                    <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill">
                        <?= $nombreRol ?>
                    </span>
                </div>
            </div>

            <p class="text-secondary mb-4" style="font-size: 0.88rem;">
                <strong class="text-danger">Acceso denegado por Rol o rango.</strong><br>
                Si crees que deberías tener acceso, contacta al Administrador.
            </p>

            <div class="d-flex flex-wrap justify-content-center gap-2">
                <a href="javascript:history.back()" class="btn btn-danger fw-bold px-4">
                    <i class="fa-solid fa-arrow-left me-1"></i> Regresar
                </a>
                <a href="<?= obtenerRutaDashboard() ?>" class="btn btn-outline-secondary fw-semibold px-4">
                    <i class="fa-solid fa-house me-1"></i> Ir al Panel
                </a>
            </div>

            <hr class="border-secondary border-opacity-25 my-4">

            <p class="text-secondary mb-0" style="font-size: 0.75rem;">
                Technobyte &mdash; Sistema de Inventario y Ventas &copy; <?= date('Y') ?>
            </p>

        </div>

    </body>
    </html>
    <?php
    exit();
}

/**
 * Calcula la ruta relativa al login desde el archivo que llama a esta función.
 */
function obtenerRutaLogin(): string
{
    // Detectar profundidad desde la raíz del proyecto
    $raiz = realpath(__DIR__ . '/..');
    $actual = realpath(dirname($_SERVER['SCRIPT_FILENAME'] ?? ''));

    if ($raiz && $actual) {
        $relativo = str_replace('\\', '/', substr($actual, strlen($raiz)));
        $niveles  = substr_count(ltrim($relativo, '/'), '/');
        $prefijo  = str_repeat('../', $niveles);
        return $prefijo . 'login.php';
    }

    return '/ProjectoFInal/login.php';
}

/**
 * Calcula la ruta relativa al dashboard desde el archivo que llama a esta función.
 */
function obtenerRutaDashboard(): string
{
    $raiz = realpath(__DIR__ . '/..');
    $actual = realpath(dirname($_SERVER['SCRIPT_FILENAME'] ?? ''));

    if ($raiz && $actual) {
        $relativo = str_replace('\\', '/', substr($actual, strlen($raiz)));
        $niveles  = substr_count(ltrim($relativo, '/'), '/');
        $prefijo  = str_repeat('../', $niveles);
        return $prefijo . 'presentacion/dashboard.php';
    }

    return '/ProjectoFInal/presentacion/dashboard.php';
}
