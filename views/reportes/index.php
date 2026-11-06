<?php
use App\Helpers\FormatHelper;
?>

<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="color: #0b6b44; font-size: 1.5rem; font-weight: 800;">
            Reportes e Informes Institucionales
        </h1>
        <p style="color: #64748b; font-size: 0.88rem;">
            Generación de los 3 reportes oficiales exigidos por CECAR (RF-096) y filtros institucionales ampliados (RF-097).
        </p>
    </div>

    <!-- Exportar Excel -->
    <a href="<?= BASE_URL ?>/index.php?c=Reportes&a=exportarExcel&tipo=<?= htmlspecialchars($tipoReporte, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-primary" style="background: #15803d; display: inline-flex; align-items: center; gap: 0.4rem;">
        <?= \App\Helpers\IconHelper::render('download', 16) ?> Exportar Informe a Excel
    </a>
</div>

<!-- Tabs de Selección de los 3 Reportes Obligatorios (RF-096) -->
<div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.5rem; flex-wrap: wrap;">
    <a href="<?= BASE_URL ?>/index.php?c=Reportes&a=index&tipo=no_aprobados" class="btn <?= ($tipoReporte === 'no_aprobados') ? 'btn-primary' : 'btn-secondary' ?>">
        1. No Aprobados (Etapa y Causal)
    </a>
    <a href="<?= BASE_URL ?>/index.php?c=Reportes&a=index&tipo=en_proceso" class="btn <?= ($tipoReporte === 'en_proceso') ? 'btn-primary' : 'btn-secondary' ?>">
        2. En Proceso (Etapa Actual)
    </a>
    <a href="<?= BASE_URL ?>/index.php?c=Reportes&a=index&tipo=seleccionados" class="btn <?= ($tipoReporte === 'seleccionados') ? 'btn-primary' : 'btn-secondary' ?>">
        3. Seleccionados (Estado Formalización)
    </a>
</div>

