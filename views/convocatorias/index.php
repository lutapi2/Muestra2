<?php
use App\Helpers\FormatHelper;
use App\Helpers\AuthHelper;
?>

<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="color: #0b6b44; font-size: 1.5rem; font-weight: 800;">Convocatorias Públicas Institucionales</h1>
        <p style="color: #64748b; font-size: 0.88rem;">Consulte las convocatorias de méritos vigentes para vinculación docente y administrativa en CECAR.</p>
    </div>
    <?php if ($esGestor): ?>
        <a href="<?= BASE_URL ?>/index.php?c=Convocatoria&a=crear" class="btn btn-primary">
            <?= \App\Helpers\IconHelper::render('plus', 16) ?> Crear Convocatoria
        </a>
    <?php endif; ?>
</div>

<form action="<?= BASE_URL ?>/index.php" method="GET" style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 1rem; margin-bottom: 1.5rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
    <input type="hidden" name="c" value="Convocatoria">
    <input type="hidden" name="a" value="index">

    <input type="text" name="buscar" value="<?= htmlspecialchars($filtros['buscar'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="filter-control" style="flex: 2; min-width: 200px;" placeholder="Buscar por cargo o código...">

    <input type="text" name="facultad" value="<?= htmlspecialchars($filtros['facultad'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="filter-control" style="flex: 1.5; min-width: 180px;" placeholder="Facultad o Dependencia...">

    <button type="submit" class="btn btn-secondary">Buscar</button>
    <a href="<?= BASE_URL ?>/index.php?c=Convocatoria&a=index" class="btn btn-secondary">Limpiar</a>
</form>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 1.25rem;">
    <?php if (empty($convocatorias)): ?>
        <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; background: #ffffff; border-radius: 10px; border: 1px solid var(--border-color);">
            <p style="color: #64748b;">No se encontraron convocatorias disponibles con los filtros aplicados.</p>
        </div>
    <?php else: ?>
        <?php foreach ($convocatorias as $c): ?>
            <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                        <span class="badge" style="background: #e0f2fe; color: #0284c7; font-weight: 700; font-size: 0.78rem;">
                            <?= htmlspecialchars($c['codigo'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <?= FormatHelper::badgeEstado($c['estado']) ?>
                    </div>

                    <h3 style="font-size: 1.15rem; font-weight: 700; color: #1e293b; margin-bottom: 0.4rem;">
                        <?= htmlspecialchars($c['cargo'], ENT_QUOTES, 'UTF-8') ?>
                    </h3>

                    <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 1rem;">
                        <strong>Facultad:</strong> <?= htmlspecialchars($c['facultad'], ENT_QUOTES, 'UTF-8') ?><br>
                        <strong>Modalidad:</strong> <?= htmlspecialchars($c['modalidad'], ENT_QUOTES, 'UTF-8') ?> &bull; 
                        <strong>Vacantes:</strong> <?= (int)$c['vacantes'] ?>
                    </p>

                    <div style="background: #f8fafc; border-radius: 6px; padding: 0.65rem 0.85rem; font-size: 0.8rem; color: #475569; margin-bottom: 1.25rem;">
                        <span style="display: inline-flex; align-items: center; gap: 0.4rem;"><?= \App\Helpers\IconHelper::render('calendar', 14) ?> <strong>Cierre de postulación:</strong> <?= FormatHelper::fecha($c['fecha_cierre']) ?></span>
                    </div>
                </div>

                <div style="display: flex; gap: 0.5rem; justify-content: space-between; align-items: center; pt-2;">
                    <a href="<?= BASE_URL ?>/index.php?c=Convocatoria&a=detalle&id=<?= $c['id'] ?>" class="btn btn-secondary btn-sm" style="flex: 1; text-align: center;">
                        Ver Requisitos
                    </a>
                    <a href="<?= BASE_URL ?>/index.php?c=Postulacion&a=index&convocatoria_id=<?= $c['id'] ?>" class="btn btn-primary btn-sm" style="flex: 1; text-align: center;">
                        Postularme
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
