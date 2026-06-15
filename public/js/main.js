document.addEventListener("DOMContentLoaded", function() {
    // Lógica para contraer y expandir el menú lateral
    const toggleButton = document.getElementById("sidebarCollapse");
    const sidebar = document.getElementById("sidebar");

    if(toggleButton && sidebar) {
        toggleButton.addEventListener("click", function() {
            sidebar.classList.toggle("active");
        });
    }
});