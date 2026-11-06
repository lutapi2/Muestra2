<div style="margin-bottom: 1.5rem;">
    <h1 style="color: #0b6b44; font-size: 1.5rem; font-weight: 800;">
        Catálogos del Sistema y Causales Oficiales
    </h1>
    <p style="color: #64748b; font-size: 0.88rem;">
        Parámetros institucionales, causales de descarte y categorías documentales (RF-080, RN-024).
    </p>
</div>

<div class="table-responsive">
    <table class="cecar-table">
        <thead>
            <tr>
                <th>Grupo</th>
                <th>Código</th>
                <th>Nombre / Descripción</th>
                <th>Estado</th>
                <th>Orden</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($catalogos as $cat): ?>
                <tr>
                    <td><span class="badge" style="background: #f1f5f9; color: #334155; font-weight: 700;"><?= htmlspecialchars($cat['grupo'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td><strong><?= htmlspecialchars($cat['codigo'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                    <td>
                        <div style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($cat['nombre'], ENT_QUOTES, 'UTF-8') ?></div>
                        <small style="color: #64748b;"><?= htmlspecialchars($cat['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
                    </td>
                    <td>
                        <span class="badge" style="background: <?= ($cat['activo'] == 1) ? '#10b981' : '#ef4444' ?>; color: #fff;">
                            <?= ($cat['activo'] == 1) ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td><?= $cat['orden'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
