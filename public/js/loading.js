/**
 * Plataforma de Contratación CECAR
 * Gestor de Pantallas de Carga y Transiciones de Autenticación
 */

const CecarLoading = (function () {
    let overlay = null;
    let titleEl = null;
    let subtitleEl = null;
    let stepEl = null;
    let barEl = null;
    let hintEl = null;

    function initOverlay() {
        if (overlay) return;

        overlay = document.createElement('div');
        overlay.id = 'cecarLoadingOverlay';
        overlay.className = 'cecar-loading-overlay';
        overlay.innerHTML = `
            <div class="cecar-loading-card" id="cecarLoadingCard">
                <div class="cecar-loading-logo">C</div>
                <h3 class="cecar-loading-title" id="cecarLoadingTitle">Acceso Institucional</h3>
                <p class="cecar-loading-subtitle" id="cecarLoadingSubtitle">Sistema de Selección y Contratación CECAR</p>
                
                <div class="cecar-dual-ring"></div>

                <div class="cecar-loading-step" id="cecarLoadingStep">Iniciando verificación...</div>
                
                <div class="cecar-progress-wrap">
                    <div class="cecar-progress-bar" id="cecarProgressBar" style="width: 15%;"></div>
                </div>

                <div class="cecar-loading-hint" id="cecarLoadingHint">Por favor espere mientras protegemos su sesión...</div>
            </div>
        `;

        document.body.appendChild(overlay);

        titleEl = document.getElementById('cecarLoadingTitle');
        subtitleEl = document.getElementById('cecarLoadingSubtitle');
        stepEl = document.getElementById('cecarLoadingStep');
        barEl = document.getElementById('cecarProgressBar');
        hintEl = document.getElementById('cecarLoadingHint');
    }

    function show(options = {}) {
        initOverlay();
        if (options.title && titleEl) titleEl.textContent = options.title;
        if (options.subtitle && subtitleEl) subtitleEl.textContent = options.subtitle;
        if (options.step && stepEl) stepEl.textContent = options.step;
        if (options.hint && hintEl) hintEl.textContent = options.hint;
        if (barEl) barEl.style.width = (options.progress || 20) + '%';

        overlay.classList.add('active');
    }

    function updateProgress(percent, stepText = '', hintText = '') {
        if (!overlay) initOverlay();
        if (barEl) barEl.style.width = Math.min(100, Math.max(0, percent)) + '%';
        if (stepText && stepEl) {
            stepEl.style.opacity = '0';
            setTimeout(() => {
                stepEl.textContent = stepText;
                stepEl.style.opacity = '1';
            }, 100);
        }
        if (hintText && hintEl) hintEl.textContent = hintText;
    }

    function hide() {
        if (overlay) {
            overlay.classList.remove('active');
        }
    }

    return {
        show,
        updateProgress,
        hide
    };
})();

// Auto-bind eventos al cargar el DOM
document.addEventListener('DOMContentLoaded', function () {
    // 1. Manejo del formulario de inicio de sesión institucional
    const loginForm = document.getElementById('loginForm');
    const loginErrorBox = document.getElementById('loginErrorBox');
    const loginBtn = document.getElementById('btnLoginSubmit');

    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const correo = (loginForm.querySelector('input[name="correo"]') || {}).value || '';
            const password = (loginForm.querySelector('input[name="password"]') || {}).value || '';
            const csrfToken = (loginForm.querySelector('input[name="csrf_token"]') || {}).value || '';

            if (loginErrorBox) {
                loginErrorBox.style.display = 'none';
                loginErrorBox.textContent = '';
            }

            if (loginBtn) {
                loginBtn.disabled = true;
            }

            // Mostrar pantalla de carga con pasos institucionales
            CecarLoading.show({
                title: 'Autenticando Acceso Institucional',
                subtitle: 'Sistema de Selección y Contratación CECAR',
                step: 'Validando credenciales seguras...',
                hint: 'Verificando con el directorio institucional de CECAR',
                progress: 25
            });

            const formData = new FormData(loginForm);

            // Simular progresión visual fluida
            setTimeout(() => {
                CecarLoading.updateProgress(55, 'Comprobando roles y autorizaciones...');
            }, 300);

            fetch(loginForm.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    CecarLoading.updateProgress(85, '¡Credenciales confirmadas!', 'Preparando su entorno...');
                    setTimeout(() => {
                        CecarLoading.updateProgress(100, 'Acceso concedido. Redirigiendo...', 'Bienvenido(a)');
                        setTimeout(() => {
                            window.location.href = data.redirect || 'index.php?c=Dashboard&a=index';
                        }, 350);
                    }, 300);
                } else {
                    setTimeout(() => {
                        CecarLoading.hide();
                        if (loginBtn) loginBtn.disabled = false;

                        if (loginErrorBox) {
                            loginErrorBox.textContent = data.message || 'Credenciales incorrectas o usuario inactivo.';
                            loginErrorBox.style.display = 'block';
                            loginErrorBox.classList.remove('shake-error');
                            void loginErrorBox.offsetWidth; // trigger reflow
                            loginErrorBox.classList.add('shake-error');
                        } else {
                            alert(data.message || 'Credenciales incorrectas o usuario inactivo.');
                        }
                    }, 400);
                }
            })
            .catch(err => {
                // Fallback a envío normal si la red o el endpoint no responden en JSON
                loginForm.submit();
            });
        });
    }

    // 2. Manejo de botones de Acceso Rápido de Prueba (Demo Roles)
    const quickLoginLinks = document.querySelectorAll('a[href*="quickLogin"]');
    quickLoginLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const targetUrl = this.href;
            const rolNombre = this.textContent.trim();

            CecarLoading.show({
                title: 'Conmutando Rol Institucional',
                subtitle: 'Acceso Inmediato de Prueba CECAR',
                step: 'Inicializando entorno para: ' + rolNombre,
                hint: 'Configurando permisos y vistas del perfil seleccionado...',
                progress: 35
            });

            setTimeout(() => {
                CecarLoading.updateProgress(75, 'Aplicando directivas de seguridad para ' + rolNombre + '...');
            }, 250);

            setTimeout(() => {
                CecarLoading.updateProgress(100, '¡Sesión configurada! Redirigiendo al Dashboard...');
                setTimeout(() => {
                    window.location.href = targetUrl;
                }, 300);
            }, 550);
        });
    });

    // 3. Manejo de formulario de registro de aspirantes
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
            // Verificar si el formulario es válido según HTML5
            if (!this.checkValidity()) return;

            CecarLoading.show({
                title: 'Registro de Aspirante Institucional',
                subtitle: 'Plataforma de Selección y Contratación CECAR',
                step: 'Validando datos personales y autorización Ley 1581...',
                hint: 'Generando cuenta y radicando consentimiento...',
                progress: 40
            });

            setTimeout(() => {
                CecarLoading.updateProgress(80, 'Creando expediente de candidato...');
            }, 300);
        });
    }

    // 4. Manejo de formulario de recuperación de contraseña
    const recuperarForm = document.getElementById('recuperarForm');
    if (recuperarForm) {
        recuperarForm.addEventListener('submit', function (e) {
            if (!this.checkValidity()) return;

            CecarLoading.show({
                title: 'Verificando Solicitud',
                subtitle: 'Seguridad Institucional CECAR',
                step: 'Consultando registro de correo institucional...',
                hint: 'Generando token seguro de recuperación...',
                progress: 50
            });
        });
    }
});
