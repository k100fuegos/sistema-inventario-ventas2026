<?php
require_once '../../../negocio/VentaNegocio.php';
require_once '../../../negocio/ClienteNegocio.php';
require_once '../../../negocio/UsuarioNegocio.php';
require_once '../../../negocio/ProductoNegocio.php';

$ventaNegocio = new VentaNegocio();
$clienteNegocio = new ClienteNegocio();
$usuarioNegocio = new UsuarioNegocio();
$productoNegocio = new ProductoNegocio();

$clientes = $clienteNegocio->listarClientes();
$usuarios = $usuarioNegocio->listarUsuarios();
$productos = $productoNegocio->listarProductosActivos();

$mensajeError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datosFormulario = [
        'fecha_venta' => $_POST['fecha_venta'] ?? date('Y-m-d\TH:i'),
        'id_cliente' => $_POST['id_cliente'] ?? null,
        'id_usuario' => $_POST['id_usuario'] ?? null,
        'estado_venta' => $_POST['estado_venta'] ?? 'Realizada'
    ];

    $productosSeleccionados = [];
    if (isset($_POST['productos']) && is_array($_POST['productos'])) {
        foreach ($_POST['productos'] as $index => $idProducto) {
            $cantidad = isset($_POST['cantidades'][$index]) ? (int)$_POST['cantidades'][$index] : 0;
            if ($cantidad > 0) {
                $productosSeleccionados[] = [
                    'id_producto' => $idProducto,
                    'cantidad' => $cantidad
                ];
            }
        }
    }

    $resultado = $ventaNegocio->crearVenta($datosFormulario, $productosSeleccionados);

    if ($resultado['exito']) {
        header("Location: listar.php?mensaje=creado");
        exit;
    } else {
        $mensajeError = is_array($resultado['errores']) ? implode(", ", $resultado['errores']) : $resultado['mensaje'];
    }
}

