<?php
  $pageTitle = "Convocatorias Públicas Docentes - CECAR";
  require_once __DIR__ . '/../layout/header.php';
  require_once __DIR__ . '/../layout/navbar.php';
  $baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
  if ($baseUrl === '/' || $baseUrl === '\\') $baseUrl = '';
?>

<main id="main-content" style="padding: var(--space-8) var(--space-6); max-width: 1200px; margin: 0 auto; width: 100%;">
  
  <div style="margin-bottom: var(--space-8); text-align: center;">
    <span class="badge-status badge-approved" style="margin-bottom: var(--space-2);">Procesos Abiertos 2026</span>
    <h1 style="font-size: var(--font-size-3xl); font-weight: 800; color: var(--color-brand-primary); margin-bottom: var(--space-2);">
      Convocatorias Públicas de Méritos Docente
    </h1>
    <p style="color: var(--color-text-secondary); max-width: 600px; margin: 0 auto; font-size: var(--font-size-sm);">
      Explore las oportunidades académicas vigentes en las diferentes facultades de CECAR. Revise perfiles, requisitos mínimos y postúlese en línea (RF-011, RF-012, RF-013).
    </p>
  </div>

  <!-- Barra de Filtros (Mockup Página 2) -->
  <div class="table-card" style="margin-bottom: var(--space-6);">
    <div class="filter-toolbar" style="border: none;">
      <input type="text" id="search-filtro" class="search-input" style="flex: 1;" placeholder="Buscar por cargo, programa o palabra clave...">
      
      <select class="filter-select" aria-label="Filtrar por facultad">
        <option value="">Todas las facultades</option>
        <option value="Ingenieria">Facultad de Ciencias Básicas e Ingeniería</option>
        <option value="Educacion">Facultad de Educación y Ciencias Sociales</option>
        <option value="Economicas">Facultad de Ciencias Económicas</option>
        <option value="Derecho">Facultad de Derecho y Ciencias Políticas</option>
      </select>

      <select class="filter-select" aria-label="Filtrar por dedicación">
        <option value="">Cualquier dedicación</option>
        <option value="TC">Tiempo Completo</option>
        <option value="MT">Medio Tiempo</option>
        <option value="Catedra">Cátedra</option>
      </select>
    </div>
  </div>

  <!-- Catálogo de Convocatorias en Grid de Tarjetas -->
  <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: var(--space-6);">
    
    <!-- Tarjeta 1 (Semilla 02_seed.sql) -->
    <article class="table-card" style="margin-bottom: 0; display: flex; flex-direction: column; justify-content: space-between;">
      <div style="padding: var(--space-6);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: var(--space-3);">
          <span class="badge-status badge-approved">ABIERTA</span>
          <span style="font-size: 0.75rem; color: var(--color-text-muted);">Cód. CONV-2026-01</span>
        </div>

        <h2 style="font-size: var(--font-size-lg); font-weight: 700; color: var(--color-text-main); margin-bottom: var(--space-2);">
          Docente Cátedra - Ingeniería de Software
        </h2>

        <p style="font-size: var(--font-size-sm); color: var(--color-text-secondary); margin-bottom: var(--space-4);">
          <strong>Facultad:</strong> Ciencias Básicas, Ingeniería y Arquitectura.<br>
          <strong>Dedicación:</strong> Cátedra (12 horas semanales).<br>
          <strong>Vacantes:</strong> 2 plazas disponibles.
        </p>

        <div style="background: var(--color-bg-app); padding: var(--space-3); border-radius: var(--radius-md); font-size: var(--font-size-xs); color: var(--color-text-secondary); margin-bottom: var(--space-4);">
          <strong>Cierre de postulación:</strong> 30 de Noviembre de 2026
        </div>
      </div>

      <div style="padding: var(--space-4) var(--space-6); background: #f8fafc; border-top: 1px solid var(--color-border-subtle); display: flex; justify-content: space-between; align-items: center;">
        <a href="<?= $baseUrl ?>/registro" class="btn-pill btn-primary-pill btn-postular" data-codigo="CONV-2026-01" style="padding: 0.45rem 1.25rem; font-size: 0.85rem;">
          Postularme
        </a>
        <button type="button" class="btn-pill btn-ghost-pill" style="padding: 0.45rem 1rem; font-size: 0.85rem; color: var(--color-brand-primary); border-color: var(--color-brand-primary);" onclick="alert('Perfil Requerido:\n- Profesional en Ingeniería de Sistemas.\n- Título de Maestría afín.\n- Mínimo 1 año de experiencia docente universitaria.')">
          Ver Perfil
        </button>
      </div>
    </article>

    <!-- Tarjeta 2 -->
    <article class="table-card" style="margin-bottom: 0; display: flex; flex-direction: column; justify-content: space-between;">
      <div style="padding: var(--space-6);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: var(--space-3);">
          <span class="badge-status badge-approved">ABIERTA</span>
          <span style="font-size: 0.75rem; color: var(--color-text-muted);">Cód. CONV-2026-02</span>
        </div>

        <h2 style="font-size: var(--font-size-lg); font-weight: 700; color: var(--color-text-main); margin-bottom: var(--space-2);">
          Docente Tiempo Completo - Pedagogía Infantil
        </h2>

        <p style="font-size: var(--font-size-sm); color: var(--color-text-secondary); margin-bottom: var(--space-4);">
          <strong>Facultad:</strong> Humanidades y Educación.<br>
          <strong>Dedicación:</strong> Tiempo Completo (40 horas semanales).<br>
          <strong>Vacantes:</strong> 1 plaza disponible.
        </p>

        <div style="background: var(--color-bg-app); padding: var(--space-3); border-radius: var(--radius-md); font-size: var(--font-size-xs); color: var(--color-text-secondary); margin-bottom: var(--space-4);">
          <strong>Cierre de postulación:</strong> 15 de Diciembre de 2026
        </div>
      </div>

      <div style="padding: var(--space-4) var(--space-6); background: #f8fafc; border-top: 1px solid var(--color-border-subtle); display: flex; justify-content: space-between; align-items: center;">
        <a href="<?= $baseUrl ?>/registro" class="btn-pill btn-primary-pill btn-postular" data-codigo="CONV-2026-01" style="padding: 0.45rem 1.25rem; font-size: 0.85rem;">
          Postularme
        </a>
        <button type="button" class="btn-pill btn-ghost-pill" style="padding: 0.45rem 1rem; font-size: 0.85rem; color: var(--color-brand-primary); border-color: var(--color-brand-primary);" onclick="alert('Perfil Requerido:\n- Licenciado en Educación o Pedagogía.\n- Título de Maestría o Doctorado.\n- Mínimo 2 años de experiencia investigativa.')">
          Ver Perfil
        </button>
      </div>
    </article>

  </div>

</main>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
