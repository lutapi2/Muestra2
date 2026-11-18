<?php
  $baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
  if ($baseUrl === '/' || $baseUrl === '\\') $baseUrl = '';
?>
  <footer style="background: #081910; color: #9ca3af; padding: 2rem 1.5rem; border-top: 1px solid rgba(255,255,255,0.08); font-size: 0.85rem; margin-top: auto;">
    <div style="max-width: 1200px; margin: 0 auto; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">
      <div>
        <p style="color: #ffffff; font-weight: 600; margin-bottom: 0.25rem;">Corporación Universitaria del Caribe - CECAR</p>
        <p>Sistema de Selección y Convocatorias Docentes v3.0 (2026) | Dirección de Talento Humano</p>
      </div>
      <div>
        <p>&copy; <?= date('Y') ?> CECAR. Todos los derechos reservados. Sincelejo, Sucre, Colombia.</p>
      </div>
    </div>
  </footer>

  <!-- Botón flotante de Tutorial observado en el Mockup (Páginas 1 y 2) -->
  <button class="floating-tutorial-pill" type="button" aria-label="Abrir tutorial rápido del sistema">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <circle cx="12" cy="12" r="10"></circle>
      <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
      <line x1="12" y1="17" x2="12.01" y2="17"></line>
    </svg>
    <span>Tutorial</span>
  </button>

  <!-- Scripts Principales -->
  <script src="<?= $baseUrl ?>/assets/js/api.js"></script>
  <script src="<?= $baseUrl ?>/assets/js/app.js"></script>
  <?php if (!empty($customScripts)): ?>
    <?php foreach ($customScripts as $script): ?>
      <script src="<?= $baseUrl ?>/assets/js/<?= htmlspecialchars($script) ?>"></script>
    <?php endforeach; ?>
  <?php endif; ?>
</body>
</html>
