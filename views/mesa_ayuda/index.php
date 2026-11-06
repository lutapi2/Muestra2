<?php
use App\Helpers\FormatHelper;
use App\Helpers\AuthHelper;
?>

<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="color: #0b6b44; font-size: 1.5rem; font-weight: 800;">
            Mesa de Ayudas Institucional y Solicitudes
        </h1>
        <p style="color: #64748b; font-size: 0.88rem;">
            Atención oportuna a inquietudes sobre convocatorias, postulación y contratación (RF-099).
        </p>
    </div>

    <button onclick="openModal('modalNuevoTicket')" class="btn btn-primary">
        <?= \App\Helpers\IconHelper::render('plus', 16) ?> Crear Solicitud de Ayuda
    </button>
</div>

<div class="table-responsive">
    <table class="cecar-table">
        <thead>
            <tr>
                <th>Ticket #</th>
                <th>Usuario</th>
                <th>Categoría</th>
                <th>Asunto</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th style="text-align: center;">Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($tickets)): ?>
                <tr><td colspan="7" style="text-align: center; color: #94a3b8; padding: 2rem;">No hay tickets de ayuda registrados.</td></tr>
            <?php else: ?>
                <?php foreach ($tickets as $t): ?>
                    <tr>
                        <td><strong>#<?= $t['id'] ?></strong></td>
                        <td>
                            <div style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($t['nombres'] . ' ' . $t['apellidos'], ENT_QUOTES, 'UTF-8') ?></div>
                            <small style="color: #64748b;"><?= htmlspecialchars($t['correo'], ENT_QUOTES, 'UTF-8') ?></small>
                        </td>
                        <td><span class="badge" style="background: #f1f5f9; color: #334155;"><?= htmlspecialchars($t['categoria'], ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td>
                            <strong><?= htmlspecialchars($t['asunto'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <p style="color: #64748b; font-size: 0.8rem; margin-top: 0.2rem;"><?= htmlspecialchars(substr($t['mensaje'], 0, 80), ENT_QUOTES, 'UTF-8') ?>...</p>
                        </td>
                        <td><?= FormatHelper::fecha($t['fecha_creacion']) ?></td>
                        <td>
                            <span class="badge" style="background: <?= ($t['estado'] === 'resuelto') ? '#10b981' : '#f59e0b' ?>; color: #fff;">
                                <?= ucfirst($t['estado']) ?>
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <button onclick="verTicket(<?= htmlspecialchars(json_encode($t), ENT_QUOTES, 'UTF-8') ?>)" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                <?= \App\Helpers\IconHelper::render('eye', 14) ?> Ver Detalle
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Nuevo Ticket -->
<div id="modalNuevoTicket" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); align-items: center; justify-content: center; z-index: 1000;">
    <div style="background: #ffffff; border-radius: 10px; max-width: 500px; width: 90%; padding: 2rem;">
        <h3 style="color: #0b6b44; font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem;">Crear Ticket de Soporte</h3>
        <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 1.25rem;">
            Describa su solicitud para que el equipo de soporte pueda asistirle.
        </p>

        <form action="<?= BASE_URL ?>/index.php?c=MesaAyuda&a=crear" method="POST">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Categoría</label>
                <select name="categoria" class="filter-control" style="width: 100%;">
                    <option value="soporte_postulacion">Dificultad con la postulación o soportes</option>
                    <option value="informacion_convocatoria">Consulta sobre perfil y requisitos de convocatoria</option>
                    <option value="contratacion_davivienda">Cuenta bancaria Davivienda o formalización</option>
                    <option value="otro">Otra consulta general</option>
                </select>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Asunto</label>
                <input type="text" name="asunto" required class="filter-control" style="width: 100%;" placeholder="Resumen breve de la consulta...">
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Descripción detallada</label>
                <textarea name="mensaje" rows="4" required class="filter-control" style="width: 100%;" placeholder="Explique su inquietud o requerimiento con claridad..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="closeModal('modalNuevoTicket')" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ver / Responder Ticket -->
<div id="modalVerTicket" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); align-items: center; justify-content: center; z-index: 1000;">
    <div style="background: #ffffff; border-radius: 10px; max-width: 550px; width: 90%; padding: 2rem;">
        <h3 id="verTicketAsunto" style="color: #0b6b44; font-size: 1.2rem; font-weight: 800; margin-bottom: 0.25rem;"></h3>
        <p id="verTicketInfo" style="color: #64748b; font-size: 0.82rem; margin-bottom: 1rem;"></p>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1rem; margin-bottom: 1.25rem; font-size: 0.88rem; color: #334155;" id="verTicketMensaje"></div>

        <div id="seccionRespuestaExistente" style="display: none; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 1rem; margin-bottom: 1.25rem;">
            <strong style="color: #065f46; font-size: 0.85rem; display: block; margin-bottom: 0.25rem;">Respuesta Institucional:</strong>
            <p id="verTicketRespuesta" style="color: #047857; font-size: 0.85rem; line-height: 1.5; margin: 0;"></p>
        </div>

        <?php if ($esGestor): ?>
            <form id="formResponderTicket" action="<?= BASE_URL ?>/index.php?c=MesaAyuda&a=responder" method="POST" style="display: none; margin-bottom: 1rem;">
                <input type="hidden" name="ticket_id" id="responderTicketId">
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Emitir Respuesta al Usuario</label>
                <textarea name="respuesta" rows="3" required class="filter-control" style="width: 100%; margin-bottom: 0.75rem;" placeholder="Escriba la respuesta oficial..."></textarea>
                <button type="submit" class="btn btn-primary btn-sm">Enviar Respuesta Oficial</button>
            </form>
        <?php endif; ?>

        <div style="text-align: right;">
            <button type="button" onclick="closeModal('modalVerTicket')" class="btn btn-secondary">Cerrar</button>
        </div>
    </div>
</div>

<script>
function verTicket(t) {
    document.getElementById('verTicketAsunto').textContent = t.asunto;
    document.getElementById('verTicketInfo').textContent = 'Solicitante: ' + t.nombres + ' ' + t.apellidos + ' (' + t.correo + ')';
    document.getElementById('verTicketMensaje').textContent = t.mensaje;

    const seccionResp = document.getElementById('seccionRespuestaExistente');
    const formResp = document.getElementById('formResponderTicket');

    if (t.respuesta) {
        seccionResp.style.display = 'block';
        document.getElementById('verTicketRespuesta').textContent = t.respuesta;
        if (formResp) formResp.style.display = 'none';
    } else {
        seccionResp.style.display = 'none';
        if (formResp) {
            formResp.style.display = 'block';
            document.getElementById('responderTicketId').value = t.id;
        }
    }

    openModal('modalVerTicket');
}
</script>
