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

    <main style="flex: 1; display: flex; align-items: center;">
        <?= $content ?? '' ?>
    </main>

    <!-- Floating Tutorial Pill -->
    <a href="javascript:void(0)" onclick="toggleTutorialModal()" class="tutorial-pill-floating">
        <?= IconHelper::render('help-circle', 16) ?>
        <span>Tutorial</span>
    </a>
</body>
</html>
