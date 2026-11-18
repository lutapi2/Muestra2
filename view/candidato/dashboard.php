<?php
  $pageTitle = "Panel del Candidato - Sistema de Selección CECAR";
  require_once __DIR__ . '/../layout/header.php';
  $baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
  if ($baseUrl === '/' || $baseUrl === '\\') $baseUrl = '';
?>

<div class="app-layout">
  <!-- 1. Barra Lateral estilo CECAR observada en Mockup Página 1 y 2 -->
  <aside class="sidebar" aria-label="Navegación lateral de administración">
    <div class="sidebar-brand">
      <div style="width: 32px; height: 32px; border-radius: 6px; background: var(--color-accent-emerald); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; color: #062816;">
        C
      </div>
      <span>CECAR Selección</span>
    </div>

    <nav class="sidebar-nav">
      <a href="<?= $baseUrl ?>/dashboard" class="nav-link-item active">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <rect x="3" y="3" width="7" height="7"></rect>
          <rect x="14" y="3" width="7" height="7"></rect>
          <rect x="14" y="14" width="7" height="7"></rect>
          <rect x="3" y="14" width="7" height="7"></rect>
        </svg>
        <span>Dashboard</span>
      </a>

      <a href="<?= $baseUrl ?>/convocatorias" class="nav-link-item">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
          <polyline points="14 2 14 8 20 8"></polyline>
          <line x1="16" y1="13" x2="8" y2="13"></line>
          <line x1="16" y1="17" x2="8" y2="17"></line>
          <polyline points="10 9 9 9 8 9"></polyline>
        </svg>
        <span>Procesos de selección</span>
      </a>

      <a href="#" class="nav-link-item" onclick="alert('Mesa de Ayudas CECAR:\nSoporte técnico y solicitudes de subsanación documental.')">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="12" cy="12" r="10"></circle>
          <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
          <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>
        <span>Mesa de Ayudas</span>
      </a>
    </nav>

    <div class="sidebar-footer">
      <a href="#" class="nav-link-item btn-logout-action" style="color: #f87171;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
          <polyline points="16 17 21 12 16 7"></polyline>
          <line x1="21" y1="12" x2="9" y2="12"></line>
        </svg>
        <span>Cerrar Sesión</span>
      </a>
    </div>
  </aside>

  <!-- 2. Área Principal de Contenido -->
  <main id="main-content" class="app-main">
    <div class="main-content-padded">

      <!-- Banner Degradado de Bienvenida (Mockup Página 1 y 2) -->
      <section class="welcome-banner" aria-label="Bienvenida al usuario">
        <div class="welcome-info">
          <h1 id="user-greeting-name">¡Bienvenido al Sistema de Gestión!</h1>
          <p>Accede a todos los módulos desde el menú lateral. Tu sesión está activa y protegida con BCRYPT (RNF-009).</p>
          
          <div class="welcome-chips" aria-label="Metadatos institucionales">
            <span class="welcome-chip">EMPRESA: CECAR</span>
            <span class="welcome-chip">REGIÓN: CARIBE</span>
            <span class="welcome-chip">SEDE: SINCELEJO</span>
          </div>
        </div>

        <div class="welcome-avatar" aria-hidden="true">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
          </svg>
        </div>
      </section>

      <!-- Fila de Resumen de KPIs (Mockup Páginas 2 y 3) -->
      <section class="kpi-row" aria-label="Indicadores del proceso de selección">
        <div class="kpi-card">
          <div>
            <span class="kpi-number">1</span>
            <span class="kpi-label">Usuario</span>
          </div>
          <div class="kpi-icon-circle green" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
          </div>
        </div>

        <div class="kpi-card">
          <div>
            <span class="kpi-number">25</span>
            <span class="kpi-label">Documentos</span>
          </div>
          <div class="kpi-icon-circle green" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path></svg>
          </div>
        </div>

        <div class="kpi-card">
          <div>
            <span class="kpi-number">25</span>
            <span class="kpi-label">Aprobados</span>
          </div>
          <div class="kpi-icon-circle green" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
          </div>
        </div>

        <div class="kpi-card">
          <div>
            <span class="kpi-number">0</span>
            <span class="kpi-label">Pendientes</span>
          </div>
          <div class="kpi-icon-circle amber" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
          </div>
        </div>

        <div class="kpi-card">
          <div>
            <span class="kpi-number">0</span>
            <span class="kpi-label">Rechazados</span>
          </div>
          <div class="kpi-icon-circle red" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
          </div>
        </div>

        <div class="kpi-card">
          <div>
            <span class="kpi-number">0</span>
            <span class="kpi-label">Subsanados</span>
          </div>
          <div class="kpi-icon-circle blue" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
          </div>
        </div>
      </section>

      <!-- Disposición en 2 Columnas: Tabla y Acordeón Documental (Mockup Páginas 2 y 3) -->
      <div style="display: grid; grid-template-columns: 2fr 1.2fr; gap: var(--space-6); align-items: start;">
        
        <!-- Columna Izquierda: Tabla con barra de filtros -->
        <section class="table-card" aria-label="Tabla de procesos de selección">
          <div class="table-card-header">
            <div class="table-card-title">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
              <span>Mis Procesos de Selección</span>
              <span class="badge-count">1</span>
            </div>
          </div>

          <!-- Barra de Filtros interactiva (Mockup Página 2) -->
          <div class="filter-toolbar">
            <input type="text" id="search-filtro" class="search-input" placeholder="Buscar convocatoria o facultad...">
            <select class="filter-select" aria-label="Filtrar por estado">
              <option value="">Todos los estados</option>
              <option value="Habilitada">Habilitada</option>
              <option value="En revisión">En revisión</option>
              <option value="Contratado">Contratado</option>
            </select>
          </div>

          <!-- Tabla de Datos -->
          <div class="data-table-wrapper">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Usuario</th>
                  <th>Proceso</th>
                  <th>Cargados</th>
                  <th>Aprobados</th>
                  <th>Pendientes</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <strong id="user-table-name">Aspirante Demo</strong><br>
                    <span id="user-table-email" style="font-size: 0.75rem; color: var(--color-text-muted);">aspirante@correo.edu.co</span>
                  </td>
                  <td>
                    <strong>Docente Cátedra Ingeniería</strong><br>
                    <span style="font-size: 0.75rem; color: var(--color-text-muted);">Facultad de Ciencias Básicas e Ingeniería</span><br>
                    <span class="badge-status badge-approved" style="margin-top: 4px;">Activo</span>
                  </td>
                  <td><span class="badge-status badge-approved">22/23</span></td>
                  <td><span class="badge-status badge-approved">22</span></td>
                  <td><span class="badge-status badge-process">0</span></td>
                  <td>
                    <button type="button" class="btn-pill btn-primary-pill" style="padding: 4px 10px; font-size: 0.75rem;" onclick="alert('Abriendo expediente documental del aspirante...')">
                      Ver Ficha
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Estado Vacío (Mockup Página 3) -->
            <div id="table-empty-state" class="empty-state" style="display: none;">
              <svg class="empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                <line x1="8" y1="11" x2="14" y2="11"></line>
              </svg>
              <h3 class="empty-state-title">No hay registros</h3>
              <p class="empty-state-desc">No se encontraron registros con los filtros aplicados en el sistema.</p>
            </div>
          </div>
        </section>

        <!-- Columna Derecha: Acordeón de Expediente y Checklist (Mockup Página 3) -->
        <section class="table-card" aria-label="Checklist de expediente de postulación">
          <div class="table-card-header">
            <div class="table-card-title">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
              <span>Expediente de Documentos</span>
            </div>
          </div>

          <div style="padding: var(--space-4);">
            <div class="accordion-group">
              
              <!-- Categoría 1: Contrato -->
              <div class="accordion-item open">
                <button type="button" class="accordion-trigger" aria-expanded="true">
                  <span>✔ Contrato</span>
                  <span class="badge-count">1/1</span>
                </button>
                <div class="accordion-content">
                  <div class="checklist-item valid">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span>Minuta de Contrato Firmada Digitalmente *</span>
                  </div>
                </div>
              </div>

              <!-- Categoría 2: Información Personal -->
              <div class="accordion-item">
                <button type="button" class="accordion-trigger" aria-expanded="false">
                  <span>Información Personal</span>
                  <span class="badge-count">3/3</span>
                </button>
                <div class="accordion-content">
                  <div class="checklist-item valid">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span>Cédula de Ciudadanía legible al 150%</span>
                  </div>
                  <div class="checklist-item valid">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span>RUT Actualizado con fecha de expedición</span>
                  </div>
                </div>
              </div>

              <!-- Categoría 3: Formación Académica -->
              <div class="accordion-item">
                <button type="button" class="accordion-trigger" aria-expanded="false">
                  <span>Formación Académica</span>
                  <span class="badge-count">5/5</span>
                </button>
                <div class="accordion-content">
                  <div class="checklist-item valid">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span>Título de Pregrado y Acta de Grado</span>
                  </div>
                  <div class="checklist-item valid">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span>Título de Maestría convalidado / legalizado</span>
                  </div>
                </div>
              </div>

              <!-- Categoría 4: Experiencia Laboral -->
              <div class="accordion-item">
                <button type="button" class="accordion-trigger" aria-expanded="false">
                  <span>Experiencia Laboral Docente</span>
                  <span class="badge-count">1/1</span>
                </button>
                <div class="accordion-content">
                  <div class="checklist-item valid">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span>Certificados con funciones y fechas exactas</span>
                  </div>
                </div>
              </div>

              <!-- Categoría 5: Información Bancaria -->
              <div class="accordion-item">
                <button type="button" class="accordion-trigger" aria-expanded="false">
                  <span>Información Bancaria</span>
                  <span class="badge-count">1/1</span>
                </button>
                <div class="accordion-content">
                  <div class="checklist-item valid">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span>Certificado Apertura Cuenta Davivienda *</span>
                  </div>
                </div>
              </div>

              <!-- Categoría 6: Salud Ocupacional y SST -->
              <div class="accordion-item">
                <button type="button" class="accordion-trigger" aria-expanded="false">
                  <span>Salud Ocupacional</span>
                  <span class="badge-count">1/1</span>
                </button>
                <div class="accordion-content">
                  <div class="checklist-item valid">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span>Concepto de Aptitud Laboral Ocupacional</span>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </section>

      </div>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
