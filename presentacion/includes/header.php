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
            <a href="<?= base_url('logout.php') ?>" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
        </div>
    </div>
</nav>
