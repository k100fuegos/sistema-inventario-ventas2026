document.addEventListener("DOMContentLoaded", function() {
    // Lógica para contraer y expandir el menú lateral
    const toggleButton = document.getElementById("sidebarCollapse");
    const closeButton = document.getElementById("sidebarClose");
    const sidebar = document.getElementById("sidebar");

    if(toggleButton && sidebar) {
        toggleButton.addEventListener("click", function() {
            sidebar.classList.toggle("active");
        });
    }

    if(closeButton && sidebar) {
        closeButton.addEventListener("click", function() {
            sidebar.classList.remove("active");
        });
    }

    // Buscador en tiempo real del lado del cliente
    const searchInput = document.querySelector('input[name*="buscar"]');
    const table = document.querySelector('table');

    if (searchInput && table) {
        const tbody = table.querySelector('tbody');
        const form = searchInput.closest('form');

        // Evitar que el formulario se envíe y recargue la página
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
            });
        }

        // Lógica para el botón de reiniciar
        const resetBtn = form ? form.querySelector('.btn-reset-search') : null;
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                searchInput.value = '';
                
                // Verificar si hay parámetros de búsqueda en la URL
                const urlParams = new URLSearchParams(window.location.search);
                let hasSearchParam = false;
                for (const key of urlParams.keys()) {
                    if (key.includes('buscar')) {
                        hasSearchParam = true;
                        break;
                    }
                }

                if (hasSearchParam) {
                    // Si se cargó desde el backend, recargamos la página limpia
                    window.location.href = window.location.pathname;
                } else {
                    // Si es tiempo real, simplemente disparamos el input event para limpiar la tabla
                    searchInput.dispatchEvent(new Event('input'));
                }
            });
        }

        searchInput.addEventListener('input', function() {
            const query = searchInput.value.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").trim();
            const rows = tbody.querySelectorAll('tr');
            let hasResults = false;

            rows.forEach(row => {
                // Ignorar e ignorar/remover la fila de "no hay resultados" previa
                if (row.classList.contains('no-results-row') || row.querySelector('td[colspan]')) {
                    row.style.display = 'none';
                    return;
                }

                // Normalizar texto para ignorar acentos y mayúsculas
                const text = row.textContent.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");

                if (text.includes(query)) {
                    row.style.display = '';
                    hasResults = true;
                } else {
                    row.style.display = 'none';
                }
            });

            // Mostrar mensaje dinámico si no hay resultados
            let noResultsRow = tbody.querySelector('.no-results-row');
            if (!hasResults && query !== '') {
                if (!noResultsRow) {
                    const colCount = table.querySelectorAll('thead th').length || 10;
                    noResultsRow = document.createElement('tr');
                    noResultsRow.className = 'no-results-row';
                    noResultsRow.innerHTML = `<td colspan="${colCount}" class="text-center py-3 text-muted">No se encontraron registros</td>`;
                    tbody.appendChild(noResultsRow);
                } else {
                    noResultsRow.style.display = '';
                }
            } else {
                if (noResultsRow) {
                    noResultsRow.style.display = 'none';
                }
                // Si la consulta está vacía y originalmente no había registros
                if (query === '' && rows.length > 0) {
                    let onlyColspan = true;
                    rows.forEach(row => {
                        if (!row.querySelector('td[colspan]')) {
                            row.style.display = '';
                            onlyColspan = false;
                        }
                    });
                    if (onlyColspan) {
                        // Si solo hay fila con colspan, mostrarla
                        rows.forEach(row => {
                            if (row.querySelector('td[colspan]')) row.style.display = '';
                        });
                    }
                }
            }
        });

        // Trigger inicial en caso de que ya haya un valor cargado por GET
        if (searchInput.value) {
            searchInput.dispatchEvent(new Event('input'));
        }
    }
});