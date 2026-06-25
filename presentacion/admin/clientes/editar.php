<?php
require_once __DIR__ . '/../../../config/control_acceso.php';
requerirLogin();
requerirRol([ROL_ADMIN, ROL_SUPERVISOR, ROL_VENDEDOR]);


require_once __DIR__ . '/../../../negocio/ClienteNegocio.php';

$clienteNegocio = new ClienteNegocio();
$errores = [];

$id_cliente = $_GET['id'] ?? null;
if (!$id_cliente) {
    header('Location: listar.php');
    exit;
}

$cliente = $clienteNegocio->obtenerClientePorId($id_cliente);
if (!$cliente) {
    header('Location: listar.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id_cliente'        => $id_cliente,
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

    $resultado = $clienteNegocio->actualizarCliente($datos);

    if ($resultado['exito']) {
        header('Location: listar.php?mensaje=actualizado');
        exit;
    }

    $errores = $resultado['errores'];
    $cliente = $datos; // Mantiene los datos tipeados en caso de error
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
    <title>Editar Cliente - Technobyte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/style.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class="d-flex">
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>

        <div id="content">
            <?php include __DIR__ . '/../../includes/header.php'; ?>

            <div class="container-fluid p-4">
                <div class="card shadow-sm border-0 border-top border-warning border-4">
                    <div class="card-header bg-white pt-4 pb-3">
                        <h4 class="fw-bold mb-0 text-warning">Editar Cliente</h4>
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

                        <form action="editar.php?id=<?php echo mostrarValor($cliente['id_cliente']) ?>" method="POST">
                            <input type="hidden" name="id_cliente" value="<?php echo mostrarValor($cliente['id_cliente']); ?>">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Nombre del Cliente <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nombre_cliente" value="<?php echo mostrarValor($cliente['nombre_cliente']); ?>" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Tipo <span class="text-danger">*</span></label>
                                    <select class="form-select" name="tipo_cliente" id="tipo_cliente" required>
                                        <option value="PN" <?php echo ($cliente['tipo_cliente'] === 'PN') ? 'selected' : ''; ?>>PN - Persona Natural</option>
                                        <option value="PJ" <?php echo ($cliente['tipo_cliente'] === 'PJ') ? 'selected' : ''; ?>>PJ - Persona Jurídica</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Estado <span class="text-danger">*</span></label>
                                    <select class="form-select" name="estado_cliente" required>
                                        <option value="1" <?php echo ((int)$cliente['estado_cliente'] === 1) ? 'selected' : ''; ?>>Activo</option>
                                        <option value="0" <?php echo ((int)$cliente['estado_cliente'] === 0) ? 'selected' : ''; ?>>Inactivo</option>
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
                                    <input type="text" class="form-control" id="dui_input" name="dui_cliente" maxlength="10" placeholder="00000000-0" value="<?php echo mostrarValor($cliente['dui_cliente']); ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold" id="nit_label">NIT</label>
                                    <input type="text" class="form-control" id="nit_input" name="nit_cliente" maxlength="17" placeholder="0000-000000-000-0" value="<?php echo mostrarValor($cliente['nit_cliente']); ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold" id="nrc_label">NRC</label>
                                    <input type="text" class="form-control" id="nrc_input" name="nrc_cliente" maxlength="8" value="<?php echo mostrarValor($cliente['nrc_cliente']); ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Teléfono</label>
                                    <input type="text" class="form-control" name="telefono_cliente" placeholder="0000-0000" value="<?php echo mostrarValor($cliente['telefono_cliente']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Correo Electrónico</label>
                                    <input type="email" class="form-control" name="correo_cliente" placeholder="ejemplo@email.com" value="<?php echo mostrarValor($cliente['correo_cliente']); ?>">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Dirección</label>
                                <textarea class="form-control" name="direccion_cliente" rows="2" placeholder="Ej. Colonia El Sitio, San Miguel"><?php echo mostrarValor($cliente['direccion_cliente']); ?></textarea>
                            </div>

                            <hr class="mt-4 mb-4">
                            <div class="text-end">
                                <a href="listar.php" class="btn btn-secondary px-4 fw-bold">Cancelar</a>
                                <button type="submit" class="btn btn-warning text-dark px-4 fw-bold">Actualizar Cliente</button>
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
                    </div>
                </div>
            </nav>

            <div class="container-fluid p-4">
                <div class="card shadow-sm border-0 border-top border-warning border-4">
                    <div class="card-header bg-white pt-4 pb-3">
                        <h4 class="fw-bold mb-0 text-warning">Editar Cliente</h4>
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

                        <form action="editar.php?id=<?php echo mostrarValor($cliente['id_cliente']) ?>" method="POST">
                            <input type="hidden" name="id_cliente" value="<?php echo mostrarValor($cliente['id_cliente']); ?>">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Nombre del Cliente <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nombre_cliente" value="<?php echo mostrarValor($cliente['nombre_cliente']); ?>" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Tipo <span class="text-danger">*</span></label>
                                    <select class="form-select" name="tipo_cliente" id="tipo_cliente" required>
                                        <option value="PN" <?php echo ($cliente['tipo_cliente'] === 'PN') ? 'selected' : ''; ?>>PN - Persona Natural</option>
                                        <option value="PJ" <?php echo ($cliente['tipo_cliente'] === 'PJ') ? 'selected' : ''; ?>>PJ - Persona Jurídica</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold">Estado <span class="text-danger">*</span></label>
                                    <select class="form-select" name="estado_cliente" required>
                                        <option value="1" <?php echo ((int)$cliente['estado_cliente'] === 1) ? 'selected' : ''; ?>>Activo</option>
                                        <option value="0" <?php echo ((int)$cliente['estado_cliente'] === 0) ? 'selected' : ''; ?>>Inactivo</option>
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
                                    <input type="text" class="form-control" id="dui_input" name="dui_cliente" maxlength="10" placeholder="00000000-0" value="<?php echo mostrarValor($cliente['dui_cliente']); ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold" id="nit_label">NIT</label>
                                    <input type="text" class="form-control" id="nit_input" name="nit_cliente" maxlength="17" placeholder="0000-000000-000-0" value="<?php echo mostrarValor($cliente['nit_cliente']); ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold" id="nrc_label">NRC</label>
                                    <input type="text" class="form-control" id="nrc_input" name="nrc_cliente" maxlength="8" value="<?php echo mostrarValor($cliente['nrc_cliente']); ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Teléfono</label>
                                    <input type="text" class="form-control" name="telefono_cliente" placeholder="0000-0000" value="<?php echo mostrarValor($cliente['telefono_cliente']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Correo Electrónico</label>
                                    <input type="email" class="form-control" name="correo_cliente" placeholder="ejemplo@email.com" value="<?php echo mostrarValor($cliente['correo_cliente']); ?>">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Dirección</label>
                                <textarea class="form-control" name="direccion_cliente" rows="2" placeholder="Ej. Colonia El Sitio, San Miguel"><?php echo mostrarValor($cliente['direccion_cliente']); ?></textarea>
                            </div>

                            <hr class="mt-4 mb-4">
                            <div class="text-end">
                                <a href="listar.php" class="btn btn-secondary px-4 fw-bold">Cancelar</a>
                                <button type="submit" class="btn btn-warning text-dark px-4 fw-bold">Actualizar Cliente</button>
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

            // Auto-formato DUI (00000000-0)
            dui.addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 8) {
                    value = value.substring(0, 8) + '-' + value.substring(8, 9);
                }
                e.target.value = value;
            });

            // Auto-formato NIT (0000-000000-000-0)
            nit.addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                let formatted = '';
                if (value.length > 0) formatted += value.substring(0, 4);
                if (value.length > 4) formatted += '-' + value.substring(4, 10);
                if (value.length > 10) formatted += '-' + value.substring(10, 13);
                if (value.length > 13) formatted += '-' + value.substring(13, 14);
                e.target.value = formatted;
            });

            // Auto-formato NRC (000000-0 asumiendo formato común)
            nrc.addEventListener('input', function (e) {
                let value = e.target.value.replace(/[^0-9a-zA-Z]/g, '');
                if (value.length > 6) {
                    value = value.substring(0, 6) + '-' + value.substring(6, 7);
                }
                e.target.value = value;
            });
        });
    </script>
</body>

</html>