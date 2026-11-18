<?php
  $pageTitle = "Registro de Candidato - Sistema de Selección CECAR";
  $customScripts = ['auth.js'];
  require_once __DIR__ . '/../layout/header.php';
  require_once __DIR__ . '/../layout/navbar.php';
  $baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
  if ($baseUrl === '/' || $baseUrl === '\\') $baseUrl = '';
?>

<main id="main-content" class="auth-page-wrapper">
  <div class="auth-card">
    <div class="auth-card-header">
      <div style="display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 50%; background: var(--color-brand-primary-light); color: var(--color-brand-primary); margin-bottom: 0.75rem;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
          <circle cx="8.5" cy="7" r="4"></circle>
          <polyline points="17 11 19 13 23 9"></polyline>
        </svg>
      </div>
      <h1 class="auth-card-title">Registro de Candidato</h1>
      <p class="auth-card-subtitle">
        Diligencie sus datos de filiación y cree su cuenta para postularse a convocatorias docentes de CECAR (RF-001).
      </p>
    </div>

    <!-- Contenedor dinámico de alertas -->
    <div id="alert-container" aria-live="polite"></div>

    <form id="form-registro-candidato" novalidate>
      <div class="form-grid-2">
        <!-- Nombres -->
        <div class="form-group">
          <label for="nombres" class="form-label">Nombres <span style="color: red;">*</span></label>
          <input type="text" id="nombres" name="nombres" class="form-control" placeholder="Ej. Mateo David" required aria-required="true" aria-describedby="feedback-nombres">
          <div id="feedback-nombres" class="form-feedback"></div>
        </div>

        <!-- Apellidos -->
        <div class="form-group">
          <label for="apellidos" class="form-label">Apellidos <span style="color: red;">*</span></label>
          <input type="text" id="apellidos" name="apellidos" class="form-control" placeholder="Ej. Morales Castillo" required aria-required="true" aria-describedby="feedback-apellidos">
          <div id="feedback-apellidos" class="form-feedback"></div>
        </div>

        <!-- Tipo de Documento -->
        <div class="form-group">
          <label for="id_tipo_documento" class="form-label">Tipo de Documento <span style="color: red;">*</span></label>
          <select id="id_tipo_documento" name="id_tipo_documento" class="form-control" required aria-required="true" aria-describedby="feedback-tipo-doc">
            <option value="1">Cédula de Ciudadanía (CC)</option>
            <option value="2">Cédula de Extranjería (CE)</option>
            <option value="3">Pasaporte (PA)</option>
            <option value="4">Permiso Especial de Permanencia (PEP)</option>
            <option value="5">Permiso por Protección Temporal (PPT)</option>
          </select>
          <div id="feedback-tipo-doc" class="form-feedback"></div>
        </div>

        <!-- Número de Documento -->
        <div class="form-group">
          <label for="numero_documento" class="form-label">Número de Documento <span style="color: red;">*</span></label>
          <input type="text" id="numero_documento" name="numero_documento" class="form-control" placeholder="Ej. 1104859600" required aria-required="true" aria-describedby="feedback-documento">
          <div id="feedback-documento" class="form-feedback"></div>
        </div>

        <!-- Teléfono Celular -->
        <div class="form-group">
          <label for="telefono" class="form-label">Teléfono Móvil <span style="color: red;">*</span></label>
          <input type="tel" id="telefono" name="telefono" class="form-control" placeholder="Ej. 3007654321" required aria-required="true" aria-describedby="feedback-telefono">
          <div id="feedback-telefono" class="form-feedback"></div>
        </div>

        <!-- Ciudad de Residencia -->
        <div class="form-group">
          <label for="ciudad" class="form-label">Ciudad de Residencia <span style="color: red;">*</span></label>
          <input type="text" id="ciudad" name="ciudad" class="form-control" placeholder="Ej. Sincelejo" required aria-required="true" aria-describedby="feedback-ciudad">
          <div id="feedback-ciudad" class="form-feedback"></div>
        </div>

        <!-- Dirección (Opcional) -->
        <div class="form-group full-width">
          <label for="direccion" class="form-label">Dirección de Contacto (Opcional)</label>
          <input type="text" id="direccion" name="direccion" class="form-control" placeholder="Ej. Barrio La Palma Cra 25 # 12-40">
        </div>

        <!-- Correo Electrónico -->
        <div class="form-group full-width">
          <label for="correo" class="form-label">Correo Electrónico <span style="color: red;">*</span></label>
          <input type="email" id="correo" name="correo" class="form-control" placeholder="aspirante@correo.com" required aria-required="true" aria-describedby="feedback-correo">
          <div id="feedback-correo" class="form-feedback"></div>
        </div>

        <!-- Contraseña (RN-004: mínimo 8 caracteres) -->
        <div class="form-group full-width">
          <label for="password" class="form-label">Contraseña de Acceso <span style="color: red;">*</span></label>
          <input type="password" id="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" required aria-required="true" aria-describedby="feedback-password">
          <small style="color: var(--color-text-muted); font-size: 0.75rem; margin-top: 2px;">
            La contraseña debe tener una longitud mínima de 8 caracteres (Regla de negocio RN-004).
          </small>
          <div id="feedback-password" class="form-feedback"></div>
        </div>
      </div>

      <!-- Botón de Envío con Estado de Carga -->
      <div style="margin-top: 1.5rem;">
        <button type="submit" id="btn-submit-registro" class="btn-primary-block">
          <span class="spinner" style="display: none;" aria-hidden="true"></span>
          <span class="btn-text">Completar Registro</span>
        </button>
      </div>
    </form>

    <div style="text-align: center; margin-top: 1.5rem; font-size: 0.875rem; color: var(--color-text-secondary);">
      ¿Ya posee una cuenta registrada? <a href="<?= $baseUrl ?>/login" style="font-weight: 600;">Inicie sesión aquí</a>
    </div>
  </div>
</main>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
