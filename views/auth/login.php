<div style="max-width: 460px; margin: 3rem auto; padding: 2.25rem; background: #0d211c; border: 1px solid #16362e; border-radius: 14px; width: 100%;">
    <div style="text-align: center; margin-bottom: 2rem;">
        <div style="width: 48px; height: 48px; background: #10b981; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-weight: 800; color: #fff; font-size: 1.5rem; margin-bottom: 0.75rem;">
            C
        </div>
        <h2 style="color: #ffffff; font-size: 1.5rem; font-weight: 800;">Acceso Institucional</h2>
        <p style="color: #94a3b8; font-size: 0.85rem;">Sistema de Selección y Contratación CECAR</p>
    </div>

    <form action="<?= BASE_URL ?>/index.php?c=Auth&a=doLogin" method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div style="margin-bottom: 1.25rem;">
            <label style="display: block; color: #cbd5e1; font-size: 0.82rem; font-weight: 600; margin-bottom: 0.35rem;">Correo Electrónico</label>
            <input type="email" name="correo" required class="filter-control" style="width: 100%; padding: 0.65rem 0.85rem;" placeholder="ejemplo@cecar.edu.co">
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; color: #cbd5e1; font-size: 0.82rem; font-weight: 600; margin-bottom: 0.35rem;">Contraseña</label>
            <input type="password" name="password" required class="filter-control" style="width: 100%; padding: 0.65rem 0.85rem;" placeholder="••••••••">
        </div>

        <button type="submit" class="landing-btn-primary" style="width: 100%; justify-content: center; padding: 0.75rem;">
            Iniciar Sesión
        </button>
    </form>

    <!-- Quick Access Demo Buttons for Review -->
    <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #16362e;">
        <p style="color: #64748b; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; margin-bottom: 0.75rem; text-align: center;">
            Accesos Rápidos de Prueba (Roles)
        </p>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
            <a href="<?= BASE_URL ?>/index.php?c=Auth&a=quickLogin&rol=talento_humano" class="btn btn-secondary btn-sm" style="background: #112d26; border-color: #1a4d41; color: #a7f3d0; text-align: center; justify-content: center;">
                Talento Humano
            </a>
            <a href="<?= BASE_URL ?>/index.php?c=Auth&a=quickLogin&rol=revisor" class="btn btn-secondary btn-sm" style="background: #112d26; border-color: #1a4d41; color: #a7f3d0; text-align: center; justify-content: center;">
                Revisor Documental
            </a>
            <a href="<?= BASE_URL ?>/index.php?c=Auth&a=quickLogin&rol=candidato" class="btn btn-secondary btn-sm" style="background: #112d26; border-color: #1a4d41; color: #a7f3d0; text-align: center; justify-content: center;">
                Aspirante Seleccionado
            </a>
            <a href="<?= BASE_URL ?>/index.php?c=Auth&a=quickLogin&rol=admin" class="btn btn-secondary btn-sm" style="background: #112d26; border-color: #1a4d41; color: #a7f3d0; text-align: center; justify-content: center;">
                Administrador
            </a>
        </div>
    </div>

    <div style="margin-top: 1.5rem; text-align: center;">
        <span style="color: #94a3b8; font-size: 0.85rem;">¿No tiene cuenta de candidato?</span>
        <a href="<?= BASE_URL ?>/index.php?c=Auth&a=register" style="color: #10b981; font-weight: 700; text-decoration: none; font-size: 0.85rem; margin-left: 0.35rem;">
            Registrarse aquí
        </a>
    </div>
</div>
