<?php
use App\Helpers\FormatHelper;
?>

<div style="margin-bottom: 1.5rem;">
    <h1 style="color: #0b6b44; font-size: 1.5rem; font-weight: 800;">
        Gestión de Usuarios y Roles Institucionales
    </h1>
    <p style="color: #64748b; font-size: 0.88rem;">
        Administración de credenciales, roles asignados y dependencias (RF-004, RF-005).
    </p>
</div>

<div class="table-responsive">
    <table class="cecar-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>Documento</th>
                <th>Correo Institucional</th>
                <th>Rol</th>
                <th>Dependencia</th>
                <th>Último Acceso</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td>#<?= $u['id'] ?></td>
                    <td>
                        <strong style="color: #1e293b;"><?= htmlspecialchars($u['nombres'] . ' ' . $u['apellidos'], ENT_QUOTES, 'UTF-8') ?></strong>
                        <?php if (!empty($u['cargo_interno'])): ?>
                            <div style="font-size: 0.75rem; color: #64748b;"><?= htmlspecialchars($u['cargo_interno'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($u['tipo_documento'] . ' ' . $u['numero_documento'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($u['correo'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <span class="badge" style="background: #e0f2fe; color: #0369a1; font-weight: 700;">
                            <?= htmlspecialchars($u['rol_nombre'] ?? $u['rol'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($u['dependencia_nombre'] ?? 'Sin dependencia', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= FormatHelper::fecha($u['ultimo_acceso']) ?></td>
                    <td>
                        <span class="badge" style="background: <?= ($u['activo'] == 1) ? '#10b981' : '#ef4444' ?>; color: #fff;">
                            <?= ($u['activo'] == 1) ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
