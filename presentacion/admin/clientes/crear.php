<?php

require_once __DIR__ . '/../../../negocio/ClienteNegocio.php';

$clienteNegocio = new ClienteNegocio();
$errores = [];

$datos = [
    'nombre_cliente'    => '',
    'tipo_cliente'      => 'PN',
    'dui_cliente'       => '',
    'nit_cliente'       => '',
    'nrc_cliente'       => '',
    'telefono_cliente'  => '',
    'correo_cliente'    => '',
    'direccion_cliente' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre_cliente'    => $_POST['nombre_cliente'] ?? '',
        'tipo_cliente'      => $_POST['tipo_cliente'] ?? 'PN',
        'dui_cliente'       => $_POST['dui_cliente'] ?? '',
        'nit_cliente'       => $_POST['nit_cliente'] ?? '',
        'nrc_cliente'       => $_POST['nrc_cliente'] ?? '',
        'telefono_cliente'  => $_POST['telefono_cliente'] ?? '',
        'correo_cliente'    => $_POST['correo_cliente'] ?? '',
        'direccion_cliente' => $_POST['direccion_cliente'] ?? ''
    ];

    $resultado = $clienteNegocio->crearCliente($datos);

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
    <title>Nuevo Cliente - Technobyte</title>
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
                <li><a href="../productos/listar.php"><i class="fa-solid fa-cubes"></i> Productos</a></li>
                <li class="active"><a href="listar.php"><i class="fa-solid fa-users"></i> Clientes</a></li>
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
                <div class="card shadow-sm border-0 border-top border-primary border-4">
                    <div class="card-header bg-white pt-4 pb-3">
                        <h4 class="fw-bold mb-0 text-primary">Registrar Nuevo Cliente</h4>
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
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label fw-bold">Nombre del Cliente <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nombre_cliente" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Tipo <span class="text-danger">*</span></label>
                                    <select class="form-select" name="tipo_cliente" id="tipo_cliente" required>
                                        <option value="PN">PN - Persona Natural</option>
                                        <option value="PJ">PJ - Persona Jurídica</option>
                                    </select>
                                    <div class="form-text text-secondary">
                                        <i class="fa-solid fa-circle-info me-1"></i>
                                        Los documentos requeridos cambiarán según el tipo.
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold" id="dui_label">DUI</label>
                                    <input type="text" class="form-control" id="dui_input" name="dui_cliente" placeholder="00000000-0">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold" id="nit_label">NIT</label>
                                    <input type="text" class="form-control" id="nit_input" name="nit_cliente" placeholder="0000-000000-000-0">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold" id="nrc_label">NRC</label>
                                    <input type="text" class="form-control" id="nrc_input" name="nrc_cliente">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Teléfono</label>
                                    <input type="text" class="form-control" name="telefono_cliente" placeholder="0000-0000">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Correo Electrónico</label>
                                    <input type="email" class="form-control" name="correo_cliente" placeholder="ejemplo@email.com">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Dirección</label>
                                <textarea class="form-control" name="direccion_cliente" rows="2" placeholder="Ej. Colonia El Sitio, San Miguel"></textarea>
                            </div>

                            <hr class="mt-4 mb-4">
                            <div class="text-end">
                                <a href="listar.php" class="btn btn-secondary px-4 fw-bold">Cancelar</a>
                                <button type="submit" class="btn btn-primary px-4 fw-bold">Guardar Cliente</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../public/js/main.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const tipo = document.getElementById("tipo_cliente");

            const dui = document.getElementById("dui_input");
            const nit = document.getElementById("nit_input");
            const nrc = document.getElementById("nrc_input");

            const lblDui = document.getElementById("dui_label");
            const lblNit = document.getElementById("nit_label");
            const lblNrc = document.getElementById("nrc_label");

            function actualizarFormulario() {

                if (tipo.value === "PN") {

                    // Persona Natural

                    dui.required = true;
                    nit.required = false;
                    nrc.required = false;

                    lblDui.innerHTML =
                        'DUI <span class="text-danger">*</span>';

                    lblNit.innerHTML =
                        'NIT <small class="text-secondary">(Opcional)</small>';

                    lblNrc.innerHTML =
                        'NRC <small class="text-secondary">(No aplica)</small>';

                    dui.disabled = false;
                    dui.placeholder = "00000000-0";
                    nit.disabled = false;
                    nrc.disabled = true;

                } else {

                    // Persona Jurídica

                    dui.required = false;
                    nit.required = true;
                    nrc.required = true;

                    lblDui.innerHTML =
                        'DUI <small class="text-secondary">(No aplica)</small>';

                    lblNit.innerHTML =
                        'NIT <span class="text-danger">*</span>';

                    lblNrc.innerHTML =
                        'NRC <span class="text-danger">*</span>';

                    dui.disabled = true;
                    dui.placeholder = "";
                    nit.disabled = false;
                    nrc.disabled = false;
                }
            }

            actualizarFormulario();

            tipo.addEventListener("change", actualizarFormulario);

        });
    </script>
</body>

</html>