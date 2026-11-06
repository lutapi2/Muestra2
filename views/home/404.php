<div style="max-width: 500px; margin: 4rem auto; text-align: center; background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 3rem;">
    <div style="display: flex; justify-content: center; margin-bottom: 1rem; color: #94a3b8;">
        <?= \App\Helpers\IconHelper::render('search', 48) ?>
    </div>
    <h2 style="color: #1e293b; font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem;">Página no encontrada</h2>
    <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 1.5rem;">
        <?= htmlspecialchars($mensaje ?? 'El recurso o módulo solicitado no existe o no se encuentra disponible.', ENT_QUOTES, 'UTF-8') ?>
    </p>
    <a href="<?= BASE_URL ?>/" class="btn btn-primary">Volver al Inicio</a>
</div>
