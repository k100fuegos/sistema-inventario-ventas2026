<?php
require_once __DIR__ . '/../../../config/control_acceso.php';
requerirLogin();


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
    'direccion_cliente' => '',
    'estado_cliente'    => 1 // Por defecto activo
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
        'direccion_cliente' => $_POST['direccion_cliente'] ?? '',
        'estado_cliente'    => $_POST['estado_cliente'] ?? 1
    ];

    $resultado = $clienteNegocio->crearCliente($datos);

    if ($resultado['exito']) {
        header('Location: listar.php?mensaje=creado');
        exit;
    }

    // Si hubo error de reactivación y se reactivó correctamente, también mandamos éxito
    if (strpos($resultado['mensaje'] ?? '', 'restaurado correctamente') !== false) {
        header('Location: listar.php?mensaje=creado');
        exit;
    }

    $errores = isset($resultado['errores']) ? $resultado['errores'] : [$resultado['mensaje']];
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
                <li><a href="../marcas/listar.php"><i class="fa-solid fa-award"></i> Marcas</a></li>
                <li><a href="../productos/listar.php"><i class="fa-solid fa-cubes"></i> Productos</a></li>
                <li class="active"><a href="listar.php"><i class="fa-solid fa-users"></i> Clientes</a></li>
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
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">
                                        Nombre del Cliente <span class="text-danger">*</span>
                                    </label>

                                    <input type="text"
                                        class="form-control"
                                        name="nombre_cliente"
                                        value="<?php echo mostrarValor($datos['nombre_cliente']); ?>"
                                        required>

                                    <small class="text-muted d-block mt-1 invisible">
                                        <i class="fa-solid fa-circle-info me-1"></i>
                                        Espacio reservado.
                                    </small>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">
                                        Tipo <span class="text-danger">*</span>
                                    </label>

                                    <select class="form-select"
                                        name="tipo_cliente"
                                        id="tipo_cliente"
                                        required>
                                        <option value="PN" <?php echo ($datos['tipo_cliente'] === 'PN') ? 'selected' : ''; ?>>
                                            PN - Persona Natural
                                        </option>
                                        <option value="PJ" <?php echo ($datos['tipo_cliente'] === 'PJ') ? 'selected' : ''; ?>>
                                            PJ - Persona Jurídica
                                        </option>
                                    </select>

                                    <small class="text-muted d-block mt-1 invisible">
                                        <i class="fa-solid fa-circle-info me-1"></i>
                                        Espacio reservado.
                                    </small>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">
                                        Estado <span class="text-danger">*</span>
                                    </label>

                                    <select class="form-select"
                                        name="estado_cliente"
                                        required>
                                        <option value="1" <?php echo ($datos['estado_cliente'] == 1) ? 'selected' : ''; ?>>
                                            Activo
                                        </option>
                                        <option value="0" <?php echo ($datos['estado_cliente'] == 0) ? 'selected' : ''; ?>>
                                            Inactivo
                                        </option>
                                    </select>

                                    <small class="text-muted d-block mt-1">
                                        <i class="fa-solid fa-circle-info me-1"></i>
                                        Los clientes inactivos no podrán realizar ventas.
                                    </small>
                                </div>
                            </div>

                            

                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="form-text text-secondary">
                                        <i class="fa-solid fa-circle-info me-1"></i>
                                        Los documentos requeridos cambiarán según el tipo de cliente.
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold" id="dui_label">DUI</label>
                                    <input type="text" class="form-control" id="dui_input" maxlength="10" name="dui_cliente" placeholder="00000000-0" value="<?php echo mostrarValor($datos['dui_cliente']); ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold" id="nit_label">NIT</label>
                                    <input type="text" class="form-control" id="nit_input" maxlength="17" name="nit_cliente" placeholder="0000-000000-000-0" value="<?php echo mostrarValor($datos['nit_cliente']); ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold" id="nrc_label">NRC</label>
                                    <input type="text" class="form-control" id="nrc_input" maxlength="8" name="nrc_cliente" value="<?php echo mostrarValor($datos['nrc_cliente']); ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Teléfono</label>
                                    <input type="text" class="form-control" name="telefono_cliente" placeholder="0000-0000" value="<?php echo mostrarValor($datos['telefono_cliente']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Correo Electrónico</label>
                                    <input type="email" class="form-control" name="correo_cliente" placeholder="ejemplo@email.com" value="<?php echo mostrarValor($datos['correo_cliente']); ?>">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Dirección</label>
                                <textarea class="form-control" name="direccion_cliente" rows="2" placeholder="Ej. Colonia El Sitio, San Miguel"><?php echo mostrarValor($datos['direccion_cliente']); ?></textarea>
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
    <script src="../../../public/js/main.js?v=<?php echo time(); ?>"></script>

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

                    lblDui.innerHTML = 'DUI <span class="text-danger">*</span>';
                    lblNit.innerHTML = 'NIT <small class="text-secondary">(Opcional)</small>';
                    lblNrc.innerHTML = 'NRC <small class="text-secondary">(No aplica)</small>';

                    dui.disabled = false;
                    if (!dui.value) dui.placeholder = "00000000-0";
                    nit.disabled = false;
                    nrc.disabled = true;
                    nrc.value = "";

                } else {

                    // Persona Jurídica
                    dui.required = false;
                    nit.required = true;
                    nrc.required = true;

                    lblDui.innerHTML = 'DUI <small class="text-secondary">(No aplica)</small>';
                    lblNit.innerHTML = 'NIT <span class="text-danger">*</span>';
                    lblNrc.innerHTML = 'NRC <span class="text-danger">*</span>';

                    dui.disabled = true;
                    dui.value = "";
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