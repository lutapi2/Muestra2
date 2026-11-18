<div style="margin-bottom: 1.5rem;">
    <a href="<?= BASE_URL ?>/index.php?c=Convocatoria&a=index" style="color: #10b981; font-size: 0.88rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
        <?= IconHelper::render('arrow-left', 14) ?> Volver al listado de convocatorias
    </a>
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-top: 0.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="color: #0b6b44; font-size: 1.6rem; font-weight: 800;">
                <?= htmlspecialchars($convocatoria['cargo'], ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p style="color: #64748b; font-size: 0.9rem;">
                Código: <strong><?= htmlspecialchars($convocatoria['codigo'], ENT_QUOTES, 'UTF-8') ?></strong> &bull;
                Facultad: <strong><?= htmlspecialchars($convocatoria['facultad'], ENT_QUOTES, 'UTF-8') ?></strong> &bull;
                Sede: <strong><?= htmlspecialchars($convocatoria['sede'], ENT_QUOTES, 'UTF-8') ?></strong>
                <?php if (!empty($convocatoria['programa'])): ?>
                    &bull; Programa: <strong><?= htmlspecialchars($convocatoria['programa'], ENT_QUOTES, 'UTF-8') ?></strong>
                <?php endif; ?>
            </p>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <?= FormatHelper::badgeEstado($convocatoria['estado']) ?>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <!-- Left Column: Cargo Profile & Requirements -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- General Description -->
        <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 1.5rem;">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem;">Descripción del Cargo</h3>
            <p style="color: #475569; font-size: 0.92rem; line-height: 1.6;">
                <?= nl2br(htmlspecialchars($convocatoria['descripcion'], ENT_QUOTES, 'UTF-8')) ?>
            </p>
        </div>

        <!-- Cargo Profile (RF-007) -->
        <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 1.5rem;">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem;">Perfil Requerido</h3>

            <div style="margin-bottom: 1rem;">
                <strong style="color: #1e293b; font-size: 0.88rem; display: block; margin-bottom: 0.2rem;">Formación Académica Exigida:</strong>
                <p style="color: #475569; font-size: 0.88rem;"><?= htmlspecialchars($convocatoria['formacion_minima'] ?? 'Título profesional afín al cargo', ENT_QUOTES, 'UTF-8') ?></p>
            </div>

            <div style="margin-bottom: 1rem;">
                <strong style="color: #1e293b; font-size: 0.88rem; display: block; margin-bottom: 0.2rem;">Experiencia Mínima:</strong>
                <p style="color: #475569; font-size: 0.88rem;"><?= (int)($convocatoria['experiencia_minima_meses'] ?? 12) ?> meses acreditables con certificación laboral o docente.</p>
            </div>

            <div style="margin-bottom: 1rem;">
                <strong style="color: #1e293b; font-size: 0.88rem; display: block; margin-bottom: 0.2rem;">Competencias Institucionales:</strong>
                <p style="color: #475569; font-size: 0.88rem;"><?= htmlspecialchars($convocatoria['competencias'] ?? 'Ética, pedagogía, compromiso institucional.', ENT_QUOTES, 'UTF-8') ?></p>
            </div>

            <div>
                <strong style="color: #1e293b; font-size: 0.88rem; display: block; margin-bottom: 0.2rem;">Funciones Principales:</strong>
                <p style="color: #475569; font-size: 0.88rem;"><?= nl2br(htmlspecialchars($convocatoria['funciones'] ?? 'Desarrollo de labores académicas y curriculares.', ENT_QUOTES, 'UTF-8')) ?></p>
            </div>
        </div>

        <!-- Requisitos Exigidos -->
        <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 1.5rem;">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem;">Requisitos Documentales Exigidos</h3>
            <div class="table-responsive">
                <table class="cecar-table">
                    <thead>
                        <tr>
                            <th>Categoría</th>
                            <th>Requisito Documental</th>
                            <th>Obligatoriedad</th>
                            <th>Soporte en PDF</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($requisitos)): ?>
                            <tr><td colspan="4" style="text-align: center; color: #94a3b8;">Requisitos documentales institucionales estándar aplicables.</td></tr>
                        <?php else: ?>
                            <?php foreach ($requisitos as $rq): ?>
                                <tr>
                                    <td><span class="badge" style="background: #f1f5f9; color: #334155; font-weight: 700;"><?= ucfirst($rq['categoria']) ?></span></td>
                                    <td><?= htmlspecialchars($rq['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><span class="badge" style="background: #e0f2fe; color: #0369a1;"><?= ucfirst($rq['obligatoriedad']) ?></span></td>
                                    <td><?= ($rq['soporte_requerido'] == 1) ? '<span style="color: #10b981; font-weight: 700;">Requerido</span>' : 'Opcional' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Evaluation Criteria (RF-009) -->
        <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 1.5rem;">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem;">Criterios de Evaluación y Ponderaciones</h3>
            <div class="table-responsive">
                <table class="cecar-table">
                    <thead>
                        <tr>
                            <th>Etapa</th>
                            <th>Criterio Evaluativo</th>
                            <th>Puntaje Máximo</th>
                            <th>Ponderación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($criterios)): ?>
                            <tr><td colspan="4" style="text-align: center; color: #94a3b8;">Criterios estándar institucionales aplicables.</td></tr>
                        <?php else: ?>
                            <?php foreach ($criterios as $cr): ?>
                                <tr>
                                    <td><span class="badge" style="background: #f1f5f9; color: #334155; font-weight: 700;"><?= ucfirst($cr['etapa']) ?></span></td>
                                    <td><?= htmlspecialchars($cr['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= $cr['puntaje_maximo'] ?> pts</td>
                                    <td><strong><?= $cr['ponderacion'] ?>%</strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Summary Card & Action -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 1.5rem; box-shadow: 0 1px 4px rgba(0,0,0,0.03);">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem;">Condiciones del Proceso</h3>

            <div style="font-size: 0.85rem; color: #475569; display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem;">
                <div><strong>Periodo:</strong> <?= htmlspecialchars($convocatoria['periodo_academico'], ENT_QUOTES, 'UTF-8') ?></div>
                <div><strong>Modalidad:</strong> <?= htmlspecialchars($convocatoria['modalidad'], ENT_QUOTES, 'UTF-8') ?></div>
                <div><strong>Vacantes:</strong> <?= (int)$convocatoria['vacantes'] ?> cupo(s)</div>
                <div><strong>Apertura:</strong> <?= FormatHelper::fecha($convocatoria['fecha_apertura']) ?></div>
                <div><strong>Cierre Oficial:</strong> <span style="color: #ef4444; font-weight: 700;"><?= FormatHelper::fecha($convocatoria['fecha_cierre']) ?></span></div>
            </div>

            <a href="<?= BASE_URL ?>/index.php?c=Postulacion&a=index&convocatoria_id=<?= $convocatoria['id'] ?>" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.75rem; font-weight: 700;">
                <?= IconHelper::render('document', 18) ?> Diligenciar Postulación
            </a>
        </div>

        <?php if ($esGestor): ?>
            <!-- Panel de Administración para Gestores -->
            <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 10px; padding: 1.5rem; box-shadow: 0 1px 4px rgba(0,0,0,0.03);">
                <h3 style="font-size: 1rem; font-weight: 800; color: #0b6b44; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.4rem;">
                    <?= IconHelper::render('shield', 16) ?> Gestión de la Vacante
                </h3>

                <!-- Cambiar Estado -->
                <form action="<?= BASE_URL ?>/index.php?c=Convocatoria&a=cambiarEstado" method="POST" style="margin-bottom: 1.25rem;">
                    <input type="hidden" name="id" value="<?= $convocatoria['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem;">Cambiar Estado:</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <select name="estado" class="filter-control" style="flex: 1;">
                            <option value="publicada" <?= ($convocatoria['estado'] === 'publicada') ? 'selected' : '' ?>>Publicada</option>
                            <option value="borrador" <?= ($convocatoria['estado'] === 'borrador') ? 'selected' : '' ?>>Borrador</option>
                            <option value="cerrada" <?= ($convocatoria['estado'] === 'cerrada') ? 'selected' : '' ?>>Cerrada</option>
                            <option value="retirada" <?= ($convocatoria['estado'] === 'retirada') ? 'selected' : '' ?>>Retirada</option>
                        </select>
                        <button type="submit" class="btn btn-secondary btn-sm" style="font-weight: 700;">Aplicar</button>
                    </div>
                </form>

                <div style="display: flex; flex-direction: column; gap: 0.5rem; border-top: 1px solid #f1f5f9; padding-top: 1rem;">
                    <a href="<?= BASE_URL ?>/index.php?c=Convocatoria&a=editar&id=<?= $convocatoria['id'] ?>" class="btn btn-secondary" style="width: 100%; justify-content: center;">
                        <?= IconHelper::render('edit', 14) ?> Editar Datos de la Vacante
                    </a>

                    <form action="<?= BASE_URL ?>/index.php?c=Convocatoria&a=eliminar" method="POST" onsubmit="return confirm('¿Está absolutamente seguro de eliminar para siempre esta convocatoria (<?= htmlspecialchars($convocatoria['codigo'], ENT_QUOTES, 'UTF-8') ?>)? Se borrarán permanentemente la vacante y todas las postulaciones asociadas.');">
                        <input type="hidden" name="id" value="<?= $convocatoria['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="btn" style="width: 100%; justify-content: center; background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; font-weight: 700; padding: 0.6rem; cursor: pointer; border-radius: 6px;">
                            <?= IconHelper::render('trash', 14) ?> Eliminar Vacante para Siempre
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
