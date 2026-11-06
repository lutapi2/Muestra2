<?php
/**
 * views/dashboard/index.php
 * Dashboard Institucional CECAR (Imagen 1 Inferior - Estilo Limpio y Sin Emojis)
 */
use App\Helpers\AuthHelper;
use App\Helpers\FormatHelper;
use App\Helpers\IconHelper;

$usuario = AuthHelper::getUsuarioActual();
?>

<!-- Banner Verde Institucional de Bienvenida (Exacto a Imagen 1 Inferior) -->
<div class="banner-gestion">
    <div class="banner-gestion-content">
        <h2>¡Bienvenido al Sistema de Gestión!</h2>
        <p>Accede a todos los módulos desde el menú lateral. Tu sesión está activa y segura.</p>
    </div>

    <div class="banner-gestion-tags">
        <div class="banner-tag">
            <span>EMPRESA</span> CECAR
        </div>
        <div class="banner-tag">
            <span>POSICIÓN</span> <?= htmlspecialchars(AuthHelper::getCargo(), ENT_QUOTES, 'UTF-8') ?>
        </div>
        <div class="banner-tag">
            <span>SEDE</span> Sincelejo
        </div>
        <div class="user-avatar" style="width: 42px; height: 42px; background: #0b4f2c; border: 2px solid #6ee7b7; font-size: 1rem;">
            <?= strtoupper(substr(AuthHelper::getNombreCompleto(), 0, 1)) ?>
        </div>
    </div>
</div>

<!-- 4 Cajas de Módulos de Gestión (Imagen 1 Inferior - Iconos Limpios) -->
<div class="module-cards-grid">
    <!-- 1. Usuarios -->
    <a href="<?= BASE_URL ?>/index.php?c=Admin&a=usuarios" class="module-card">
        <div class="module-card-icon" style="background: #f0fdf4; color: #15803d;">
            <?= IconHelper::render('users', 22) ?>
        </div>
        <div class="module-card-text">
            <h3>Usuarios</h3>
            <span>Gestión completa</span>
        </div>
    </a>

    <!-- 2. Activos -->
    <a href="<?= BASE_URL ?>/index.php?c=Contratacion&a=index" class="module-card">
        <div class="module-card-icon" style="background: #f0fdf4; color: #15803d;">
            <?= IconHelper::render('box', 22) ?>
        </div>
        <div class="module-card-text">
            <h3>Activos</h3>
            <span>Control total</span>
        </div>
    </a>

    <!-- 3. Tareas -->
    <a href="<?= BASE_URL ?>/index.php?c=PlanTrabajo&a=index" class="module-card">
        <div class="module-card-icon" style="background: #f0fdf4; color: #15803d;">
            <?= IconHelper::render('calendar', 22) ?>
        </div>
        <div class="module-card-text">
            <h3>Tareas</h3>
            <span>Pendientes</span>
        </div>
    </a>

    <!-- 4. Reportes -->
    <a href="<?= BASE_URL ?>/index.php?c=Reportes&a=index" class="module-card">
        <div class="module-card-icon" style="background: #f0fdf4; color: #15803d;">
            <?= IconHelper::render('chart', 22) ?>
        </div>
        <div class="module-card-text">
            <h3>Reportes</h3>
            <span>Análisis</span>
        </div>
    </a>
</div>

<!-- Acciones Rápidas (Imagen 1 Inferior) -->
<div class="quick-actions-bar">
    <h4>Acciones Rápidas</h4>
    <div class="quick-actions-buttons">
        <a href="<?= BASE_URL ?>/index.php?c=Convocatoria&a=crear" class="btn btn-secondary">
            <?= IconHelper::render('plus', 14) ?>
            <span>Nuevo Título</span>
        </a>
        <a href="<?= BASE_URL ?>/index.php?c=Contratacion&a=index" class="btn btn-secondary">
            <?= IconHelper::render('file-text', 14) ?>
            <span>Registrar Activo</span>
        </a>
        <a href="<?= BASE_URL ?>/index.php?c=Reportes&a=index" class="btn btn-secondary">
            <?= IconHelper::render('chart', 14) ?>
            <span>Generar Reporte</span>
        </a>
    </div>
</div>

