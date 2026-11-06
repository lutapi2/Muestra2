<?php
use App\Helpers\FormatHelper;
use App\Helpers\AuthHelper;
use App\Helpers\IconHelper;
?>

<div style="margin-bottom: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="color: #0b6b44; font-size: 1.5rem; font-weight: 800;">
                Postulación y Hoja de Vida por Ítems
            </h1>
            <p style="color: #64748b; font-size: 0.88rem;">
                Convocatoria: <strong><?= htmlspecialchars($convocatoria['cargo'], ENT_QUOTES, 'UTF-8') ?></strong> &bull;
                Radicado: <strong><?= htmlspecialchars($postulacion['codigo_radicado'], ENT_QUOTES, 'UTF-8') ?></strong>
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <?= FormatHelper::badgeEstado($postulacion['estado']) ?>
            <?php if (!$esEditable): ?>
                <span class="badge" style="background: #1e293b; color: #fff; border-radius: 4px;">Bloqueada tras envío (RN-009)</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Barra de Avance de Completitud en Tiempo Real (RF-036) -->
    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 8px; padding: 1rem; margin-top: 1rem;">
        <div style="display: flex; justify-content: space-between; font-size: 0.82rem; margin-bottom: 0.35rem;">
            <strong style="color: #1e293b;">Nivel de Completitud del Expediente</strong>
            <span style="font-weight: 700; color: #10b981;"><?= (int)$postulacion['porcentaje_avance'] ?>% Completado</span>
        </div>
        <div style="height: 8px; background: #e2e8f0; border-radius: 9999px; overflow: hidden;">
            <div style="width: <?= (int)$postulacion['porcentaje_avance'] ?>%; height: 100%; background: #10b981; transition: width 0.3s ease;"></div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 340px; gap: 1.5rem; align-items: start;">
    <!-- Left Column: Form & Registered Items by Category -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <?php if ($esEditable): ?>
            <!-- Formulario de Registro de Ítem con Soporte (RF-019 a RF-032) -->
            <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.25rem;">
                    <span style="color: #10b981; display: inline-flex;">
                        <?= IconHelper::render('plus', 18) ?>
                    </span>
                    <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b;">Registrar Ítem de Hoja de Vida y Soporte</h3>
                </div>

                <form action="<?= BASE_URL ?>/index.php?c=Postulacion&a=agregarItem" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="postulacion_id" value="<?= $postulacion['id'] ?>">
                    <input type="hidden" name="convocatoria_id" value="<?= $convocatoria['id'] ?>">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Categoría Documental</label>
                            <select name="categoria" required class="filter-control" style="width: 100%;">
                                <option value="formacion_academica">Formación Académica (Pregrado, Posgrado, Acta)</option>
                                <option value="experiencia_laboral">Experiencia Laboral o Docente</option>
                                <option value="seguridad_social">Seguridad Social (EPS, Pensión)</option>
                                <option value="informacion_bancaria">Información Bancaria (Davivienda)</option>
                                <option value="salud_ocupacional">Salud Ocupacional (Examen Médico)</option>
                                <option value="antecedentes_legales">Antecedentes Legales (Procuraduría, etc.)</option>
                                <option value="informacion_personal">Información Personal y Cédula</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Tipo de Registro</label>
                            <input type="text" name="tipo_item" required class="filter-control" style="width: 100%;" placeholder="Ej: Titulo_Pregrado, Certificado_Laboral">
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Título / Descripción del Ítem</label>
                        <input type="text" name="titulo" required class="filter-control" style="width: 100%;" placeholder="Ej: Licenciatura en Pedagogía - Universidad de Sucre (2018)">
                    </div>

                    <!-- Carga de soporte probatorio independiente (RF-032, RF-034) -->
                    <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 1rem; margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
                            Adjuntar Soporte Probatorio (PDF, JPG o PNG - Máx. 10MB)
                        </label>
                        <input type="file" name="archivo" accept=".pdf,.jpg,.jpeg,.png" class="filter-control" style="width: 100%; background: #ffffff;">
                        <small style="color: #64748b; font-size: 0.75rem; display: block; margin-top: 0.25rem;">
                            Cada ítem debe respaldarse de manera individual con su certificación o diploma correspondiente (RN-006).
                        </small>
                    </div>

                    <div style="display: flex; justify-content: flex-end;">
                        <button type="submit" class="btn btn-primary" style="font-weight: 700;">
                            <span>Guardar Ítem y Soporte</span>
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Lista de Ítems Registrados por Categoría -->
        <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 1.5rem;">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem;">Ítems y Soportes Registrados en este Expediente</h3>

            <?php foreach ($itemsPorCategoria as $catKey => $catData): ?>
                <?php if (!empty($catData['items'])): ?>
                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="font-size: 0.9rem; font-weight: 700; color: #0b6b44; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.35rem; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.4rem;">
                            <?= IconHelper::render('folder', 15) ?>
                            <span><?= htmlspecialchars($catData['titulo'], ENT_QUOTES, 'UTF-8') ?></span>
                        </h4>
                        <div style="display: flex; flex-direction: column; gap: 0.65rem;">
                            <?php foreach ($catData['items'] as $item): ?>
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.85rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                                    <div>
                                        <div style="font-weight: 600; color: #1e293b; font-size: 0.88rem;">
                                            <?= htmlspecialchars($item['titulo'], ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                        <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.15rem;">
                                            Tipo: <?= htmlspecialchars($item['tipo_item'], ENT_QUOTES, 'UTF-8') ?> &bull; 
                                            Registrado: <?= FormatHelper::fecha($item['fecha_registro']) ?>
                                        </div>
                                    </div>

                                    <div style="display: flex; align-items: center; gap: 0.6rem;">
                                        <?= FormatHelper::badgeEstado($item['estado']) ?>
                                        <?php if (!empty($item['soporte_id'])): ?>
                                            <a href="<?= BASE_URL ?>/index.php?c=Descarga&a=ver&id=<?= $item['soporte_id'] ?>" target="_blank" class="btn btn-secondary btn-sm" title="Ver archivo">
                                                <?= IconHelper::render('file-text', 13) ?>
                                                <span><?= htmlspecialchars(substr($item['nombre_original'], 0, 18), ENT_QUOTES, 'UTF-8') ?>...</span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Right Column: Final Submission & Veracity Declaration -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <?php if ($esEditable): ?>
            <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 1.5rem; box-shadow: 0 1px 4px rgba(0,0,0,0.03);">
                <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">Envío Definitivo de Postulación</h3>
                <p style="color: #64748b; font-size: 0.82rem; margin-bottom: 1.25rem;">
                    Una vez enviada, su postulación quedará bloqueada para revisión por parte de Gestión de Talento Humano (RN-009).
                </p>

                <form action="<?= BASE_URL ?>/index.php?c=Postulacion&a=enviarDefinitivo" method="POST" onsubmit="return confirm('¿Está seguro de radicar su postulación definitiva?');">
                    <input type="hidden" name="postulacion_id" value="<?= $postulacion['id'] ?>">

                    <!-- Declaración de Veracidad (HV-022, RN-008) -->
                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 0.85rem; border-radius: 6px; margin-bottom: 1rem;">
                        <label style="display: flex; gap: 0.5rem; align-items: flex-start; cursor: pointer;">
                            <input type="checkbox" name="declaracion_veracidad" value="1" required style="margin-top: 0.2rem; accent-color: #10b981;">
                            <span style="color: #166534; font-size: 0.78rem; line-height: 1.4;">
                                <strong>Declaración de Veracidad (HV-022):</strong> Manifiesto bajo gravedad de juramento que toda la información consignada y sus soportes son auténticos y veraces.
                            </span>
                        </label>
                    </div>

                    <!-- Consentimiento Banco de Talento (RF-076, RN-021) -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.85rem; border-radius: 6px; margin-bottom: 1.25rem;">
                        <label style="display: flex; gap: 0.5rem; align-items: flex-start; cursor: pointer;">
                            <input type="checkbox" name="banco_talento_consentimiento" value="1" style="margin-top: 0.2rem; accent-color: #10b981;">
                            <span style="color: #475569; font-size: 0.78rem; line-height: 1.4;">
                                Autorizo expresamente la inclusión de mi perfil en el <strong>Banco de Candidatos Elegibles</strong> de CECAR para futuras convocatorias (RF-076).
                            </span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.75rem; font-weight: 700;">
                        <span>Radicar Postulación Definitiva</span>
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 1.5rem;">
                <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">Postulación Radicada</h3>
                <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 1rem;">
                    Su postulación fue confirmada el <?= FormatHelper::fecha($postulacion['fecha_envio']) ?>.
                </p>
                <a href="<?= BASE_URL ?>/index.php?c=Postulacion&a=constancia&id=<?= $postulacion['id'] ?>" class="btn btn-secondary" style="width: 100%; justify-content: center;">
                    <?= IconHelper::render('file-text', 14) ?>
                    <span>Ver Constancia de Radicación</span>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
