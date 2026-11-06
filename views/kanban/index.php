<?php
use App\Helpers\FormatHelper;
?>

<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="color: #0b6b44; font-size: 1.5rem; font-weight: 800;">
            Tablero Kanban de Flujo de Selección
        </h1>
        <p style="color: #64748b; font-size: 0.88rem;">
            Monitoreo ágil de aspirantes por etapas del proceso de selección (RF-092).
        </p>
    </div>

    <form action="<?= BASE_URL ?>/index.php" method="GET">
        <input type="hidden" name="c" value="Kanban">
        <input type="hidden" name="a" value="index">
        <select name="convocatoria_id" class="filter-control" onchange="this.form.submit()">
            <?php foreach ($convocatorias as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($convocatoriaId == $c['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['codigo'] . ' - ' . $c['cargo'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<!-- Kanban Columns Container -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; align-items: start;">
    <?php foreach ($columnas as $colKey => $col): ?>
        <div style="background: #f1f5f9; border-radius: 10px; border: 1px solid #e2e8f0; overflow: hidden;">
            <div style="background: #e2e8f0; padding: 0.75rem 1rem; display: flex; justify-content: space-between; align-items: center; border-top: 4px solid <?= $col['badge'] ?>;">
                <strong style="font-size: 0.85rem; color: #1e293b;"><?= htmlspecialchars($col['titulo'], ENT_QUOTES, 'UTF-8') ?></strong>
                <span style="background: #ffffff; color: #334155; font-size: 0.72rem; font-weight: 800; padding: 0.15rem 0.45rem; border-radius: 9999px;">
                    <?= count($col['items']) ?>
                </span>
            </div>

            <div style="padding: 0.75rem; display: flex; flex-direction: column; gap: 0.75rem; min-height: 250px;">
                <?php if (empty($col['items'])): ?>
                    <div style="color: #94a3b8; font-size: 0.78rem; text-align: center; padding: 2rem 0;">Sin aspirantes</div>
                <?php else: ?>
                    <?php foreach ($col['items'] as $item): ?>
                        <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 8px; padding: 0.85rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                            <div style="font-weight: 700; color: #1e293b; font-size: 0.88rem; margin-bottom: 0.2rem;">
                                <?= htmlspecialchars($item['nombres'] . ' ' . $item['apellidos'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.5rem;">
                                Rad: <?= htmlspecialchars($item['codigo_radicado'], ENT_QUOTES, 'UTF-8') ?>
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <?= FormatHelper::badgeEstado($item['estado']) ?>
                                <a href="<?= BASE_URL ?>/index.php?c=Kanban&a=lineaTiempo&id=<?= $item['id'] ?>" class="btn btn-secondary btn-sm" style="padding: 0.2rem 0.5rem; font-size: 0.72rem; display: inline-flex; align-items: center; gap: 0.25rem;" title="Ver Línea de Tiempo">
                                    <?= \App\Helpers\IconHelper::render('clock', 12) ?> Trazabilidad
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
