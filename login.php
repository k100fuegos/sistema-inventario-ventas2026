<?php
session_start();

if (isset($_SESSION['id_usuario'])) {
    header("Location: presentacion/dashboard.php");
    exit();
}

require_once 'negocio/UsuarioNegocio.php';

$mensajeError = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    $negocio = new UsuarioNegocio();
    $respuesta = $negocio->iniciarSesion($correo, $password);

    if ($respuesta['status'] == true) {
        $_SESSION['id_usuario'] = $respuesta['usuario']['id_usuario'];
        $_SESSION['id_rol']     = $respuesta['usuario']['id_rol'];
        $_SESSION['nombre']     = $respuesta['usuario']['nombre'];
        $_SESSION['nombre_rol'] = $respuesta['usuario']['nombre_rol'];
        
        header("Location: presentacion/dashboard.php");
        exit();
    } else {
        $mensajeError = $respuesta['mensaje'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tecnobyte</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <link rel="stylesheet" href="public/css/login.css">
</head>
<body>

    <div class="container d-flex justify-content-center">
        
        <div class="login-card row g-0">
            
            <div class="col-md-5 d-none d-md-flex flex-column justify-content-center align-items-center login-left text-center">
                
                <img src="public/img/technobyte.svg" alt="Logo Technobyte" class="login-logo">
                
                <h2 class="fw-bold tracking-wide mt-3">TECHNOBYTE</h2>
                <p class="text-white-60 small text-uppercase">Tecnología e innovación a tu alcance</p>
            </div>

            <div class="col-md-7 d-flex justify-content-center align-items-center login-right">
                <div class="w-100" style="max-width: 380px;">
                    
                    <div class="text-center mb-4">
                        <h1 class="fw-bold text-cyan">¡¡Bienvenido!!</h1>
                    </div>

                    <?php if($mensajeError != ''): ?>
                        <div class="alert alert-danger text-center rounded-pill py-2 small fw-bold">
                            <i class="fa-solid fa-triangle-exclamation"></i> <?= $mensajeError ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-4">
                            <label for="correo" class="form-label fw-bold d-flex align-items-center text-secondary">
                                <i class="fa-solid fa-circle-user text-cyan icon-label"></i> Usuario o Correo
                            </label>
                            <input type="email" class="form-control rounded-pill" id="correo" name="correo" required autofocus>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold d-flex align-items-center text-secondary">
                                <i class="fa-solid fa-lock text-cyan icon-label"></i> Contraseña
                            </label>
                            <input type="password" class="form-control rounded-pill" id="password" name="password" required>
                        </div>

                        <div class="text-center mt-5 mb-3">
                            <button type="submit" class="btn bg-cyan text-white fw-bold rounded-pill w-100 py-2 fs-5">
                                Iniciar Sesión
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted mb-1 small">Si olvidaste tu contraseña.</p>
                        <a href="#" class="text-cyan text-decoration-none fw-bold small">Notifica a tu Supervisor</a>
                    </div>

                </div>
            </div>

        </div>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>