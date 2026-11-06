<?php
use App\Helpers\FormatHelper;
?>

<div style="margin-bottom: 1.5rem;">
    <h1 style="color: #0b6b44; font-size: 1.5rem; font-weight: 800;">
        Bandeja de Revisión Documental de Postulaciones
    </h1>
    <p style="color: #64748b; font-size: 0.88rem;">
        Verificación ítem por ítem de datos frente a sus soportes probatorios adjuntos (RF-043 a RF-053).
    </p>
</div>

<div class="table-responsive">
    <table class="cecar-table">
        <thead>
            <tr>
                <th>Radicado</th>
                <th>Aspirante</th>
                <th>Convocatoria</th>
                <th>Facultad</th>
                <th>Ítems</th>
                <th>Estado Documental</th>
                <th>Estado Proceso</th>
                <th style="text-align: center;">Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($postulaciones)): ?>
                <tr><td colspan="8" style="text-align: center; color: #94a3b8; padding: 2rem;">No hay postulaciones en espera de revisión.</td></tr>
            <?php else: ?>
                <?php foreach ($postulaciones as $p): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($p['codigo_radicado'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                        <td>
                            <div style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($p['nombres'] . ' ' . $p['apellidos'], ENT_QUOTES, 'UTF-8') ?></div>
                            <small style="color: #64748b;">CC <?= htmlspecialchars($p['numero_documento'], ENT_QUOTES, 'UTF-8') ?></small>
                        </td>
                        <td><?= htmlspecialchars($p['cargo'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($p['facultad'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <span class="badge" style="background: #e2e8f0; color: #334155; font-weight: 700;">
                                <?= $p['aprobados'] ?>/<?= $p['total_items'] ?> aprobados
                            </span>
                        </td>
                        <td><?= FormatHelper::badgeEstado($p['resultado_documental']) ?></td>
                        <td><?= FormatHelper::badgeEstado($p['estado']) ?></td>
                        <td style="text-align: center;">
                            <a href="<?= BASE_URL ?>/index.php?c=Revision&a=expediente&id=<?= $p['id'] ?>" class="btn btn-primary btn-sm">
                                🔍 Revisar Expediente
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
