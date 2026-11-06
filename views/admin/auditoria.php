<?php
use App\Helpers\FormatHelper;
?>

<div style="margin-bottom: 1.5rem;">
    <h1 style="color: #0b6b44; font-size: 1.5rem; font-weight: 800;">
        Bitácora de Auditoría y Seguridad del Sistema
    </h1>
    <p style="color: #64748b; font-size: 0.88rem;">
        Registro inmutable de acciones críticas, modificaciones y accesos (RNF-019, RN-020).
    </p>
</div>

<div class="table-responsive">
    <table class="cecar-table">
        <thead>
            <tr>
                <th>Fecha y Hora</th>
                <th>Usuario</th>
                <th>Acción</th>
                <th>Entidad Afectada</th>
                <th>ID Registro</th>
                <th>IP Origen</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($registros)): ?>
                <tr><td colspan="6" style="text-align: center; color: #94a3b8; padding: 2rem;">No hay registros de auditoría recientes.</td></tr>
            <?php else: ?>
                <?php foreach ($registros as $reg): ?>
                    <tr>
                        <td><strong><?= FormatHelper::fecha($reg['fecha_hora']) ?></strong></td>
                        <td>
                            <strong style="color: #1e293b;"><?= htmlspecialchars(($reg['nombres'] ?? 'Anónimo') . ' ' . ($reg['apellidos'] ?? ''), ENT_QUOTES, 'UTF-8') ?></strong>
                            <small style="color: #64748b; display: block;"><?= htmlspecialchars($reg['correo'] ?? '—', ENT_QUOTES, 'UTF-8') ?></small>
                        </td>
                        <td><span class="badge" style="background: #e0f2fe; color: #0284c7; font-weight: 700;"><?= htmlspecialchars($reg['accion'], ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td><?= htmlspecialchars($reg['entidad'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>#<?= htmlspecialchars($reg['entidad_id'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td style="font-family: monospace; font-size: 0.8rem; color: #64748b;"><?= htmlspecialchars($reg['ip'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
