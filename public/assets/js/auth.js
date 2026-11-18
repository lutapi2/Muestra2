/**
 * Controlador de Autenticación y Registro de Candidatos - CECAR v3.0 (2026)
 * Satisface:
 *   - RF-001: Validación e integración de registro de candidatos.
 *   - RF-002: Inicio de sesión de candidatos.
 *   - RN-004: Validación de longitud de contraseña >= 8 caracteres.
 *   - RNF-024: Manejo seguro de errores sin fugar datos del sistema.
 */

document.addEventListener('DOMContentLoaded', () => {
  const registroForm = document.getElementById('form-registro-candidato');
  const loginForm = document.getElementById('form-login');

  // =========================================================================
  // 1. REGISTRO DE CANDIDATOS (RF-001, RN-004)
  // =========================================================================
  if (registroForm) {
    const passwordInput = document.getElementById('password');
    const emailInput = document.getElementById('correo');

    // Validación interactiva de contraseña mínima de 8 caracteres (RN-004)
    if (passwordInput) {
      passwordInput.addEventListener('input', () => {
        const val = passwordInput.value;
        const feedback = document.getElementById('feedback-password');
        const group = passwordInput.closest('.form-group');

        if (val.length > 0 && val.length < 8) {
          group.classList.add('has-error');
          passwordInput.classList.add('is-invalid');
          if (feedback) feedback.textContent = 'La contraseña debe contener al menos 8 caracteres (RN-004).';
        } else {
          group.classList.remove('has-error');
          passwordInput.classList.remove('is-invalid');
          if (feedback) feedback.textContent = '';
        }
      });
    }

    registroForm.addEventListener('submit', async (e) => {
      e.preventDefault();

      const alertContainer = document.getElementById('alert-container');
      const submitBtn = document.getElementById('btn-submit-registro');
      const btnText = submitBtn.querySelector('.btn-text');
      const btnSpinner = submitBtn.querySelector('.spinner');

      // Limpiar errores previos
      clearErrors(registroForm);
      if (alertContainer) alertContainer.innerHTML = '';

      // Extraer datos del formulario
      const payload = {
        nombres: document.getElementById('nombres').value.trim(),
        apellidos: document.getElementById('apellidos').value.trim(),
        id_tipo_documento: parseInt(document.getElementById('id_tipo_documento').value, 10),
        numero_documento: document.getElementById('numero_documento').value.trim(),
        telefono: document.getElementById('telefono').value.trim(),
        ciudad: document.getElementById('ciudad').value.trim(),
        direccion: document.getElementById('direccion') ? document.getElementById('direccion').value.trim() : '',
        correo: document.getElementById('correo').value.trim(),
        password: document.getElementById('password').value
      };

      // Validaciones previas cliente
      let hasClientErrors = false;
      if (payload.password.length < 8) {
        showFieldError('password', 'La contraseña debe contener al menos 8 caracteres (RN-004).');
        hasClientErrors = true;
      }
      if (!validateEmail(payload.correo)) {
        showFieldError('correo', 'El formato del correo electrónico es inválido.');
        hasClientErrors = true;
      }

      if (hasClientErrors) return;

      // Estado Cargando (Spinner + botón deshabilitado)
      submitBtn.disabled = true;
      submitBtn.setAttribute('aria-busy', 'true');
      if (btnSpinner) btnSpinner.style.display = 'inline-block';
      if (btnText) btnText.textContent = 'Registrando candidato...';

      const result = await ApiClient.post('/api/auth/register', payload);

      // Restaurar estado botón
      submitBtn.disabled = false;
      submitBtn.removeAttribute('aria-busy');
      if (btnSpinner) btnSpinner.style.display = 'none';
      if (btnText) btnText.textContent = 'Completar Registro';

      if (result.success) {
        showAlert(
          alertContainer,
          'success',
          '¡Registro exitoso! Su cuenta ha sido creada. Redirigiendo a inicio de sesión...'
        );
        registroForm.reset();
        setTimeout(() => {
          window.location.href = `${ApiClient.getBaseUrl()}/login`;
        }, 1800);
      } else {
        // Manejar errores 400 por campo o generales
        if (result.error && typeof result.error === 'object') {
          for (const [field, msg] of Object.entries(result.error)) {
            showFieldError(field, msg);
          }
        }
        showAlert(alertContainer, 'danger', result.message || 'No se pudo completar el registro.');
      }
    });
  }

  // =========================================================================
  // 2. INICIO DE SESIÓN (RF-002)
  // =========================================================================
  if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
      e.preventDefault();

      const alertContainer = document.getElementById('alert-container');
      const submitBtn = document.getElementById('btn-submit-login');
      const btnText = submitBtn.querySelector('.btn-text');
      const btnSpinner = submitBtn.querySelector('.spinner');

      clearErrors(loginForm);
      if (alertContainer) alertContainer.innerHTML = '';

      const correo = document.getElementById('correo').value.trim();
      const password = document.getElementById('password').value;

      if (!correo || !password) {
        showAlert(alertContainer, 'warning', 'Por favor complete su correo y contraseña.');
        return;
      }

      // Estado Cargando
      submitBtn.disabled = true;
      submitBtn.setAttribute('aria-busy', 'true');
      if (btnSpinner) btnSpinner.style.display = 'inline-block';
      if (btnText) btnText.textContent = 'Verificando...';

      const result = await ApiClient.post('/api/auth/login', { correo, password });

      submitBtn.disabled = false;
      submitBtn.removeAttribute('aria-busy');
      if (btnSpinner) btnSpinner.style.display = 'none';
      if (btnText) btnText.textContent = 'Ingresar al Sistema';

      if (result.success && result.data) {
        // Almacenar datos básicos de sesión y token
        localStorage.setItem('cecar_user', JSON.stringify(result.data));
        if (result.data.token) {
          localStorage.setItem('cecar_session_token', result.data.token);
        }
        showAlert(alertContainer, 'success', 'Bienvenido al Sistema de Selección CECAR. Ingresando...');
        setTimeout(() => {
          window.location.href = `${ApiClient.getBaseUrl()}/dashboard`;
        }, 1000);
      } else {
        showAlert(alertContainer, 'danger', result.message || 'Credenciales no autorizadas.');
      }
    });
  }

  // =========================================================================
  // FUNCIONES AUXILIARES DE UI
  // =========================================================================
  function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  function showFieldError(fieldId, message) {
    const input = document.getElementById(fieldId);
    if (!input) return;
    const group = input.closest('.form-group');
    if (group) {
      group.classList.add('has-error');
      const feedback = group.querySelector('.form-feedback') || document.getElementById(`feedback-${fieldId}`);
      if (feedback) feedback.textContent = message;
    }
    input.classList.add('is-invalid');
  }

  function clearErrors(form) {
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.has-error').forEach(el => el.classList.remove('has-error'));
    form.querySelectorAll('.form-feedback').forEach(el => el.textContent = '');
  }

  function showAlert(container, type, message) {
    if (!container) return;
    container.innerHTML = `
      <div class="alert-box ${type}" role="alert">
        <span>${message}</span>
      </div>
    `;
  }
});
