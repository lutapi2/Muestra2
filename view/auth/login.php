<?php
  $pageTitle = "Iniciar Sesión - Sistema de Selección CECAR";
  $customScripts = ['auth.js'];
  require_once __DIR__ . '/../layout/header.php';
  require_once __DIR__ . '/../layout/navbar.php';
  $baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
  if ($baseUrl === '/' || $baseUrl === '\\') $baseUrl = '';
?>

<main id="main-content" class="auth-page-wrapper">
  <div class="auth-card narrow">
    <div class="auth-card-header">
      <div style="display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 50%; background: var(--color-brand-primary-light); color: var(--color-brand-primary); margin-bottom: 0.75rem;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
          <polyline points="10 17 15 12 10 7"></polyline>
          <line x1="15" y1="12" x2="3" y2="12"></line>
        </svg>
      </div>
      <h1 class="auth-card-title">Iniciar Sesión</h1>
      <p class="auth-card-subtitle">
        Ingrese sus credenciales de acceso para gestionar sus postulaciones docentes en CECAR (RF-002).
      </p>
    </div>

    <!-- Contenedor dinámico de alertas -->
    <div id="alert-container" aria-live="polite"></div>

    <form id="form-login" novalidate>
      <div class="form-group">
        <label for="correo" class="form-label">Correo Electrónico</label>
        <input type="email" id="correo" name="correo" class="form-control" placeholder="correo@ejemplo.com" required aria-required="true">
      </div>

      <div class="form-group">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <label for="password" class="form-label">Contraseña</label>
        </div>
        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required aria-required="true">
      </div>

      <div style="margin-top: 1.5rem;">
        <button type="submit" id="btn-submit-login" class="btn-primary-block">
          <span class="spinner" style="display: none;" aria-hidden="true"></span>
          <span class="btn-text">Ingresar al Sistema</span>
        </button>
      </div>
    </form>

    <div style="text-align: center; margin-top: 1.5rem; font-size: 0.875rem; color: var(--color-text-secondary);">
      ¿Aún no tiene cuenta? <a href="<?= $baseUrl ?>/registro" style="font-weight: 600;">Regístrese aquí</a>
    </div>
  </div>
</main>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
