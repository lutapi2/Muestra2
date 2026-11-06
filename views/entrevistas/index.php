<?php
use App\Helpers\FormatHelper;
use App\Helpers\AuthHelper;
?>

<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="color: #0b6b44; font-size: 1.5rem; font-weight: 800;">
            Programación y Evaluación de Entrevistas
        </h1>
        <p style="color: #64748b; font-size: 0.88rem;">
            Asignación de comités, control de conflictos de agenda y registro de calificaciones individuales (RF-060 a RF-072).
        </p>
    </div>

    <?php if (AuthHelper::esTalentoHumano()): ?>
        <button onclick="openModal('modalProgramarEntrevista')" class="btn btn-primary">
            <?= \App\Helpers\IconHelper::render('plus', 16) ?> Programar Nueva Entrevista
        </button>
    <?php endif; ?>
</div>

<!-- List of Interviews -->
<div class="table-responsive">
    <table class="cecar-table">
        <thead>
            <tr>
                <th>Candidato</th>
                <th>Convocatoria</th>
                <th>Fecha y Horario</th>
                <th>Modalidad</th>
                <th>Lugar / Enlace</th>
                <th>Estado</th>
                <th style="text-align: center;">Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($entrevistas)): ?>
                <tr><td colspan="7" style="text-align: center; color: #94a3b8; padding: 2rem;">No hay entrevistas programadas en este momento.</td></tr>
            <?php else: ?>
                <?php foreach ($entrevistas as $e): ?>
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($e['nombres'] . ' ' . $e['apellidos'], ENT_QUOTES, 'UTF-8') ?></div>
                            <small style="color: #64748b;">CC <?= htmlspecialchars($e['numero_documento'], ENT_QUOTES, 'UTF-8') ?></small>
                        </td>
                        <td><?= htmlspecialchars($e['cargo'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <strong><?= FormatHelper::fechaCorta($e['fecha']) ?></strong><br>
                            <span style="color: #64748b; font-size: 0.8rem;"><?= substr($e['hora_inicio'], 0, 5) ?> - <?= substr($e['hora_fin'], 0, 5) ?></span>
                        </td>
                        <td>
                            <span class="badge" style="background: <?= ($e['modalidad'] === 'virtual') ? '#e0f2fe' : '#fef3c7' ?>; color: <?= ($e['modalidad'] === 'virtual') ? '#0369a1' : '#92400e' ?>; font-weight: 700;">
                                <?= ucfirst($e['modalidad']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($e['modalidad'] === 'virtual' && !empty($e['enlace'])): ?>
                                <a href="<?= htmlspecialchars($e['enlace'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="btn btn-secondary btn-sm" style="color: #0284c7; display: inline-flex; align-items: center; gap: 0.35rem;">
                                    <?= \App\Helpers\IconHelper::render('external', 14) ?> Unirse a Reunión
                                </a>
                            <?php else: ?>
                                <span style="font-size: 0.82rem; color: #475569;"><?= htmlspecialchars($e['ubicacion'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= FormatHelper::badgeEstado($e['estado']) ?></td>
                        <td style="text-align: center;">
                            <?php if ($e['estado'] !== 'realizada'): ?>
                                <button onclick="abrirModalCalificarEntrevista(<?= $e['id'] ?>, '<?= htmlspecialchars($e['nombres'] . ' ' . $e['apellidos'], ENT_QUOTES, 'UTF-8') ?>')" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                    <?= \App\Helpers\IconHelper::render('award', 14) ?> Calificar
                                </button>
                            <?php else: ?>
                                <span style="color: #10b981; font-weight: 700; font-size: 0.82rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                                    <?= \App\Helpers\IconHelper::render('check', 14) ?> Evaluada
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Programar Entrevista (RF-063, RF-064, RN-017) -->
<div id="modalProgramarEntrevista" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); align-items: center; justify-content: center; z-index: 1000;">
    <div style="background: #ffffff; border-radius: 10px; max-width: 560px; width: 90%; padding: 2rem;">
        <h3 style="color: #0b6b44; font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem;">Programar Cita de Entrevista</h3>
        <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 1.25rem;">
            El sistema valida automáticamente que no existan conflictos de horario (RN-017).
        </p>

        <form action="<?= BASE_URL ?>/index.php?c=Entrevista&a=programar" method="POST">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Aspirante Preseleccionado</label>
                <select name="postulacion_id" required class="filter-control" style="width: 100%;">
                    <?php if (empty($candidatosDisponibles)): ?>
                        <option value="">-- No hay aspirantes preseleccionados pendientes de citar --</option>
                    <?php else: ?>
                        <?php foreach ($candidatosDisponibles as $cd): ?>
                            <option value="<?= $cd['id'] ?>">
                                <?= htmlspecialchars($cd['nombres'] . ' ' . $cd['apellidos'] . ' (' . $cd['cargo'] . ')', ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Fecha</label>
                    <input type="date" name="fecha" required value="<?= date('Y-m-d', strtotime('+2 days')) ?>" class="filter-control" style="width: 100%;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Hora Inicio</label>
                    <input type="time" name="hora_inicio" required value="09:00" class="filter-control" style="width: 100%;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Hora Fin</label>
                    <input type="time" name="hora_fin" required value="10:00" class="filter-control" style="width: 100%;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Modalidad</label>
                    <select name="modalidad" class="filter-control" style="width: 100%;">
                        <option value="virtual">Virtual (Meet / Teams)</option>
                        <option value="presencial">Presencial (Campus CECAR)</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Ubicación / Sala</label>
                    <input type="text" name="ubicacion" value="Sala de Juntas Decanatura" class="filter-control" style="width: 100%;">
                </div>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Enlace Virtual (si aplica)</label>
                <input type="url" name="enlace" value="https://meet.google.com/cecar-entrevista-docente" class="filter-control" style="width: 100%;">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="closeModal('modalProgramarEntrevista')" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Agendar y Notificar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Calificar Entrevista (RF-070) -->
<div id="modalCalificarEntrevista" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); align-items: center; justify-content: center; z-index: 1000;">
    <div style="background: #ffffff; border-radius: 10px; max-width: 500px; width: 90%; padding: 2rem;">
        <h3 style="color: #0b6b44; font-size: 1.25rem; font-weight: 800; margin-bottom: 0.25rem;">Evaluación Individual de Entrevista</h3>
        <p id="modalCalificarSub" style="color: #64748b; font-size: 0.85rem; margin-bottom: 1.25rem;"></p>

        <form action="<?= BASE_URL ?>/index.php?c=Entrevista&a=calificar" method="POST">
            <input type="hidden" name="entrevista_id" id="calificarEntrevistaId">

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Puntaje de Entrevista (0 a 100)</label>
                <input type="number" step="0.5" min="0" max="100" name="puntaje" required value="92" class="filter-control" style="width: 100%;">
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Concepto del Evaluador</label>
                <select name="concepto" class="filter-control" style="width: 100%;">
                    <option value="favorable">Favorable</option>
                    <option value="condicionado">Condicionado</option>
                    <option value="desfavorable">Desfavorable</option>
                </select>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Observaciones y Fortalezas Evidenciadas</label>
                <textarea name="observacion" rows="3" required class="filter-control" style="width: 100%;" placeholder="Concepto técnico y pedagógico..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="closeModal('modalCalificarEntrevista')" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Registrar Evaluación</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalCalificarEntrevista(id, nombre) {
    document.getElementById('calificarEntrevistaId').value = id;
    document.getElementById('modalCalificarSub').textContent = 'Aspirante: ' + nombre;
    openModal('modalCalificarEntrevista');
}
</script>
