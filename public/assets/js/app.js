/**
 * Interacciones Generales y Componentes de Interfaz - CECAR v3.0 (2026)
 * Satisface:
 *   - Acordeón de checklist documental (Mockup Página 3).
 *   - Filtros interactivos y alternancia de estado vacío (Empty State).
 *   - Píldora de tutorial flotante (Mockup Páginas 1 y 2).
 *   - Manejo de sesión de usuario y cierre de sesión.
 */

document.addEventListener('DOMContentLoaded', () => {
  // =========================================================================
  // 1. ACORDEÓN DE DOCUMENTOS Y CHECKLIST (Mockup Página 3)
  // =========================================================================
  const accordionTriggers = document.querySelectorAll('.accordion-trigger');
  accordionTriggers.forEach(trigger => {
    trigger.addEventListener('click', () => {
      const item = trigger.closest('.accordion-item');
      if (item) {
        item.classList.toggle('open');
        const isOpen = item.classList.contains('open');
        trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      }
    });
  });

  // =========================================================================
  // 2. BUSCADOR INTERACTIVO DE DOCUMENTOS / TABLA
  // =========================================================================
  const searchInput = document.getElementById('search-filtro');
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      const term = e.target.value.toLowerCase().trim();
      const rows = document.querySelectorAll('.data-table tbody tr');
      const emptyState = document.getElementById('table-empty-state');
      let visibleCount = 0;

      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(term)) {
          row.style.display = '';
          visibleCount++;
        } else {
          row.style.display = 'none';
        }
      });

      if (emptyState) {
        emptyState.style.display = visibleCount === 0 ? 'flex' : 'none';
      }
    });
  }

  // =========================================================================
  // 3. PÍLDORA FLOTANTE DE TUTORIAL (Mockup Páginas 1 y 2)
  // =========================================================================
  const tutorialBtn = document.querySelector('.floating-tutorial-pill');
  if (tutorialBtn) {
    tutorialBtn.addEventListener('click', () => {
      alert('Guía Rápida CECAR:\n\n1. Regístrese como candidato con sus datos de filiación.\n2. Revise las convocatorias públicas vigentes.\n3. Cargue sus soportes en PDF y complete su postulación.');
    });
  }

  // =========================================================================
  // 4. SESIÓN Y CONTROL DE CIERRE
  // =========================================================================
  const sessionUser = localStorage.getItem('cecar_user');
  const currentPath = window.location.pathname;

  // Protección de ruta /dashboard en cliente
  if (currentPath.endsWith('/dashboard') && !sessionUser) {
    window.location.href = `${ApiClient.getBaseUrl()}/login`;
    return;
  }

  if (sessionUser) {
    try {
      const user = JSON.parse(sessionUser);
      const userGreetingElem = document.getElementById('user-greeting-name');
      if (userGreetingElem) {
        userGreetingElem.textContent = `¡Bienvenido, ${user.nombre_completo || user.correo}!`;
      }

      const tableNameElem = document.getElementById('user-table-name');
      const tableEmailElem = document.getElementById('user-table-email');
      if (tableNameElem && user.nombre_completo) {
        tableNameElem.textContent = user.nombre_completo;
      }
      if (tableEmailElem && user.correo) {
        tableEmailElem.textContent = user.correo;
      }
    } catch (e) {
      console.warn('Error al leer sesión:', e);
    }
  }

  const postularBtns = document.querySelectorAll('.btn-postular');
  postularBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      if (sessionUser) {
        e.preventDefault();
        alert('Ha seleccionado postularse con su cuenta activa. Redirigiendo a su panel de gestión documental...');
        window.location.href = `${ApiClient.getBaseUrl()}/dashboard`;
      }
    });
  });

  const logoutBtns = document.querySelectorAll('.btn-logout-action');
  logoutBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      localStorage.removeItem('cecar_user');
      localStorage.removeItem('cecar_session_token');
      window.location.href = `${ApiClient.getBaseUrl()}/login`;
    });
  });
});
