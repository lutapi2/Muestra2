<?php
use App\Helpers\FormatHelper;
?>

<div style="max-width: 800px; margin: 1.5rem auto; background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 2.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #0b6b44; padding-bottom: 1.25rem; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="width: 44px; height: 44px; background: #0b6b44; color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.35rem;">
                C
            </div>
            <div>
                <h2 style="font-size: 1.15rem; font-weight: 800; color: #0b6b44; margin: 0;">CECAR</h2>
                <span style="font-size: 0.75rem; color: #64748b;">Corporación Universitaria del Caribe</span>
            </div>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 0.78rem; color: #64748b;">Código Oficial de Radicación</div>
            <strong style="font-size: 1.15rem; color: #0b6b44;"><?= htmlspecialchars($postulacion['codigo_radicado'], ENT_QUOTES, 'UTF-8') ?></strong>
        </div>
    </div>

    <div style="text-align: center; margin-bottom: 2rem;">
        <h3 style="font-size: 1.35rem; font-weight: 800; color: #1e293b; margin-bottom: 0.25rem;">
            Constancia Oficial de Postulación Institucional
        </h3>
        <p style="color: #64748b; font-size: 0.88rem;">Dirección de Gestión de Talento Humano &bull; Selección de Personal</p>
    </div>

    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.25rem; margin-bottom: 1.75rem;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.88rem;">
            <div><strong>Aspirante:</strong> <?= htmlspecialchars($postulacion['nombres'] . ' ' . $postulacion['apellidos'], ENT_QUOTES, 'UTF-8') ?></div>
            <div><strong>Documento de Identidad:</strong> <?= htmlspecialchars($postulacion['tipo_documento'] . ' ' . $postulacion['numero_documento'], ENT_QUOTES, 'UTF-8') ?></div>
            <div><strong>Convocatoria:</strong> <?= htmlspecialchars($postulacion['convocatoria_cargo'], ENT_QUOTES, 'UTF-8') ?></div>
            <div><strong>Facultad:</strong> <?= htmlspecialchars($postulacion['facultad'], ENT_QUOTES, 'UTF-8') ?></div>
            <div><strong>Fecha y Hora de Radicación:</strong> <?= FormatHelper::fecha($postulacion['fecha_envio'] ?? $postulacion['fecha_postulacion']) ?></div>
            <div><strong>Estado del Expediente:</strong> <?= strtoupper($postulacion['estado']) ?></div>
        </div>
    </div>

    <div style="margin-bottom: 2rem;">
        <h4 style="font-size: 0.95rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.35rem;">
            Resumen de Ítems y Soportes Recibidos (<?= count($items) ?> Documentos)
        </h4>
        <ul style="list-style: none; font-size: 0.85rem; color: #475569; display: flex; flex-direction: column; gap: 0.4rem;">
            <?php foreach ($items as $item): ?>
                <li style="display: flex; justify-content: space-between; padding: 0.35rem 0.5rem; background: #fdfdfd; border-radius: 4px; align-items: center;">
                    <span style="display: inline-flex; align-items: center; gap: 0.4rem; color: #10b981;"><?= \App\Helpers\IconHelper::render('check', 14) ?> <span style="color: #334155;"><?= htmlspecialchars($item['titulo'], ENT_QUOTES, 'UTF-8') ?></span></span>
                    <span style="color: #64748b; font-size: 0.78rem;"><?= htmlspecialchars($item['nombre_original'] ?? 'Sin archivo adjunto', ENT_QUOTES, 'UTF-8') ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div style="border-top: 1px solid #e2e8f0; padding-top: 1.25rem; font-size: 0.75rem; color: #94a3b8; text-align: center; margin-bottom: 2rem;">
        Este documento certifica la radicación electrónica en el Sistema de Gestión y Contratación CECAR conforme a la Ley 527 de 1999 sobre comercio electrónico y firmas digitales.
    </div>

    <div style="display: flex; justify-content: center; gap: 1rem;">
        <button onclick="window.print()" class="btn btn-primary" style="padding: 0.6rem 1.5rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem;">
            <?= \App\Helpers\IconHelper::render('document', 16) ?> Imprimir Constancia / Guardar PDF
        </button>
        <a href="<?= BASE_URL ?>/index.php?c=Dashboard&a=index" class="btn btn-secondary" style="padding: 0.6rem 1.5rem;">
            Volver al Dashboard
        </a>
    </div>
</div>
