<?php
use App\Helpers\FormatHelper;
?>

<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="color: #0b6b44; font-size: 1.5rem; font-weight: 800;">
            Plan de Trabajo de Coordinación de Selección
        </h1>
        <p style="color: #64748b; font-size: 0.88rem;">
            Actividades, responsables, plazos y seguimiento operativo por convocatoria (RF-091).
        </p>
    </div>

    <form action="<?= BASE_URL ?>/index.php" method="GET">
        <input type="hidden" name="c" value="PlanTrabajo">
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

<!-- Plan Header Card -->
<?php if (!empty($planes)): ?>
    <?php $plan = $planes[0]; ?>
    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 1.5rem; margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <h3 style="font-size: 1.15rem; font-weight: 700; color: #1e293b;">
                <?= htmlspecialchars($plan['titulo'], ENT_QUOTES, 'UTF-8') ?>
            </h3>
            <span class="badge" style="background: #10b981; color: #fff; font-weight: 700;">
                Estado: <?= strtoupper($plan['estado']) ?>
            </span>
        </div>
        <p style="color: #64748b; font-size: 0.85rem;">
            Responsable: <strong><?= htmlspecialchars($plan['nombres'] . ' ' . $plan['apellidos'], ENT_QUOTES, 'UTF-8') ?></strong> &bull;
            Periodo de Ejecución: <strong><?= FormatHelper::fechaCorta($plan['fecha_inicio']) ?> al <?= FormatHelper::fechaCorta($plan['fecha_fin']) ?></strong>
        </p>
    </div>

    <!-- Actividades del Plan -->
    <div class="table-responsive">
        <table class="cecar-table">
            <thead>
                <tr>
                    <th>Actividad / Hito</th>
                    <th>Funcionario Responsable</th>
                    <th>Fecha Límite</th>
                    <th>Estado</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($actividades)): ?>
                    <tr><td colspan="5" style="text-align: center; color: #94a3b8; padding: 2rem;">No hay actividades registradas en este plan.</td></tr>
                <?php else: ?>
                    <?php foreach ($actividades as $act): ?>
                        <tr>
                            <td><strong style="color: #1e293b;"><?= htmlspecialchars($act['actividad'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                            <td><?= htmlspecialchars($act['nombres'] . ' ' . $act['apellidos'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= FormatHelper::fechaCorta($act['fecha_limite']) ?></td>
                            <td>
                                <span class="badge" style="background: <?= ($act['estado'] === 'completada') ? '#10b981' : '#f59e0b' ?>; color: #fff;">
                                    <?= ucfirst($act['estado']) ?>
                                </span>
                            </td>
                            <td style="color: #64748b; font-size: 0.82rem;"><?= htmlspecialchars($act['observaciones'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 3rem; text-align: center;">
        <p style="color: #64748b;">No hay plan de trabajo activo registrado para esta convocatoria.</p>
    </div>
<?php endif; ?>
