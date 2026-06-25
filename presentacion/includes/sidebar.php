<?php
$current_page = basename($_SERVER['SCRIPT_NAME']);
$current_dir = basename(dirname($_SERVER['SCRIPT_NAME']));
?>
<nav id="sidebar">
<button type="button" id="sidebarClose" class="btn btn-link text-white d-block d-md-none position-absolute top-0 end-0 mt-3 me-2" style="z-index: 1060; text-decoration: none;">
    <i class="fa-solid fa-xmark fs-3"></i>
</button>
    <div class="sidebar-header d-flex align-items-center justify-content-center py-3">
        <img src="<?= base_url('public/img/logo-nav.svg') ?>" alt="Logo" class="img-fluid me-2" style="max-width: 40px;">
        <h4 class="fw-bold mb-0">Technobyte</h4>
    </div>
    
    <ul class="list-unstyled components">
        <li class="<?= ($current_page == 'dashboard.php') ? 'active' : '' ?>"><a href="<?= base_url('presentacion/dashboard.php') ?>"><i class="fa-solid fa-house"></i> Panel Principal</a></li>
        <li class="<?= ($current_page == 'crear.php' && $current_dir == 'ventas') ? 'active' : '' ?>"><a href="<?= base_url('presentacion/admin/ventas/crear.php') ?>"><i class="fa-solid fa-cart-shopping"></i> Nueva Venta</a></li>
        <?php if(tieneRol([ROL_ADMIN, ROL_SUPERVISOR, ROL_VENDEDOR])): ?><li class="<?= ($current_page == 'listar.php' && $current_dir == 'ventas') ? 'active' : '' ?>"><a href="<?= base_url('presentacion/admin/ventas/listar.php') ?>"><i class="fa-solid fa-file-invoice-dollar"></i> Historial Ventas</a></li><?php endif; ?>
        <?php if(tieneRol([ROL_ADMIN, ROL_SUPERVISOR])): ?><li class="<?= ($current_dir == 'categorias') ? 'active' : '' ?>"><a href="<?= base_url('presentacion/admin/categorias/listar.php') ?>"><i class="fa-solid fa-tags"></i> Categorías</a></li><?php endif; ?>
        <?php if(tieneRol([ROL_ADMIN, ROL_SUPERVISOR])): ?><li class="<?= ($current_dir == 'marcas') ? 'active' : '' ?>"><a href="<?= base_url('presentacion/admin/marcas/listar.php') ?>"><i class="fa-solid fa-award"></i> Marcas</a></li><?php endif; ?>
        <li class="<?= ($current_dir == 'productos') ? 'active' : '' ?>"><a href="<?= base_url('presentacion/admin/productos/listar.php') ?>"><i class="fa-solid fa-cubes"></i> Productos</a></li>
        <li class="<?= ($current_dir == 'clientes') ? 'active' : '' ?>"><a href="<?= base_url('presentacion/admin/clientes/listar.php') ?>"><i class="fa-solid fa-users"></i> Clientes</a></li>
        <?php if(tieneRol([ROL_ADMIN])): ?><li class="<?= ($current_dir == 'usuarios' || $current_dir == 'roles') ? 'active' : '' ?>"><a href="<?= base_url('presentacion/admin/usuarios/listar.php') ?>"><i class="fa-solid fa-user-shield"></i> Usuarios</a></li><?php endif; ?>
    </ul>
</nav>