<!-- Indicadores Integrales por Estado (RF-086) -->
<div style="margin-bottom: 2rem;">
    <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem;">
        Indicadores Clave del Proceso de Selección y Contratación
    </h3>
    <div class="kpi-counters-row" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
        <div class="kpi-counter-card">
            <div>
                <div class="kpi-counter-number"><?= $stats['total_postulados'] ?></div>
                <div class="kpi-counter-label">Postulados Totales</div>
            </div>
            <div class="kpi-counter-icon" style="margin-left: auto; color: #64748b;">
                <?= IconHelper::render('users', 20) ?>
            </div>
        </div>

        <div class="kpi-counter-card">
            <div>
                <div class="kpi-counter-number" style="color: #b45309;"><?= $stats['en_proceso'] ?></div>
                <div class="kpi-counter-label">En Proceso / Revisión</div>
            </div>
            <div class="kpi-counter-icon" style="margin-left: auto; color: #b45309;">
                <?= IconHelper::render('clock', 20) ?>
            </div>
        </div>

        <div class="kpi-counter-card">
            <div>
                <div class="kpi-counter-number" style="color: #b91c1c;"><?= $stats['no_aprobados'] ?></div>
                <div class="kpi-counter-label">No Aprobados</div>
            </div>
            <div class="kpi-counter-icon" style="margin-left: auto; color: #b91c1c;">
                <?= IconHelper::render('x-circle', 20) ?>
            </div>
        </div>

        <div class="kpi-counter-card">
            <div>
                <div class="kpi-counter-number" style="color: #15803d;"><?= $stats['seleccionados'] ?></div>
                <div class="kpi-counter-label">Seleccionados</div>
            </div>
            <div class="kpi-counter-icon" style="margin-left: auto; color: #15803d;">
                <?= IconHelper::render('check-circle', 20) ?>
            </div>
        </div>

        <div class="kpi-counter-card" style="background: #f0fdf4;">
            <div>
                <div class="kpi-counter-number" style="color: #047857;"><?= $stats['vinculados'] ?></div>
                <div class="kpi-counter-label">Vinculados Formales</div>
            </div>
            <div class="kpi-counter-icon" style="margin-left: auto; color: #047857;">
                <?= IconHelper::render('briefcase', 20) ?>
            </div>
        </div>
    </div>
</div>

<!-- Bandeja de Convocatorias Activas / Mis Postulaciones -->
<?php if ($esCandidato): ?>
    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 1.5rem; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b;">Mis Postulaciones Activas</h3>
            <a href="<?= BASE_URL ?>/index.php?c=Convocatoria&a=index" class="btn btn-primary btn-sm">Ver Convocatorias Abiertas</a>
        </div>

        <?php if (empty($misPostulaciones)): ?>
            <p style="color: #64748b; font-size: 0.88rem;">Aún no ha iniciado ninguna postulación.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="cecar-table">
                    <thead>
                        <tr>
                            <th>Radicado</th>
                            <th>Cargo Convocatoria</th>
                            <th>Facultad</th>
                            <th>Avance HV</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($misPostulaciones as $mp): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($mp['codigo_radicado'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                                <td><?= htmlspecialchars($mp['cargo'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($mp['facultad'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div style="flex: 1; height: 6px; background: #e2e8f0; border-radius: 9999px; overflow: hidden; max-width: 80px;">
                                            <div style="width: <?= (int)$mp['porcentaje_avance'] ?>%; height: 100%; background: #10b981;"></div>
                                        </div>
                                        <span style="font-size: 0.78rem; font-weight: 700;"><?= (int)$mp['porcentaje_avance'] ?>%</span>
                                    </div>
                                </td>
                                <td><?= FormatHelper::badgeEstado($mp['estado']) ?></td>
                                <td>
                                    <?php if ($mp['estado'] === 'borrador'): ?>
                                        <a href="<?= BASE_URL ?>/index.php?c=Postulacion&a=index&convocatoria_id=<?= $mp['convocatoria_id'] ?>" class="btn btn-primary btn-sm">
                                            Continuar Diligenciamiento
                                        </a>
                                    <?php elseif ($mp['estado'] === 'subsanacion_requerida'): ?>
                                        <a href="<?= BASE_URL ?>/index.php?c=Postulacion&a=subsanar&id=<?= $mp['id'] ?>" class="btn btn-danger btn-sm">
                                            Subsanar Ítems
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= BASE_URL ?>/index.php?c=Postulacion&a=constancia&id=<?= $mp['id'] ?>" class="btn btn-secondary btn-sm">
                                            Ver Radicado
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b;">Procesos de Convocatoria en Curso</h3>
            <a href="<?= BASE_URL ?>/index.php?c=Convocatoria&a=crear" class="btn btn-primary btn-sm">
                <?= IconHelper::render('plus', 14) ?>
                <span>Nueva Convocatoria</span>
            </a>
        </div>

        <div class="table-responsive">
            <table class="cecar-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Cargo</th>
                        <th>Facultad / Unidad</th>
                        <th>Modalidad</th>
                        <th>Vacantes</th>
                        <th>Cierre</th>
                        <th>Postulados</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($convocatoriasRecientes as $c): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($c['codigo'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                            <td><?= htmlspecialchars($c['cargo'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($c['facultad'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($c['modalidad'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= (int)$c['vacantes'] ?></td>
                            <td><?= FormatHelper::fechaCorta($c['fecha_cierre']) ?></td>
                            <td>
                                <span class="badge" style="background: #f1f5f9; color: #334155; font-weight: 700;">
                                    <?= (int)$c['total_postulados'] ?>
                                </span>
                            </td>
                            <td><?= FormatHelper::badgeEstado($c['estado']) ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>/index.php?c=Convocatoria&a=detalle&id=<?= $c['id'] ?>" class="btn btn-secondary btn-sm">
                                    Detalle
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
