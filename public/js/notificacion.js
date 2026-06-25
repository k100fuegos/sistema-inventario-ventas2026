document.addEventListener("DOMContentLoaded", function () {

    const toastElement = document.getElementById("toastMensaje");

    if (!toastElement) return;

    const mensaje = toastElement.dataset.mensaje;
    const tipo = toastElement.dataset.tipo;

    if (!mensaje) return;

    const icono = document.getElementById("toastIcono");
    const titulo = document.getElementById("toastTitulo");
    const cuerpo = document.getElementById("toastCuerpo");

    cuerpo.textContent = mensaje;

    switch (tipo) {

        case "success":
            toastElement.classList.add("text-bg-success");
            titulo.textContent = "Correcto";
            icono.className = "fa-solid fa-circle-check me-2";
            break;

        case "error":
            toastElement.classList.add("text-bg-danger");
            titulo.textContent = "Error";
            icono.className = "fa-solid fa-circle-xmark me-2";
            break;

        case "warning":
            toastElement.classList.add("text-bg-warning");
            titulo.textContent = "Advertencia";
            icono.className = "fa-solid fa-triangle-exclamation me-2";
            break;

        default:
            toastElement.classList.add("text-bg-primary");
            titulo.textContent = "Información";
            icono.className = "fa-solid fa-circle-info me-2";
    }

    const toast = new bootstrap.Toast(toastElement, {
        delay: 3500
    });

    toast.show();

});