<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="color: #0b6b44; font-size: 1.5rem; font-weight: 800;">
            Bandeja de Revisión Documental y Postulaciones
        </h1>
        <p style="color: #64748b; font-size: 0.88rem;">
            Control integral de aspirantes y verificación ítem por ítem de soportes probatorios (RF-043 a RF-053).
        </p>
    </div>
</div>

<!-- Filtros Rápidos por Estado (Pestañas) -->
<div style="display: flex; gap: 0.5rem; margin-bottom: 1.25rem; flex-wrap: wrap;">
    <a href="<?= BASE_URL ?>/index.php?c=Revision&a=index&estado=todos&convocatoria_id=<?= $convocatoriaIdFiltro ?>" 
       class="btn btn-sm <?= ($estadoFiltro === 'todos') ? 'btn-primary' : 'btn-secondary' ?>" style="font-weight: 700;">
        Todos (<?= $conteoTotal ?>)
    </a>
    <a href="<?= BASE_URL ?>/index.php?c=Revision&a=index&estado=enviadas&convocatoria_id=<?= $convocatoriaIdFiltro ?>" 
       class="btn btn-sm <?= ($estadoFiltro === 'enviadas') ? 'btn-primary' : 'btn-secondary' ?>" style="font-weight: 700;">
        Enviadas / Para Revisar (<?= $conteoEnviadas ?>)
    </a>
    <a href="<?= BASE_URL ?>/index.php?c=Revision&a=index&estado=borradores&convocatoria_id=<?= $convocatoriaIdFiltro ?>" 
       class="btn btn-sm <?= ($estadoFiltro === 'borradores') ? 'btn-primary' : 'btn-secondary' ?>" style="font-weight: 700;">
        Borradores en Diligenciamiento (<?= $conteoBorradores ?>)
    </a>
    <a href="<?= BASE_URL ?>/index.php?c=Revision&a=index&estado=habilitadas&convocatoria_id=<?= $convocatoriaIdFiltro ?>" 
       class="btn btn-sm <?= ($estadoFiltro === 'habilitadas') ? 'btn-primary' : 'btn-secondary' ?>" style="font-weight: 700;">
        Habilitadas (<?= $conteoHabilitadas ?>)
    </a>
</div>

<!-- Barra de Filtro por Convocatoria -->
<form action="<?= BASE_URL ?>/index.php" method="GET" style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
    <input type="hidden" name="c" value="Revision">
    <input type="hidden" name="a" value="index">
    <input type="hidden" name="estado" value="<?= htmlspecialchars($estadoFiltro, ENT_QUOTES, 'UTF-8') ?>">

    <label style="font-size: 0.82rem; font-weight: 700; color: #475569;">Filtrar por Vacante:</label>
    <select name="convocatoria_id" class="filter-control" style="flex: 1; min-width: 260px;" onchange="this.form.submit()">
        <option value="0">Todas las convocatorias</option>
        <?php foreach ($convocatorias as $c): ?>
            <option value="<?= $c['id'] ?>" <?= ($convocatoriaIdFiltro === (int)$c['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['codigo'] . ' - ' . $c['cargo'], ENT_QUOTES, 'UTF-8') ?>
            </option>
        <?php endforeach; ?>
    </select>

    <?php if ($convocatoriaIdFiltro > 0 || $estadoFiltro !== 'todos'): ?>
        <a href="<?= BASE_URL ?>/index.php?c=Revision&a=index" class="btn btn-secondary btn-sm">Limpiar Filtros</a>
    <?php endif; ?>
</form>

<div class="table-responsive">
    <table class="cecar-table">
        <thead>
            <tr>
                <th>Radicado</th>
                <th>Aspirante</th>
                <th>Convocatoria</th>
                <th>Facultad</th>
                <th>Ítems Soportes</th>
                <th>Estado Documental</th>
                <th>Estado Proceso</th>
                <th style="text-align: center;">Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($postulaciones)): ?>
                <tr><td colspan="8" style="text-align: center; color: #94a3b8; padding: 2.5rem;">No se encontraron postulaciones con los filtros seleccionados.</td></tr>
            <?php else: ?>
                <?php foreach ($postulaciones as $p): ?>
                    <tr>
                        <td><strong style="color: #0b6b44; font-family: monospace;"><?= htmlspecialchars($p['codigo_radicado'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                        <td>
                            <div style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($p['nombres'] . ' ' . $p['apellidos'], ENT_QUOTES, 'UTF-8') ?></div>
                            <small style="color: #64748b;">Doc: <?= htmlspecialchars($p['numero_documento'], ENT_QUOTES, 'UTF-8') ?> &bull; <?= htmlspecialchars($p['correo'], ENT_QUOTES, 'UTF-8') ?></small>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: #334155;"><?= htmlspecialchars($p['cargo'], ENT_QUOTES, 'UTF-8') ?></div>
                            <small class="badge" style="background: #e0f2fe; color: #0284c7; font-size: 0.72rem;"><?= htmlspecialchars($p['convocatoria_codigo'], ENT_QUOTES, 'UTF-8') ?></small>
                        </td>
                        <td><?= htmlspecialchars($p['facultad'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <span class="badge" style="background: #e2e8f0; color: #334155; font-weight: 700;">
                                <?= $p['aprobados'] ?>/<?= $p['total_items'] ?> aprobados
                            </span>
                        </td>
                        <td><?= FormatHelper::badgeEstado($p['resultado_documental']) ?></td>
                        <td><?= FormatHelper::badgeEstado($p['estado']) ?></td>
                        <td style="text-align: center;">
                            <a href="<?= BASE_URL ?>/index.php?c=Revision&a=expediente&id=<?= $p['id'] ?>" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.3rem;">
                                <?= IconHelper::render('search', 14) ?> Revisar Expediente
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
