<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 404 - No encontrado</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-card {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 50px 40px;
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        .error-icon {
            font-size: 80px;
            color: #ff4757;
            margin-bottom: 20px;
        }
        .error-title {
            font-size: 28px;
            font-weight: 700;
            color: #2f3542;
            margin-bottom: 15px;
        }
        .error-message {
            color: #57606f;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .btn-home {
            background-color: #00bcd4;
            color: #ffffff;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .btn-home:hover {
            background-color: #0097a7;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 188, 212, 0.4);
            color: white;
        }
    </style>
</head>
<body>

    <div class="error-card">
        <i class="fa-solid fa-triangle-exclamation error-icon"></i>
        <h1 class="error-title">Error 404</h1>
        <p class="error-message">
            Ups... Ha ocurrido un problema con la base de datos o el recurso que intentas buscar no se encuentra disponible.
        </p>
        <!-- Retorna a la página principal, asumiendo que index.php o login.php están en la raíz del dominio relativo -->
        <a href="javascript:history.back()" class="btn-home">
            <i class="fa-solid fa-arrow-left me-2"></i> Volver Atrás
        </a>
    </div>

</body>
</html>
