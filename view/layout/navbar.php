<?php
  $baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
  if ($baseUrl === '/' || $baseUrl === '\\') $baseUrl = '';
?>
<header class="site-header" style="background: #081910; border-bottom: 1px solid rgba(255,255,255,0.08); padding: 0.75rem 1.5rem;">
  <div style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between;">
    <a href="<?= $baseUrl ?>/" style="display: flex; align-items: center; gap: 0.75rem; text-decoration: none; color: #ffffff;">
      <div style="width: 38px; height: 38px; border-radius: 8px; background: var(--color-brand-primary); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.1rem; color: #fff;">
        C
      </div>
      <div>
        <div style="font-weight: 800; font-size: 1.1rem; letter-spacing: -0.01em;">CECAR</div>
        <div style="font-size: 0.7rem; color: var(--color-hero-text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Selección de Personal</div>
      </div>
    </a>

    <nav aria-label="Navegación principal">
      <ul style="display: flex; list-style: none; gap: 1.25rem; align-items: center; margin: 0;">
        <li><a href="<?= $baseUrl ?>/" style="color: #e5e7eb; font-size: 0.9rem; font-weight: 500;">Inicio</a></li>
        <li><a href="<?= $baseUrl ?>/convocatorias" style="color: #e5e7eb; font-size: 0.9rem; font-weight: 500;">Convocatorias</a></li>
        <li><a href="<?= $baseUrl ?>/login" style="color: #e5e7eb; font-size: 0.9rem; font-weight: 500;">Iniciar Sesión</a></li>
        <li>
          <a href="<?= $baseUrl ?>/registro" class="btn-pill btn-primary-pill" style="padding: 0.45rem 1.2rem; font-size: 0.85rem;">
            Registrarme
          </a>
        </li>
      </ul>
    </nav>
  </div>
</header>
