<?php
use App\Helpers\FormatHelper;
?>

<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem;">
        <a href="<?= BASE_URL ?>/index.php?c=Kanban&a=index" style="color: #10b981; font-size: 0.88rem; font-weight: 700; text-decoration: none;">
            &larr; Volver al Tablero Kanban
        </a>
        <h1 style="color: #0b6b44; font-size: 1.5rem; font-weight: 800; margin-top: 0.25rem;">
            Línea de Tiempo y Trazabilidad del Candidato
        </h1>
        <p style="color: #64748b; font-size: 0.88rem;">
            Aspirante: <strong><?= htmlspecialchars($postulacion['nombres'] . ' ' . $postulacion['apellidos'], ENT_QUOTES, 'UTF-8') ?></strong> &bull;
            Radicado: <strong><?= htmlspecialchars($postulacion['codigo_radicado'], ENT_QUOTES, 'UTF-8') ?></strong>
        </p>
    </div>

    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 2rem;">
        <div style="position: relative; padding-left: 2rem; border-left: 3px solid #10b981; margin-left: 1rem; display: flex; flex-direction: column; gap: 2rem;">
            <?php if (empty($movimientos)): ?>
                <p style="color: #64748b; font-size: 0.88rem;">No se han registrado eventos históricos para esta postulación.</p>
            <?php else: ?>
                <?php foreach ($movimientos as $m): ?>
                    <div style="position: relative;">
                        <!-- Timeline Dot -->
                        <div style="position: absolute; left: -2.65rem; top: 0; width: 18px; height: 18px; border-radius: 50%; background: #10b981; border: 3px solid #ffffff; box-shadow: 0 0 0 2px #10b981;"></div>

                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.25rem;">
                            <strong style="color: #1e293b; font-size: 0.95rem; text-transform: capitalize;">
                                <?= htmlspecialchars(str_replace('_', ' ', $m['accion']), ENT_QUOTES, 'UTF-8') ?>
                            </strong>
                            <span style="font-size: 0.78rem; color: #64748b;"><?= FormatHelper::fecha($m['fecha_hora']) ?></span>
                        </div>

                        <div style="font-size: 0.82rem; color: #64748b; margin-bottom: 0.5rem;">
                            Responsable: <strong><?= htmlspecialchars(($m['nombres'] ?? 'Sistema') . ' ' . ($m['apellidos'] ?? ''), ENT_QUOTES, 'UTF-8') ?></strong>
                            <?php if (!empty($m['cargo_interno'])): ?>
                                (<?= htmlspecialchars($m['cargo_interno'], ENT_QUOTES, 'UTF-8') ?>)
                            <?php endif; ?>
                        </div>

                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 0.75rem; font-size: 0.85rem; color: #334155;">
                            <?= htmlspecialchars($m['detalle'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
