/**
 * CECAR - Plataforma de Contratación y Selección
 * Scripts de Interfaz y Filtros Dinámicos
 */

document.addEventListener('DOMContentLoaded', function () {
    // Accordion functionality (Image 3)
    const accordionHeaders = document.querySelectorAll('.accordion-header');
    accordionHeaders.forEach(header => {
        header.addEventListener('click', function () {
            const content = this.nextElementSibling;
            const isOpen = content.classList.contains('open');

            // Close siblings if desired or toggle
            this.classList.toggle('active');
            content.classList.toggle('open');
        });
    });

    // Document search inside accordion (Image 3: "Buscar documento...")
    const searchDocInput = document.getElementById('searchDocumentInput');
    if (searchDocInput) {
        searchDocInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            const documentItems = document.querySelectorAll('.document-item-row');

            documentItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(query)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // Filter table by user or status in Contratación (Image 2)
    const searchUserInput = document.getElementById('filterSearchUser');
    if (searchUserInput) {
        searchUserInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            const tableRows = document.querySelectorAll('#contratacionTable tbody tr');

            let visibleCount = 0;
            tableRows.forEach(row => {
                const userCell = row.querySelector('.user-col');
                if (userCell) {
                    const text = userCell.textContent.toLowerCase();
                    if (text.includes(query)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                }
            });

            const emptyState = document.getElementById('tableEmptyState');
            if (emptyState) {
                emptyState.style.display = (visibleCount === 0) ? 'block' : 'none';
            }
        });
    }
});

// Modal helper
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}