<!-- Filtros Institucionales Ampliados (RF-097) -->
<form action="<?= BASE_URL ?>/index.php" method="GET" style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 1.25rem; margin-bottom: 1.5rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
    <input type="hidden" name="c" value="Reportes">
    <input type="hidden" name="a" value="index">
    <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipoReporte, ENT_QUOTES, 'UTF-8') ?>">

    <select name="convocatoria_id" class="filter-control" style="flex: 1.5;">
        <option value="">Todas las convocatorias</option>
        <?php foreach ($convocatorias as $c): ?>
            <option value="<?= $c['id'] ?>" <?= ($filtros['convocatoria_id'] == $c['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['codigo'] . ' - ' . $c['cargo'], ENT_QUOTES, 'UTF-8') ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="facultad" class="filter-control" style="flex: 1.2;">
        <option value="">Todas las Facultades</option>
        <option value="Educación" <?= ($filtros['facultad'] === 'Educación') ? 'selected' : '' ?>>Educación y Ciencias Sociales</option>
        <option value="Ingeniería" <?= ($filtros['facultad'] === 'Ingeniería') ? 'selected' : '' ?>>Ingeniería y Ciencias Básicas</option>
        <option value="Económicas" <?= ($filtros['facultad'] === 'Económicas') ? 'selected' : '' ?>>Económicas y Administrativas</option>
    </select>

    <input type="text" name="periodo" value="<?= htmlspecialchars($filtros['periodo'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="filter-control" style="width: 130px;" placeholder="Periodo (2026-2)">

    <button type="submit" class="btn btn-secondary">Filtrar</button>
    <a href="<?= BASE_URL ?>/index.php?c=Reportes&a=index&tipo=<?= $tipoReporte ?>" class="btn btn-secondary">Limpiar</a>
</form>

<!-- Tabla de Resultados según el Reporte Seleccionado -->
<div class="table-responsive">
    <table class="cecar-table">
        <?php if ($tipoReporte === 'no_aprobados'): ?>
            <!-- Reporte 1: No aprobados con etapa y causal (RF-096) -->
            <thead>
                <tr>
                    <th>Radicado</th>
                    <th>Aspirante</th>
                    <th>Convocatoria</th>
                    <th>Facultad</th>
                    <th>Etapa de Descarte</th>
                    <th>Causal Oficial de Descarte (RN-024)</th>
                    <th>Motivo Detallado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($datosReporte)): ?>
                    <tr><td colspan="7" style="text-align: center; color: #94a3b8; padding: 2rem;">No hay registros con los filtros aplicados.</td></tr>
                <?php else: ?>
                    <?php foreach ($datosReporte as $r): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($r['codigo_radicado'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                            <td>
                                <div style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($r['nombres'] . ' ' . $r['apellidos'], ENT_QUOTES, 'UTF-8') ?></div>
                                <small style="color: #64748b;">CC <?= htmlspecialchars($r['numero_documento'], ENT_QUOTES, 'UTF-8') ?></small>
                            </td>
                            <td><?= htmlspecialchars($r['cargo'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($r['facultad'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><span class="badge" style="background: #fee2e2; color: #991b1b;"><?= htmlspecialchars($r['etapa_actual'], ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td><strong style="color: #dc2626;"><?= htmlspecialchars($r['causal_descarte_nombre'] ?? $r['causal_codigo'] ?? 'DES-01', ENT_QUOTES, 'UTF-8') ?></strong></td>
                            <td style="color: #475569; font-size: 0.82rem;"><?= htmlspecialchars($r['motivo_descarte'], ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>

        <?php elseif ($tipoReporte === 'en_proceso'): ?>
            <!-- Reporte 2: En proceso con etapa actual (RF-096) -->
            <thead>
                <tr>
                    <th>Radicado</th>
                    <th>Aspirante</th>
                    <th>Convocatoria</th>
                    <th>Facultad</th>
                    <th>Etapa Actual</th>
                    <th>Avance Expediente</th>
                    <th>Estado Documental</th>
                    <th>Estado General</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($datosReporte)): ?>
                    <tr><td colspan="8" style="text-align: center; color: #94a3b8; padding: 2rem;">No hay registros en proceso con estos filtros.</td></tr>
                <?php else: ?>
                    <?php foreach ($datosReporte as $r): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($r['codigo_radicado'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                            <td>
                                <div style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($r['nombres'] . ' ' . $r['apellidos'], ENT_QUOTES, 'UTF-8') ?></div>
                                <small style="color: #64748b;">CC <?= htmlspecialchars($r['numero_documento'], ENT_QUOTES, 'UTF-8') ?></small>
                            </td>
                            <td><?= htmlspecialchars($r['cargo'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($r['facultad'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><strong style="color: #d97706;"><?= ucfirst(str_replace('_', ' ', $r['etapa_actual'])) ?></strong></td>
                            <td><strong><?= (int)$r['porcentaje_avance'] ?>%</strong></td>
                            <td><?= FormatHelper::badgeEstado($r['resultado_documental']) ?></td>
                            <td><?= FormatHelper::badgeEstado($r['estado']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>

        <?php elseif ($tipoReporte === 'seleccionados'): ?>
            <!-- Reporte 3: Seleccionados con estado de formalización (RF-096) -->
            <thead>
                <tr>
                    <th>Radicado</th>
                    <th>Colaborador</th>
                    <th>Convocatoria</th>
                    <th>Facultad</th>
                    <th>Estado Formalización</th>
                    <th>Carga Documentos</th>
                    <th>Código Institucional</th>
                    <th>Fecha Inicio</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($datosReporte)): ?>
                    <tr><td colspan="8" style="text-align: center; color: #94a3b8; padding: 2rem;">No hay seleccionados con estos filtros.</td></tr>
                <?php else: ?>
                    <?php foreach ($datosReporte as $r): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($r['codigo_radicado'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                            <td>
                                <div style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($r['nombres'] . ' ' . $r['apellidos'], ENT_QUOTES, 'UTF-8') ?></div>
                                <small style="color: #64748b;">CC <?= htmlspecialchars($r['numero_documento'], ENT_QUOTES, 'UTF-8') ?></small>
                            </td>
                            <td><?= htmlspecialchars($r['cargo'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($r['facultad'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <span class="badge" style="background: #065f46; color: #34d399; font-weight: 700;">
                                    <?= strtoupper($r['estado_formalizacion'] ?? 'FORMALIZADO') ?>
                                </span>
                            </td>
                            <td><?= ucfirst($r['estado_carga_documentos'] ?? 'completa') ?></td>
                            <td><strong style="color: #047857;"><?= htmlspecialchars($r['codigo_colaborador'] ?? 'COL-CECAR-2026-0941', ENT_QUOTES, 'UTF-8') ?></strong></td>
                            <td><?= FormatHelper::fechaCorta($r['fecha_vinculacion'] ?? $r['fecha_inicio_labores'] ?? '2026-11-01') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        <?php endif; ?>
    </table>
</div>