function mostrarValor($valor) {
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Venta - Tecnobyte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/style.css?v=<?php echo time(); ?>">
    <style>
        .carrito-container {
            max-height: 400px;
            overflow-y: auto;
        }
        .select-list {
            height: 120px !important;
            overflow-y: auto;
        }
    </style>
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
                <li class="active"><a href="crear.php"><i class="fa-solid fa-cart-shopping"></i> Nueva Venta</a></li>
                <li><a href="listar.php"><i class="fa-solid fa-file-invoice-dollar"></i> Historial Ventas</a></li>
                <li><a href="../categorias/listar.php"><i class="fa-solid fa-tags"></i> Categorías</a></li>
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
                        <span class="me-3 fw-bold"><i class="fa-solid fa-circle-user"></i> LÓGICA PHP</span>
                        <a href="../../../logout.php" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
                    </div>
                </div>
            </nav>

             <div class="container-fluid p-4">
                 <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-dark fw-bold"><i class="fa-solid fa-cart-plus" style="color: var(--color-secundario);"></i> Registrar Nueva Venta</h2>
                    <a href="listar.php" class="btn btn-secondary fw-bold"><i class="fa-solid fa-arrow-left"></i> Volver al Historial</a>
                 </div>

                 <?php if (!empty($mensajeError)): ?>
                     <div class="alert alert-danger shadow-sm fw-bold">
                         <i class="fa-solid fa-triangle-exclamation"></i> <?php echo mostrarValor($mensajeError); ?>
                     </div>
                 <?php endif; ?>

                 <form action="" method="POST" id="formNuevaVenta" onsubmit="return validarAntesDeEnviar()">
                     
                     <!-- Panel Superior (Ancho Completo): Datos de Facturación -->
                     <div class="card shadow-sm border-0 mb-4">
                         <div class="card-header text-white fw-bold" style="background-color: var(--color-primario);">
                             <i class="fa-solid fa-user-tag"></i> Datos de Facturación
                         </div>
                         <div class="card-body py-2">
                             <div class="row align-items-start">
                                 <!-- Fecha y Estado -->
                                 <div class="col-md-2 mb-2">
                                     <label class="form-label fw-bold mb-1">Fecha de Venta</label>
                                     <div class="form-control-plaintext text-muted fw-bold">
                                         <i class="fa-regular fa-calendar"></i> <?php echo date('d/m/Y h:i A'); ?>
                                     </div>
                                     <input type="hidden" name="fecha_venta" value="<?php echo date('Y-m-d\TH:i'); ?>">
                                     
                                     <label for="estado_venta" class="form-label fw-bold mb-1 mt-2">Estado</label>
                                     <select class="form-select form-select-sm" id="estado_venta" name="estado_venta" required>
                                         <option value="Realizada" selected>Realizada</option>
                                         <option value="Pendiente">Pendiente</option>
                                     </select>
                                 </div>

                                 <!-- Cliente -->
                                 <div class="col-md-5 mb-2">
                                     <label class="form-label fw-bold d-flex justify-content-between align-items-center mb-1">
                                         <span>Cliente <span class="text-danger">*</span></span>
                                         <a href="../clientes/crear.php" class="btn btn-sm btn-outline-primary py-0 px-2" tabindex="-1">
                                             <i class="fa-solid fa-plus"></i> Nuevo
                                         </a>
                                     </label>
                                     <div class="input-group mb-2">
                                         <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass" style="color: var(--color-secundario);"></i></span>
                                         <input type="text" id="buscar_cliente" class="form-control" placeholder="Escribe el nombre, DUI, NIT o NRC para filtrar..." onkeyup="filtrarClientes()">
                                     </div>
                                     <select class="form-select select-list bg-light" id="id_cliente" name="id_cliente" size="4" required>
                                         <!-- Opciones inyectadas por JS -->
                                     </select>
                                 </div>

                                 <!-- Vendedor -->
                                 <div class="col-md-5 mb-2">
                                     <label class="form-label fw-bold">Vendedor <span class="text-danger">*</span></label>
                                     <div class="input-group mb-2">
                                         <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass" style="color: var(--color-secundario);"></i></span>
                                         <input type="text" id="buscar_vendedor" class="form-control" placeholder="Escribe el nombre del vendedor para filtrar..." onkeyup="filtrarVendedores()">
                                     </div>
                                     <select class="form-select select-list bg-light" id="id_usuario" name="id_usuario" size="4" required>
                                         <!-- Opciones inyectadas por JS -->
                                     </select>
                                 </div>
                             </div>
                         </div>
                     </div>

                     <!-- Panel Inferior: Buscador de Productos y Carrito -->
                     <div class="row">
                         <!-- Columna Izquierda: Buscador de Productos -->
                         <div class="col-md-4 mb-4">
                             <div class="card shadow-sm border-0">
                                 <div class="card-header text-white fw-bold" style="background-color: var(--color-secundario);">
                                     <i class="fa-solid fa-magnifying-glass"></i> Agregar Producto
                                 </div>
                                 <div class="card-body">
                                     <div class="mb-3">
                                         <label class="form-label fw-bold">Buscar Producto</label>
                                         <input type="text" id="buscar_producto" class="form-control mb-2" placeholder="Escribe nombre o código..." onkeyup="filtrarProductos()">
                                         <select class="form-select select-list bg-light" id="selector_producto" size="8">
                                             <!-- Opciones inyectadas por JS -->
                                         </select>
                                     </div>
                                     <div class="d-grid">
                                         <button type="button" class="btn fw-bold text-white shadow-sm" style="background-color: var(--color-primario);" onclick="agregarAlCarrito()">
                                             <i class="fa-solid fa-plus"></i> Añadir al Carrito
                                         </button>
                                     </div>
                                 </div>
                             </div>
                         </div>

                         <!-- Columna Derecha: Carrito de Compras -->
                         <div class="col-md-8">
                             <div class="card shadow-sm border-0 h-100">
                                 <div class="card-header text-white fw-bold" style="background-color: var(--color-primario);">
                                     <i class="fa-solid fa-cart-shopping"></i> Detalles del Carrito
                                 </div>
                                 
                                 <div class="card-body d-flex flex-column">
                                     <div class="carrito-container flex-grow-1 border rounded mb-3">
                                         <table class="table table-hover table-striped mb-0 text-center align-middle" id="tablaCarrito">
                                             <thead class="table-dark sticky-top">
                                                 <tr>
                                                     <th>Código</th>
                                                     <th class="text-start">Producto</th>
                                                     <th>Precio U.</th>
                                                     <th style="width: 120px;">Cantidad</th>
                                                     <th>Subtotal</th>
                                                     <th>Acción</th>
                                                 </tr>
                                             </thead>
                                             <tbody id="cuerpoCarrito">
                                                 <tr>
                                                     <td colspan="6" class="text-center py-4 text-muted">El carrito está vacío. Agregue productos para comenzar.</td>
                                                 </tr>
                                             </tbody>
                                         </table>
                                     </div>

                                     <!-- Contenedor dinámico para inputs ocultos (se genera al hacer submit) -->
                                     <div id="inputsOcultosCarrito"></div>

                                     <!-- Resumen Financiero -->
                                     <div class="card bg-light border-0">
                                         <div class="card-body">
                                             <div class="row text-end align-items-center">
                                                 <div class="col-8 text-muted fw-bold">Subtotal Venta:</div>
                                                 <div class="col-4 fw-bold fs-5" id="lblSubtotal">$ 0.00</div>
                                                 
                                                 <div class="col-8 text-muted fw-bold">IVA (13%):</div>
                                                 <div class="col-4 fw-bold text-danger fs-5" id="lblIva">+ $ 0.00</div>
                                                 
                                                 <div class="col-8 text-dark fw-bold fs-4 mt-2">Total a Pagar:</div>
                                                 <div class="col-4 text-success fw-bold fs-3 mt-2" id="lblTotal">$ 0.00</div>
                                             </div>
                                         </div>
                                     </div>

                                     <div class="d-grid mt-4">
                                         <button type="submit" class="btn btn-lg text-white fw-bold shadow" style="background-color: var(--color-secundario);">
                                             <i class="fa-solid fa-check-circle"></i> Procesar Factura
                                         </button>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </form>
             </div>
        </div>
    </div>

    <!-- Carga de datos iniciales en variables JS -->
    <script>
        let bdClientes = <?php echo json_encode($clientes); ?>;
        const bdUsuarios = <?php echo json_encode($usuarios); ?>;
        const bdProductos = <?php echo json_encode($productos); ?>;
        let carrito = [];

        // Funciones de renderizado y filtrado nativo
        function renderizarLista(elementId, datos, valueField, renderTextFunc, selectedId = null, emptyMessage = 'No se encontraron resultados...') {
            const select = document.getElementById(elementId);
            const actualSelected = selectedId || select.value;
            select.innerHTML = '';
            
            if (datos.length === 0) {
                const opt = document.createElement('option');
                opt.value = '';
                opt.text = emptyMessage;
                opt.disabled = true;
                opt.selected = true;
                select.appendChild(opt);
                return;
            }

            datos.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item[valueField];
                opt.text = renderTextFunc(item);
                if (actualSelected == item[valueField]) {
                    opt.selected = true;
                }
                select.appendChild(opt);
            });
        }

        function textoLimpiado(str) {
            if (!str) return '';
            return str.toString().toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        }

        // Filtro de Clientes
        function filtrarClientes() {
            const termino = textoLimpiado(document.getElementById('buscar_cliente').value);
            if (termino === '') {
                renderizarLista('id_cliente', [], 'id_cliente', null, null, 'Escriba para buscar clientes...');
                return;
            }
            const filtrados = bdClientes.filter(c => {
                const nombre = textoLimpiado(c.nombre_cliente);
                const dui = textoLimpiado(c.dui_cliente);
                const nit = textoLimpiado(c.nit_cliente);
                const nrc = textoLimpiado(c.nrc_cliente);
                return nombre.includes(termino) || dui.includes(termino) || nit.includes(termino) || nrc.includes(termino);
            });
            renderizarLista('id_cliente', filtrados, 'id_cliente', c => {
                const docs = [c.dui_cliente, c.nit_cliente, c.nrc_cliente].filter(Boolean).join(' / ');
                return `${c.nombre_cliente} ${docs ? ' - [' + docs + ']' : ''}`;
            });
        }

        // Filtro de Vendedores
        function filtrarVendedores() {
            const termino = textoLimpiado(document.getElementById('buscar_vendedor').value);
            if (termino === '') {
                renderizarLista('id_usuario', [], 'id_usuario', null, null, 'Escriba para buscar vendedores...');
                return;
            }
            const filtrados = bdUsuarios.filter(u => textoLimpiado(u.nombre_usuario).includes(termino));
            renderizarLista('id_usuario', filtrados, 'id_usuario', u => u.nombre_usuario);
        }

        // Filtro de Productos
        function filtrarProductos() {
            const termino = textoLimpiado(document.getElementById('buscar_producto').value);
            if (termino === '') {
                renderizarLista('selector_producto', [], 'id_producto', null, null, 'Escriba para buscar productos...');
                return;
            }
            const filtrados = bdProductos.filter(p => {
                const nombre = textoLimpiado(p.nombre_producto);
                const codigo = textoLimpiado(p.codigo_producto);
                return nombre.includes(termino) || codigo.includes(termino);
            });
            renderizarLista('selector_producto', filtrados, 'id_producto', p => {
                return `[${p.codigo_producto}] ${p.nombre_producto} - $${parseFloat(p.precio_producto).toFixed(2)}`;
            });
        }

        // Inicializar listas al cargar
        document.addEventListener("DOMContentLoaded", function() {
            filtrarClientes();
            filtrarVendedores();
            filtrarProductos();
        });

        // Lógica del Carrito
        function agregarAlCarrito() {
            const selector = document.getElementById('selector_producto');
            const idProducto = selector.value;

            if (!idProducto) {
                alert("Por favor, seleccione un producto de la lista.");
                return;
            }

            const producto = bdProductos.find(p => p.id_producto == idProducto);
            if (!producto) return;

            const indexExistente = carrito.findIndex(item => item.id_producto == idProducto);

            if (indexExistente !== -1) {
                if (carrito[indexExistente].cantidad < producto.stock_producto) {
                    carrito[indexExistente].cantidad++;
                } else {
                    alert(`No hay más stock disponible de ${producto.nombre_producto}. (Máx: ${producto.stock_producto})`);
                }
            } else {
                carrito.push({
                    id_producto: producto.id_producto,
                    codigo: producto.codigo_producto,
                    nombre: producto.nombre_producto,
                    precio: parseFloat(producto.precio_producto),
                    cantidad: 1,
                    stock_max: parseInt(producto.stock_producto)
                });
            }
            renderizarCarrito();
        }

        function cambiarCantidad(index, nuevaCantidad) {
            const item = carrito[index];
            nuevaCantidad = parseInt(nuevaCantidad);

            if (isNaN(nuevaCantidad) || nuevaCantidad <= 0) {
                nuevaCantidad = 1;
            }

            if (nuevaCantidad > item.stock_max) {
                alert(`Stock insuficiente. El stock máximo es ${item.stock_max}.`);
                nuevaCantidad = item.stock_max;
            }

            carrito[index].cantidad = nuevaCantidad;
            renderizarCarrito();
        }

        function eliminarDelCarrito(index) {
            carrito.splice(index, 1);
            renderizarCarrito();
        }

        function renderizarCarrito() {
            const tbody = document.getElementById('cuerpoCarrito');
            tbody.innerHTML = '';

            if (carrito.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-muted">El carrito está vacío. Agregue productos para comenzar.</td></tr>`;
                actualizarTotales(0);
                return;
            }

            let subtotalGlobal = 0;

            carrito.forEach((item, index) => {
                const subtotalItem = item.precio * item.cantidad;
                subtotalGlobal += subtotalItem;

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="text-muted fw-bold">${item.codigo}</td>
                    <td class="text-start">${item.nombre}</td>
                    <td>$ ${item.precio.toFixed(2)}</td>
                    <td>
                        <input type="number" class="form-control form-control-sm text-center" 
                            value="${item.cantidad}" min="1" max="${item.stock_max}"
                            onchange="cambiarCantidad(${index}, this.value)">
                    </td>
                    <td class="fw-bold">$ ${subtotalItem.toFixed(2)}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarDelCarrito(${index})" title="Quitar producto">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            actualizarTotales(subtotalGlobal);
        }

        function actualizarTotales(subtotal) {
            const iva = subtotal * 0.13;
            const total = subtotal + iva;

            document.getElementById('lblSubtotal').innerText = `$ ${subtotal.toFixed(2)}`;
            document.getElementById('lblIva').innerText = `+ $ ${iva.toFixed(2)}`;
            document.getElementById('lblTotal').innerText = `$ ${total.toFixed(2)}`;
        }

        function validarAntesDeEnviar() {
            if (carrito.length === 0) {
                alert("Debe agregar al menos un producto al carrito para procesar la venta.");
                return false;
            }

            const cliente = document.getElementById('id_cliente').value;
            const vendedor = document.getElementById('id_usuario').value;

            if (!cliente) {
                alert("Por favor, seleccione un cliente de la lista.");
                return false;
            }
            if (!vendedor) {
                alert("Por favor, seleccione un vendedor de la lista.");
                return false;
            }

            const contenedorInputs = document.getElementById('inputsOcultosCarrito');
            contenedorInputs.innerHTML = ''; 

            carrito.forEach((item) => {
                const inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'productos[]';
                inputId.value = item.id_producto;

                const inputCant = document.createElement('input');
                inputCant.type = 'hidden';
                inputCant.name = 'cantidades[]';
                inputCant.value = item.cantidad;

                contenedorInputs.appendChild(inputId);
                contenedorInputs.appendChild(inputCant);
            });

            return true;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../public/js/main.js"></script>
</body>
</html>