<div style="max-width: 560px; margin: 2rem auto; padding: 2.25rem; background: #0d211c; border: 1px solid #16362e; border-radius: 14px; width: 100%;">
    <div style="text-align: center; margin-bottom: 1.75rem;">
        <h2 style="color: #ffffff; font-size: 1.5rem; font-weight: 800;">Registro de Aspirante</h2>
        <p style="color: #94a3b8; font-size: 0.85rem;">Cree su cuenta para postular a convocatorias públicas de CECAR</p>
    </div>

    <form action="<?= BASE_URL ?>/index.php?c=Auth&a=doRegister" method="POST" id="registerForm">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <label style="display: block; color: #cbd5e1; font-size: 0.82rem; font-weight: 600; margin-bottom: 0.35rem;">Nombres</label>
                <input type="text" name="nombres" required class="filter-control" style="width: 100%;" placeholder="Nombres">
            </div>
            <div>
                <label style="display: block; color: #cbd5e1; font-size: 0.82rem; font-weight: 600; margin-bottom: 0.35rem;">Apellidos</label>
                <input type="text" name="apellidos" required class="filter-control" style="width: 100%;" placeholder="Apellidos">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 120px 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <label style="display: block; color: #cbd5e1; font-size: 0.82rem; font-weight: 600; margin-bottom: 0.35rem;">Tipo Doc.</label>
                <select name="tipo_documento" class="filter-control" style="width: 100%;">
                    <option value="CC">CC</option>
                    <option value="CE">CE</option>
                    <option value="PA">PA</option>
                </select>
            </div>
            <div>
                <label style="display: block; color: #cbd5e1; font-size: 0.82rem; font-weight: 600; margin-bottom: 0.35rem;">Número de Documento</label>
                <input type="text" name="numero_documento" required class="filter-control" style="width: 100%;" placeholder="Cédula de ciudadanía">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <label style="display: block; color: #cbd5e1; font-size: 0.82rem; font-weight: 600; margin-bottom: 0.35rem;">Correo Electrónico</label>
                <input type="email" name="correo" required class="filter-control" style="width: 100%;" placeholder="correo@ejemplo.com">
            </div>
            <div>
                <label style="display: block; color: #cbd5e1; font-size: 0.82rem; font-weight: 600; margin-bottom: 0.35rem;">Teléfono Móvil</label>
                <input type="tel" name="telefono" required class="filter-control" style="width: 100%;" placeholder="300 000 0000">
            </div>
        </div>

        <div style="margin-bottom: 1.25rem;">
            <label style="display: block; color: #cbd5e1; font-size: 0.82rem; font-weight: 600; margin-bottom: 0.35rem;">Contraseña de Acceso</label>
            <input type="password" name="password" required class="filter-control" style="width: 100%;" placeholder="Mínimo 6 caracteres">
        </div>

        <!-- Consentimiento Explícito de Datos Personales (RF-015, HV-001, RN-004, RNF-015) -->
        <div style="background: #081714; border: 1px solid #163e34; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <label style="display: flex; gap: 0.65rem; align-items: flex-start; cursor: pointer;">
                <input type="checkbox" name="consentimiento_datos" value="1" required style="margin-top: 0.25rem; accent-color: #10b981;">
                <span style="color: #94a3b8; font-size: 0.8rem; line-height: 1.45;">
                    <strong style="color: #34d399;">Autorización de Tratamiento de Datos (Ley 1581 de 2012 / CECAR):</strong>
                    Autorizo de manera previa, expresa e informada a la Corporación Universitaria del Caribe - CECAR para recolectar, almacenar y tratar mis datos personales con la única finalidad de adelantar los procesos de selección, evaluación y contratación laboral institucional (Versión v3.0-2026).
                </span>
            </label>
        </div>

        <button type="submit" class="landing-btn-primary" style="width: 100%; justify-content: center; padding: 0.75rem;">
            Registrar Cuenta de Aspirante
        </button>
    </form>

    <div style="margin-top: 1.5rem; text-align: center;">
        <span style="color: #94a3b8; font-size: 0.85rem;">¿Ya tiene una cuenta?</span>
        <a href="<?= BASE_URL ?>/index.php?c=Auth&a=login" style="color: #10b981; font-weight: 700; text-decoration: none; font-size: 0.85rem; margin-left: 0.35rem;">
            Ingresar
        </a>
    </div>
</div>
