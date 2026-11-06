<?php
use App\Helpers\IconHelper;
?>
<div class="landing-hero">
    <!-- Left Column: Copy & Actions -->
    <div>
        <div class="landing-pill-new">
            Nueva plataforma
        </div>

        <h1 class="landing-title">
            Toda tu <span>gestión</span><br>
            administrativa<br>
            en un solo lugar
        </h1>

        <p class="landing-subtitle">
            Una plataforma integral para administrar los procesos de la institución de forma ágil, segura y con trazabilidad total. Centraliza, controla y decide mejor.
        </p>

        <div class="landing-actions">
            <a href="<?= BASE_URL ?>/index.php?c=Auth&a=login" class="landing-btn-primary">
                <?= IconHelper::render('lock', 16) ?>
                <span>Iniciar sesión</span>
            </a>
            <a href="<?= BASE_URL ?>/index.php?c=Home&a=comoFunciona" class="landing-btn-outline">
                <?= IconHelper::render('play', 16) ?>
                <span>Ver cómo funciona</span>
            </a>
        </div>
    </div>

    <!-- Right Column: Interactive Status Preview Card (Matching Screenshot) -->
    <div>
        <div class="landing-preview-window">
            <div class="preview-window-header">
                <span class="window-dot dot-red"></span>
                <span class="window-dot dot-yellow"></span>
                <span class="window-dot dot-green"></span>
            </div>

            <div class="preview-window-content">
                <!-- Status 1: Aprobado -->
                <div class="preview-status-item">
                    <div class="item-info">
                        <span style="background: #064e3b; color: #34d399; padding: 0.35rem 0.5rem; border-radius: 6px; display: inline-flex;">
                            <?= IconHelper::render('check', 14) ?>
                        </span>
                        <div style="height: 8px; width: 140px; background: #133a30; border-radius: 4px;"></div>
                    </div>
                    <span style="background: #065f46; color: #34d399; padding: 0.2rem 0.65rem; border-radius: 4px; font-size: 0.78rem; font-weight: 700;">
                        Aprobado
                    </span>
                </div>

                <!-- Status 2: En proceso -->
                <div class="preview-status-item">
                    <div class="item-info">
                        <span style="background: #451a03; color: #f59e0b; padding: 0.35rem 0.5rem; border-radius: 6px; display: inline-flex;">
                            <?= IconHelper::render('clock', 14) ?>
                        </span>
                        <div style="height: 8px; width: 180px; background: #133a30; border-radius: 4px;"></div>
                    </div>
                    <span style="background: #78350f; color: #fcd34d; padding: 0.2rem 0.65rem; border-radius: 4px; font-size: 0.78rem; font-weight: 700;">
                        En proceso
                    </span>
                </div>

                <!-- Status 3: Firmado -->
                <div class="preview-status-item">
                    <div class="item-info">
                        <span style="background: #064e3b; color: #34d399; padding: 0.35rem 0.5rem; border-radius: 6px; display: inline-flex;">
                            <?= IconHelper::render('edit', 14) ?>
                        </span>
                        <div style="height: 8px; width: 120px; background: #133a30; border-radius: 4px;"></div>
                    </div>
                    <span style="background: #065f46; color: #34d399; padding: 0.2rem 0.65rem; border-radius: 4px; font-size: 0.78rem; font-weight: 700;">
                        Firmado
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
