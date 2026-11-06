<?php
use App\Helpers\FormatHelper;
use App\Helpers\AuthHelper;
?>

<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="color: #0b6b44; font-size: 1.5rem; font-weight: 800;">
            Matriz de Evaluación, Pruebas y Decisiones
        </h1>
        <p style="color: #64748b; font-size: 0.88rem;">
            Calificación de pruebas psicotécnicas, de aula docente y comités con dictamen obligatorio (RF-054 a RF-058, RF-087 a RF-093).
        </p>
    </div>

    <!-- Selector de Convocatoria -->
    <form action="<?= BASE_URL ?>/index.php" method="GET" style="display: flex; gap: 0.5rem; align-items: center;">
        <input type="hidden" name="c" value="Evaluacion">
        <input type="hidden" name="a" value="matriz">
        <select name="convocatoria_id" class="filter-control" onchange="this.form.submit()">
            <?php foreach ($convocatorias as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($convocatoriaId == $c['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['codigo'] . ' - ' . $c['cargo'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<!-- Criterios de Evaluación y Ponderaciones -->
<div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 1.25rem; margin-bottom: 1.5rem;">
    <h3 style="font-size: 0.95rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">
        Criterios Parametrizados de la Convocatoria (RN-013)
    </h3>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <?php foreach ($criterios as $crit): ?>
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 0.5rem 0.85rem; font-size: 0.82rem;">
                <strong><?= htmlspecialchars($crit['nombre'], ENT_QUOTES, 'UTF-8') ?>:</strong>
                <span style="color: #10b981; font-weight: 700;"><?= $crit['ponderacion'] ?>%</span> (Máx <?= $crit['puntaje_maximo'] ?> pts)
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Tabla de Postulaciones y Calificaciones -->
<div class="table-responsive">
    <table class="cecar-table">
        <thead>
            <tr>
                <th>Aspirante</th>
                <th>Estado Actual</th>
                <th>Doc. Conforme</th>
                <th>Prueba Psicotécnica</th>
                <th>Prueba de Aula Docente</th>
                <th>Entrevista Comité</th>
                <th>Acciones Decisorias (RN-015, RN-024)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($postulaciones)): ?>
                <tr><td colspan="7" style="text-align: center; color: #94a3b8; padding: 2rem;">No hay candidatos postulados para evaluar en esta convocatoria.</td></tr>
            <?php else: ?>
                <?php foreach ($postulaciones as $p): ?>
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($p['nombres'] . ' ' . $p['apellidos'], ENT_QUOTES, 'UTF-8') ?></div>
                            <small style="color: #64748b;">Rad: <?= htmlspecialchars($p['codigo_radicado'], ENT_QUOTES, 'UTF-8') ?></small>
                        </td>

                        <td><?= FormatHelper::badgeEstado($p['estado']) ?></td>

                        <td>
                            <span class="badge" style="background: <?= ($p['resultado_documental'] === 'conforme') ? '#10b981' : '#f59e0b' ?>; color: #fff;">
                                <?= ucfirst($p['resultado_documental']) ?>
                            </span>
                        </td>

                        <td>
                            <?php if ($p['nota_psicotecnica'] !== null): ?>
                                <strong style="color: #059669;"><?= number_format($p['nota_psicotecnica'], 1) ?> pts</strong>
                            <?php else: ?>
                                <button onclick="abrirModalPrueba(<?= $p['id'] ?>, 'psicotecnica', '<?= htmlspecialchars($p['nombres'] . ' ' . $p['apellidos'], ENT_QUOTES, 'UTF-8') ?>')" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">
                                    ➕ Registrar
                                </button>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ($p['nota_aula'] !== null): ?>
                                <strong style="color: #059669;"><?= number_format($p['nota_aula'], 1) ?> pts</strong>
                            <?php else: ?>
                                <button onclick="abrirModalPrueba(<?= $p['id'] ?>, 'aula_docente', '<?= htmlspecialchars($p['nombres'] . ' ' . $p['apellidos'], ENT_QUOTES, 'UTF-8') ?>')" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">
                                    ➕ Evaluar Aula
                                </button>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ($p['nota_entrevista'] !== null): ?>
                                <strong style="color: #059669;"><?= number_format($p['nota_entrevista'], 1) ?> pts</strong>
                            <?php else: ?>
                                <span style="color: #94a3b8; font-size: 0.82rem;">Pendiente</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                                <!-- Preseleccionar (RF-057) -->
                                <form action="<?= BASE_URL ?>/index.php?c=Evaluacion&a=tomarDecision" method="POST" style="display: inline;">
                                    <input type="hidden" name="postulacion_id" value="<?= $p['id'] ?>">
                                    <input type="hidden" name="convocatoria_id" value="<?= $convocatoriaId ?>">
                                    <input type="hidden" name="decision" value="preseleccionar">
                                    <input type="hidden" name="fundamento" value="Cumple con los requisitos para entrevista institucional.">
                                    <button type="submit" class="btn btn-secondary btn-sm" title="Preseleccionar para entrevista" style="background: #ede9fe; color: #6d28d9; border-color: #ddd6fe;">
                                        ⭐ Preseleccionar
                                    </button>
                                </form>

                                <!-- Seleccionar (RF-073) -->
                                <form action="<?= BASE_URL ?>/index.php?c=Evaluacion&a=tomarDecision" method="POST" style="display: inline;" onsubmit="return confirm('¿Confirma la SELECCIÓN FINAL de este candidato? Pasará al módulo de contratación.');">
                                    <input type="hidden" name="postulacion_id" value="<?= $p['id'] ?>">
                                    <input type="hidden" name="convocatoria_id" value="<?= $convocatoriaId ?>">
                                    <input type="hidden" name="decision" value="seleccionar">
                                    <input type="hidden" name="fundamento" value="Aprobación unánime en todas las fases del proceso de selección.">
                                    <button type="submit" class="btn btn-success btn-sm" title="Seleccionar para vinculación" style="font-weight: 700;">
                                        ✔️ Seleccionar
                                    </button>
                                </form>

                                <!-- Descartar con causal (RF-058, RF-093, RN-024) -->
                                <button onclick="abrirModalDescarte(<?= $p['id'] ?>, '<?= htmlspecialchars($p['nombres'] . ' ' . $p['apellidos'], ENT_QUOTES, 'UTF-8') ?>')" class="btn btn-danger btn-sm" title="Descartar con causal">
                                    ❌ Descartar
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Registrar Prueba (RF-087, RF-088, RF-089) -->
<div id="modalPrueba" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); align-items: center; justify-content: center; z-index: 1000;">
    <div style="background: #ffffff; border-radius: 10px; max-width: 500px; width: 90%; padding: 1.75rem;">
        <h3 id="modalPruebaTitulo" style="color: #0b6b44; font-size: 1.2rem; font-weight: 800; margin-bottom: 0.5rem;">Registrar Calificación</h3>
        <p id="modalPruebaSub" style="color: #64748b; font-size: 0.85rem; margin-bottom: 1rem;"></p>

        <form action="<?= BASE_URL ?>/index.php?c=Evaluacion&a=registrarPrueba" method="POST">
            <input type="hidden" name="postulacion_id" id="pruebaPostulacionId">
            <input type="hidden" name="convocatoria_id" value="<?= $convocatoriaId ?>">
            <input type="hidden" name="tipo" id="pruebaTipo">

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Calificación Obtenida (0 a 100)</label>
                <input type="number" step="0.1" min="0" max="100" name="calificacion" required class="filter-control" style="width: 100%;" value="85">
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Dictamen</label>
                <select name="dictamen" class="filter-control" style="width: 100%;">
                    <option value="aprobado">Aprobado</option>
                    <option value="no_aprobado">No Aprobado</option>
                </select>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Observaciones Técnicas</label>
                <textarea name="observaciones" rows="3" class="filter-control" style="width: 100%;" placeholder="Observaciones de la rúbrica o prueba..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="closeModal('modalPrueba')" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Calificación</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Descartar con Causal (RF-093, RN-024) -->
<div id="modalDescarte" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); align-items: center; justify-content: center; z-index: 1000;">
    <div style="background: #ffffff; border-radius: 10px; max-width: 520px; width: 90%; padding: 1.75rem; border-top: 5px solid #ef4444;">
        <h3 style="color: #991b1b; font-size: 1.2rem; font-weight: 800; margin-bottom: 0.25rem;">Descartar Candidato</h3>
        <p id="modalDescarteSub" style="color: #64748b; font-size: 0.85rem; margin-bottom: 1rem;"></p>

        <form action="<?= BASE_URL ?>/index.php?c=Evaluacion&a=tomarDecision" method="POST">
            <input type="hidden" name="postulacion_id" id="descartePostulacionId">
            <input type="hidden" name="convocatoria_id" value="<?= $convocatoriaId ?>">
            <input type="hidden" name="decision" value="descartar">

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #991b1b; margin-bottom: 0.25rem;">
                    Causal Oficial de Descarte (Obligatorio RN-024)
                </label>
                <select name="causal_descarte_id" required class="filter-control" style="width: 100%;">
                    <option value="">-- Seleccione una causal del catálogo --</option>
                    <?php foreach ($causales as $caus): ?>
                        <option value="<?= $caus['codigo'] ?>"><?= htmlspecialchars($caus['codigo'] . ': ' . $caus['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Fundamento / Motivo Detallado</label>
                <textarea name="fundamento" required rows="3" class="filter-control" style="width: 100%;" placeholder="Explicación fundamentada de la decisión..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="closeModal('modalDescarte')" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-danger">Confirmar Descarte</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalPrueba(postulacionId, tipo, nombre) {
    document.getElementById('pruebaPostulacionId').value = postulacionId;
    document.getElementById('pruebaTipo').value = tipo;
    document.getElementById('modalPruebaTitulo').textContent = 'Evaluar ' + (tipo === 'aula_docente' ? 'Prueba de Aula Docente' : 'Prueba Psicotécnica');
    document.getElementById('modalPruebaSub').textContent = 'Aspirante: ' + nombre;
    openModal('modalPrueba');
}

function abrirModalDescarte(postulacionId, nombre) {
    document.getElementById('descartePostulacionId').value = postulacionId;
    document.getElementById('modalDescarteSub').textContent = 'Aspirante: ' + nombre;
    openModal('modalDescarte');
}
</script>
