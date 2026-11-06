<?php
use App\Helpers\IconHelper;
?>
<div style="max-width: 460px; margin: 3rem auto; padding: 2.25rem; background: #0d211c; border: 1px solid #16362e; border-radius: 14px; width: 100%;">
    <div style="text-align: center; margin-bottom: 2rem;">
        <div style="width: 48px; height: 48px; background: #10b981; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-weight: 800; color: #fff; font-size: 1.5rem; margin-bottom: 0.75rem;">
            C
        </div>
        <h2 style="color: #ffffff; font-size: 1.5rem; font-weight: 800;">Recuperar Contraseña</h2>
        <p style="color: #94a3b8; font-size: 0.85rem;">Sistema de Selección y Contratación CECAR</p>
    </div>

    <p style="color: #cbd5e1; font-size: 0.85rem; line-height: 1.5; margin-bottom: 1.5rem;">
        Ingrese su correo electrónico institucional o registrado. Si existe en nuestra base de datos, le enviaremos las instrucciones para restablecer su acceso de manera segura.
    </p>

    <form action="<?= BASE_URL ?>/index.php?c=Auth&a=doRecuperar" method="POST" id="recuperarForm">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; color: #cbd5e1; font-size: 0.82rem; font-weight: 600; margin-bottom: 0.35rem;">Correo Electrónico</label>
            <input type="email" name="correo" required class="filter-control" style="width: 100%; padding: 0.65rem 0.85rem;" placeholder="ejemplo@cecar.edu.co">
        </div>

        <button type="submit" class="landing-btn-primary" style="width: 100%; justify-content: center; padding: 0.75rem;">
            <?= IconHelper::render('lock', 16) ?>
            <span style="margin-left: 0.5rem;">Enviar Instrucciones</span>
        </button>
    </form>

    <div style="margin-top: 2rem; text-align: center; border-top: 1px solid #16362e; padding-top: 1.25rem;">
        <a href="<?= BASE_URL ?>/index.php?c=Auth&a=login" style="color: #34d399; font-weight: 700; text-decoration: none; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.35rem;">
            <?= IconHelper::render('arrow-left', 14) ?>
            <span>Volver al Inicio de Sesión</span>
        </a>
    </div>
</div>
