<?php
use App\Helpers\IconHelper;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/theme.css">
    <script src="<?= BASE_URL ?>/js/tutorial.js" defer></script>
    <script src="<?= BASE_URL ?>/js/loading.js" defer></script>
</head>
<body class="landing-body">
    <!-- Landing Navigation -->
    <header class="landing-nav">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="width: 38px; height: 38px; background: #10b981; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #fff; font-size: 1.25rem;">
                C
            </div>
            <div>
                <h1 style="font-size: 1.15rem; font-weight: 800; color: #ffffff; letter-spacing: -0.01em;">CECAR</h1>
                <span style="font-size: 0.72rem; color: #64748b; display: block;">Sistema de Selección y Contratación</span>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 1rem;">
            <a href="javascript:void(0)" onclick="toggleTutorialModal()" style="color: #94a3b8; font-size: 0.88rem; text-decoration: none; font-weight: 600;">
                Tutorial
            </a>
            <a href="<?= BASE_URL ?>/index.php?c=Auth&a=login" class="landing-btn-outline" style="padding: 0.45rem 1rem; font-size: 0.88rem;">
                Iniciar sesión
            </a>
        </div>
    </header>

    <main style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem 1rem;">
        <?php $flashes = \App\Helpers\AuthHelper::getFlashes(); ?>
        <?php if (!empty($flashes)): ?>
            <div style="max-width: 560px; width: 100%; margin: 0 auto 1.5rem auto;">
                <?php foreach ($flashes as $flash): 
                    $bg = '#112d26'; $border = '#10b981'; $color = '#a7f3d0';
                    if ($flash['tipo'] === 'danger') { $bg = '#451a1a'; $border = '#ef4444'; $color = '#fca5a5'; }
                    if ($flash['tipo'] === 'warning') { $bg = '#452a10'; $border = '#f59e0b'; $color = '#fde68a'; }
                    if ($flash['tipo'] === 'info') { $bg = '#1e293b'; $border = '#38bdf8'; $color = '#bae6fd'; }
                ?>
                    <div style="background: <?= $bg ?>; border: 1px solid <?= $border ?>; color: <?= $color ?>; padding: 0.85rem 1.25rem; border-radius: 8px; font-size: 0.88rem; margin-bottom: 0.75rem; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
                        <?= htmlspecialchars($flash['mensaje'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </main>

    <!-- Floating Tutorial Pill -->
    <a href="javascript:void(0)" onclick="toggleTutorialModal()" class="tutorial-pill-floating">
        <?= IconHelper::render('help-circle', 16) ?>
        <span>Tutorial</span>
    </a>
</body>
</html>
