<?php
use App\Helpers\FormatHelper;
?>

<div style="max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem;">
        <a href="<?= BASE_URL ?>/index.php?c=Dashboard&a=index" style="color: #10b981; font-size: 0.88rem; font-weight: 700; text-decoration: none;">
            &larr; Volver al Dashboard
        </a>
        <h1 style="color: #991b1b; font-size: 1.5rem; font-weight: 800; margin-top: 0.25rem;">
            ⚠️ Subsanación Requerida de Documentos
        </h1>
        <p style="color: #64748b; font-size: 0.88rem;">
            Postulación: <strong><?= htmlspecialchars($postulacion['codigo_radicado'], ENT_QUOTES, 'UTF-8') ?></strong> &bull;
            Cargo: <strong><?= htmlspecialchars($postulacion['convocatoria_cargo'], ENT_QUOTES, 'UTF-8') ?></strong>
        </p>
    </div>

    <!-- Banner de Alerta con Fecha Límite (RF-051) -->
    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem;">
        <h3 style="color: #991b1b; font-size: 1rem; font-weight: 700; margin-bottom: 0.25rem;">
            Plazo Límite de Respuesta: <?= FormatHelper::fecha($solicitud['fecha_limite'] ?? date('Y-m-d 23:59:59')) ?>
        </h3>
        <p style="color: #7f1d1d; font-size: 0.85rem; margin-bottom: 0.5rem;">
            Observación general del revisor: <?= htmlspecialchars($solicitud['observaciones'] ?? 'Por favor adjunte soportes legibles y actualizados según lo requerido.', ENT_QUOTES, 'UTF-8') ?>
        </p>
        <span style="font-size: 0.78rem; color: #b91c1c; font-weight: 600;">
            * Conforme a la Regla de Negocio RN-011, solo se habilitan para edición los ítems expresamente observados por Talento Humano. El resto del expediente permanece bloqueado.
        </span>
    </div>

    <!-- Lista de Ítems Observados para Corregir -->
    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
        <?php if (empty($itemsObservados)): ?>
            <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 2.5rem; text-align: center;">
                <p style="color: #10b981; font-weight: 700; font-size: 1.1rem;">🎉 ¡Todos los requerimientos de subsanación han sido respondidos!</p>
                <p style="color: #64748b; font-size: 0.88rem; margin-top: 0.5rem;">Su expediente ha vuelto a la bandeja de revisión de Talento Humano.</p>
                <a href="<?= BASE_URL ?>/index.php?c=Dashboard&a=index" class="btn btn-primary" style="margin-top: 1rem;">Ir al Dashboard</a>
            </div>
        <?php else: ?>
            <?php foreach ($itemsObservados as $obs): ?>
                <div style="background: #ffffff; border: 1px solid #fca5a5; border-radius: 10px; padding: 1.5rem; box-shadow: 0 1px 4px rgba(239,68,68,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                        <div>
                            <span class="badge" style="background: #fee2e2; color: #991b1b; font-weight: 700; margin-bottom: 0.35rem;">
                                ÍTEM OBSERVADO: <?= strtoupper($obs['categoria']) ?>
                            </span>
                            <h3 style="font-size: 1.1rem; font-weight: 700; color: #1e293b;"><?= htmlspecialchars($obs['titulo'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <p style="color: #dc2626; font-size: 0.85rem; font-weight: 600; margin-top: 0.25rem;">
                                ⚠️ Motivo de la observación: <?= htmlspecialchars($obs['motivo_observacion'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                    </div>

                    <form action="<?= BASE_URL ?>/index.php?c=Postulacion&a=guardarSubsanacion" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="item_id" value="<?= $obs['id'] ?>">
                        <input type="hidden" name="postulacion_id" value="<?= $postulacion['id'] ?>">

                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Actualizar Título / Información</label>
                            <input type="text" name="titulo" value="<?= htmlspecialchars($obs['titulo'], ENT_QUOTES, 'UTF-8') ?>" required class="filter-control" style="width: 100%;">
                        </div>

                        <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
                                📎 Adjuntar Nuevo Archivo Soporte Corregido (PDF, JPG, PNG)
                            </label>
                            <input type="file" name="nuevo_soporte" required accept=".pdf,.jpg,.jpeg,.png" class="filter-control" style="width: 100%; background: #ffffff;">
                        </div>

                        <button type="submit" class="btn btn-primary" style="font-weight: 700;">
                            📤 Enviar Corrección de este Ítem
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
