<?php
use App\Helpers\FormatHelper;
use App\Helpers\AuthHelper;
?>

<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
    <div>
        <a href="<?= BASE_URL ?>/index.php?c=Revision&a=index" style="color: #10b981; font-size: 0.88rem; font-weight: 700; text-decoration: none;">
            &larr; Volver a Bandeja de Revisión
        </a>
        <h1 style="color: #0b6b44; font-size: 1.5rem; font-weight: 800; margin-top: 0.25rem;">
            Verificación Documental Lado a Lado
        </h1>
        <p style="color: #64748b; font-size: 0.88rem;">
            Aspirante: <strong><?= htmlspecialchars($postulacion['nombres'] . ' ' . $postulacion['apellidos'], ENT_QUOTES, 'UTF-8') ?></strong> &bull;
            Radicado: <strong><?= htmlspecialchars($postulacion['codigo_radicado'], ENT_QUOTES, 'UTF-8') ?></strong> &bull;
            Cargo: <strong><?= htmlspecialchars($postulacion['convocatoria_cargo'], ENT_QUOTES, 'UTF-8') ?></strong>
        </p>
    </div>

    <!-- Counters -->
    <div style="display: flex; gap: 0.5rem; align-items: center;">
        <span class="badge" style="background: #10b981; color: #fff; padding: 0.4rem 0.75rem;">✔️ <?= $aprobados ?> Aprobados</span>
        <span class="badge" style="background: #f59e0b; color: #fff; padding: 0.4rem 0.75rem;">⏳ <?= $pendientes ?> Pendientes</span>
        <span class="badge" style="background: #f97316; color: #fff; padding: 0.4rem 0.75rem;">⚠️ <?= $observados ?> Observados</span>
        <span class="badge" style="background: #ef4444; color: #fff; padding: 0.4rem 0.75rem;">❌ <?= $rechazados ?> Rechazados</span>
    </div>
</div>

<!-- Modal or Form to Request Subsanación if observations exist (RF-048) -->
<?php if ($observados > 0): ?>
    <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 1.25rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h4 style="color: #92400e; font-weight: 800; font-size: 1rem;">⚠️ Existen <?= $observados ?> ítem(s) con observación pendiente</h4>
            <p style="color: #b45309; font-size: 0.85rem;">Puede emitir formalmente la solicitud de subsanación hacia el candidato con plazo límite institucional.</p>
        </div>

        <form action="<?= BASE_URL ?>/index.php?c=Revision&a=solicitarSubsanacion" method="POST" style="display: flex; gap: 0.75rem; align-items: center;">
            <input type="hidden" name="postulacion_id" value="<?= $postulacion['id'] ?>">
            <label style="font-size: 0.82rem; font-weight: 600; color: #92400e;">Plazo (días):</label>
            <input type="number" name="dias_limite" value="5" min="1" max="15" class="filter-control" style="width: 70px;">
            <button type="submit" class="btn btn-warning" style="background: #d97706; color: #fff; font-weight: 700;">
                📢 Enviar Solicitud de Subsanación
            </button>
        </form>
    </div>
<?php endif; ?>

<!-- Side-by-side List of Items -->
<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <?php foreach ($itemsPorCategoria as $catKey => $catData): ?>
        <?php if (!empty($catData['items'])): ?>
            <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 1.5rem;">
                <h3 style="font-size: 1.05rem; font-weight: 700; color: #0b6b44; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.4rem; margin-bottom: 1rem;">
                    📁 <?= htmlspecialchars($catData['titulo'], ENT_QUOTES, 'UTF-8') ?>
                </h3>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach ($catData['items'] as $item): ?>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.25rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <!-- Column 1: Declared Data -->
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                    <h4 style="font-size: 0.95rem; font-weight: 700; color: #1e293b;"><?= htmlspecialchars($item['titulo'], ENT_QUOTES, 'UTF-8') ?></h4>
                                    <?= FormatHelper::badgeEstado($item['estado']) ?>
                                </div>
                                <div style="font-size: 0.8rem; color: #64748b; margin-bottom: 0.75rem;">
                                    <strong>Tipo:</strong> <?= htmlspecialchars($item['tipo_item'], ENT_QUOTES, 'UTF-8') ?> &bull; 
                                    <strong>Versión:</strong> <?= $item['version'] ?> &bull;
                                    <strong>Registrado:</strong> <?= FormatHelper::fecha($item['fecha_registro']) ?>
                                </div>
                                
                                <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0.75rem; font-size: 0.82rem; font-family: monospace; max-height: 120px; overflow-y: auto;">
                                    <?= htmlspecialchars($item['datos_json'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </div>

                            <!-- Column 2: Attached Support & Review Controls (RF-045, RF-046, RF-047) -->
                            <div style="border-left: 1px solid #e2e8f0; padding-left: 1.5rem; display: flex; flex-direction: column; justify-content: space-between;">
                                <div>
                                    <strong style="display: block; font-size: 0.82rem; color: #1e293b; margin-bottom: 0.35rem;">Soporte Probatorio Adjunto:</strong>
                                    <?php if (!empty($item['soporte_id'])): ?>
                                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                            <a href="<?= BASE_URL ?>/index.php?c=Descarga&a=ver&id=<?= $item['soporte_id'] ?>" target="_blank" class="btn btn-secondary btn-sm" style="background: #ecfdf5; border-color: #a7f3d0; color: #047857; font-weight: 700;">
                                                📄 Visualizar Documento (<?= htmlspecialchars($item['nombre_original'], ENT_QUOTES, 'UTF-8') ?>)
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: #ef4444; font-size: 0.82rem; font-weight: 600;">⚠️ Sin archivo adjunto</span>
                                    <?php endif; ?>

                                    <?php if (!empty($item['revision_motivo'])): ?>
                                        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 0.5rem; font-size: 0.78rem; color: #991b1b; margin-bottom: 0.75rem;">
                                            <strong>Motivo registrado:</strong> <?= htmlspecialchars($item['revision_motivo'], ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Review Decision Buttons -->
                                <form action="<?= BASE_URL ?>/index.php?c=Revision&a=calificarItem" method="POST" style="margin-top: 0.75rem;">
                                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                    <input type="hidden" name="postulacion_id" value="<?= $postulacion['id'] ?>">

                                    <div style="margin-bottom: 0.5rem;">
                                        <input type="text" name="motivo" placeholder="Motivo u observación de verificación..." class="filter-control" style="width: 100%; font-size: 0.8rem;">
                                    </div>

                                    <div style="display: flex; gap: 0.5rem;">
                                        <button type="submit" name="resultado" value="aprobado" class="btn btn-success btn-sm" style="flex: 1;">
                                            ✔️ Aprobar
                                        </button>
                                        <button type="submit" name="resultado" value="observado" class="btn btn-warning btn-sm" style="flex: 1; background: #f59e0b; color: #fff;">
                                            ⚠️ Observar
                                        </button>
                                        <button type="submit" name="resultado" value="rechazado" class="btn btn-danger btn-sm" style="flex: 1;">
                                            ❌ Rechazar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
