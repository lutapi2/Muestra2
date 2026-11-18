<?php
  $pageTitle = "CECAR - Toda tu gestión de selección en un solo lugar";
  require_once __DIR__ . '/../layout/header.php';
  require_once __DIR__ . '/../layout/navbar.php';
  $baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
  if ($baseUrl === '/' || $baseUrl === '\\') $baseUrl = '';
?>

<main id="main-content" class="hero-wrapper">
  <div class="hero-container">
    <!-- Columna Izquierda: Mensaje y Acciones del Mockup Página 1 -->
    <div class="hero-content">
      <div class="hero-tag-pill">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
        </svg>
        <span>Nueva plataforma</span>
      </div>

      <h1 class="hero-title">
        Toda tu <span class="highlight-green">gestión de selección</span><br>
        en un solo lugar
      </h1>

      <p class="hero-desc">
        Una plataforma integral para postularte a las convocatorias públicas docentes de la institución de forma ágil, segura y con trazabilidad total. Centraliza, postula y decide mejor.
      </p>

      <div class="hero-actions">
        <a href="<?= $baseUrl ?>/registro" class="btn-pill btn-primary-pill">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="8.5" cy="7" r="4"></circle>
            <line x1="20" y1="8" x2="20" y2="14"></line>
            <line x1="23" y1="11" x2="17" y2="11"></line>
          </svg>
          <span>Registrarme como Candidato</span>
        </a>

        <a href="<?= $baseUrl ?>/convocatorias" class="btn-pill btn-ghost-pill">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="10"></circle>
            <polygon points="10 8 16 12 10 16 10 8"></polygon>
          </svg>
          <span>Ver convocatorias abiertas</span>
        </a>
      </div>
    </div>

    <!-- Columna Derecha: Tarjeta Visual Interactiva observada en Mockup Página 1 -->
    <div class="hero-card-column">
      <div class="hero-preview-card">
        <div class="preview-card-header">
          <div class="window-dots" aria-hidden="true">
            <span class="window-dot" style="background: #ef4444;"></span>
            <span class="window-dot" style="background: #eab308;"></span>
            <span class="window-dot" style="background: #22c55e;"></span>
          </div>
          <span style="font-size: 0.75rem; color: #9ca3af; margin-left: 0.5rem;">Expediente Digital del Aspirante</span>
        </div>

        <div class="preview-card-body">
          <div class="preview-item-row">
            <div class="preview-item-left">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
              </svg>
              <span>Hoja de Vida y Títulos Académicos</span>
            </div>
            <span class="badge-status badge-approved">Aprobado</span>
          </div>

          <div class="preview-item-row">
            <div class="preview-item-left">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
              <span>Verificación de Requisitos Mínimos</span>
            </div>
            <span class="badge-status badge-process">En proceso</span>
          </div>

          <div class="preview-item-row">
            <div class="preview-item-left">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
              </svg>
              <span>Entrevista y Prueba de Aula</span>
            </div>
            <span class="badge-status" style="background: rgba(255,255,255,0.08); color: #9ca3af;">Pendiente</span>
          </div>

          <div class="preview-item-row">
            <div class="preview-item-left">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 20h9"></path>
                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
              </svg>
              <span>Concepto y Formalización</span>
            </div>
            <span class="badge-status badge-approved">Firmado</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
