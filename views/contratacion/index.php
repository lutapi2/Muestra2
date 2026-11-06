<?php
/**
 * views/contratacion/index.php
 * Gestión de Contratación de Seleccionados (Exacto a Imagen 2)
 */
use App\Helpers\FormatHelper;
?>

<!-- ========================================================================
     Encabezado de Página (Exacto a Imagen 2)
     ======================================================================== -->
<div style="margin-bottom: 1.5rem;">
    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
        <span style="color: #10b981; font-size: 1.35rem;">📄</span>
        <h1 style="color: #0b6b44; font-size: 1.45rem; font-weight: 800; letter-spacing: -0.01em;">
            Gestión de Contratación de Seleccionados
        </h1>
    </div>
    <p style="color: #64748b; font-size: 0.88rem;">
        Administra tus documentos por proceso
    </p>
</div>

<!-- ========================================================================
     6 Contadores KPI Superiores (Exacto a Imagen 2)
     ======================================================================== -->
<div class="kpi-counters-row" style="grid-template-columns: repeat(6, 1fr);">
    <!-- 1. Usuarios -->
    <div class="kpi-counter-card">
        <div class="kpi-counter-number"><?= $kpis['usuarios'] ?></div>
        <div class="kpi-counter-label">Usuarios</div>
        <div class="kpi-counter-icon" style="margin-left: auto;">👥</div>
    </div>

    <!-- 2. Documentos -->
    <div class="kpi-counter-card">
        <div class="kpi-counter-number"><?= $kpis['documentos'] ?></div>
        <div class="kpi-counter-label">Documentos</div>
        <div class="kpi-counter-icon" style="margin-left: auto;">📋</div>
    </div>

    <!-- 3. Aprobados -->
    <div class="kpi-counter-card">
        <div class="kpi-counter-number"><?= $kpis['aprobados'] ?></div>
        <div class="kpi-counter-label">Aprobados</div>
        <div class="kpi-counter-icon" style="margin-left: auto; color: #10b981;">✔️</div>
    </div>

    <!-- 4. Pendientes Revisión -->
    <div class="kpi-counter-card">
        <div class="kpi-counter-number" style="color: #d97706;"><?= $kpis['pendientes'] ?></div>
        <div class="kpi-counter-label">Pendientes Revisión</div>
        <div class="kpi-counter-icon" style="margin-left: auto; color: #f59e0b;">⏳</div>
    </div>

    <!-- 5. Rechazados -->
    <div class="kpi-counter-card">
        <div class="kpi-counter-number" style="color: #dc2626;"><?= $kpis['rechazados'] ?></div>
        <div class="kpi-counter-label">Rechazados</div>
        <div class="kpi-counter-icon" style="margin-left: auto; color: #ef4444;">❌</div>
    </div>

    <!-- 6. Subsanados -->
    <div class="kpi-counter-card">
        <div class="kpi-counter-number" style="color: #10b981;"><?= $kpis['subsanados'] ?></div>
        <div class="kpi-counter-label">Subsanados</div>
        <div class="kpi-counter-icon" style="margin-left: auto; color: #10b981;">🔄</div>
    </div>
</div>

<!-- ========================================================================
     Tarjeta de Filtros: "Mis Documentos por Proceso [1]" (Exacto a Imagen 2)
     ======================================================================== -->
<form action="<?= BASE_URL ?>/index.php" method="GET" class="filter-card">
    <input type="hidden" name="c" value="Contratacion">
    <input type="hidden" name="a" value="index">

    <div class="filter-card-header">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span>📁 Mis Documentos por Proceso</span>
            <span class="count-pill"><?= count($seleccionados) ?></span>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="<?= BASE_URL ?>/index.php?c=Contratacion&a=exportarExcel" class="btn btn-sm btn-secondary" style="background: rgba(255,255,255,0.15); border-color: rgba(255,255,255,0.25); color: #fff;" title="Exportar reporte a Excel (RF-079)">
                📊 Exportar Excel
            </a>
        </div>
    </div>

    <div class="filter-card-body">
        <!-- Fila 1 de Filtros (Exacto a Imagen 2) -->
        <div class="filter-row">
            <select name="proceso" class="filter-control" style="flex: 1.2;">
                <option value="">Todos los procesos</option>
                <?php foreach ($convocatorias as $conv): ?>
                    <option value="<?= $conv['id'] ?>" <?= ($filtros['proceso'] == $conv['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($conv['codigo'] . ' - ' . $conv['cargo'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="unidad" class="filter-control" style="flex: 1;">
                <option value="">Todas las unidades</option>
                <option value="Educación" <?= ($filtros['unidad'] === 'Educación') ? 'selected' : '' ?>>Facultad de Educación y Ciencias Sociales</option>
                <option value="Ingeniería" <?= ($filtros['unidad'] === 'Ingeniería') ? 'selected' : '' ?>>Facultad de Ciencias Básicas e Ingeniería</option>
                <option value="Económicas" <?= ($filtros['unidad'] === 'Económicas') ? 'selected' : '' ?>>Facultad de Ciencias Económicas</option>
            </select>

            <select name="estado_proceso" class="filter-control" style="flex: 0.9;">
                <option value="">Estado proceso</option>
                <option value="contratado" <?= ($filtros['estado_proceso'] === 'contratado') ? 'selected' : '' ?>>Contratado</option>
                <option value="en_formalizacion" <?= ($filtros['estado_proceso'] === 'en_formalizacion') ? 'selected' : '' ?>>En Formalización</option>
                <option value="vinculado" <?= ($filtros['estado_proceso'] === 'vinculado') ? 'selected' : '' ?>>Vinculado</option>
            </select>

            <select name="estado_contratacion" class="filter-control" style="flex: 1.1;">
                <option value="">Contratación de Seleccionados</option>
                <option value="En Formalización" <?= ($filtros['estado_contratacion'] === 'En Formalización') ? 'selected' : '' ?>>En Formalización</option>
                <option value="Contratado" <?= ($filtros['estado_contratacion'] === 'Contratado') ? 'selected' : '' ?>>Contratado</option>
            </select>

            <button type="submit" class="btn btn-secondary" style="padding: 0.45rem 0.75rem;" title="Buscar">
                🔍
            </button>
            <a href="<?= BASE_URL ?>/index.php?c=Contratacion&a=index" class="btn btn-success" style="padding: 0.45rem 0.75rem;" title="Restablecer filtros">
                🔄
            </a>
        </div>

        <!-- Fila 2 de Filtros (Exacto a Imagen 2 y 3) -->
        <div class="filter-row" style="align-items: center;">
            <div style="position: relative; flex: 1.2;">
                <input type="text" id="filterSearchUser" name="buscar_usuario" value="<?= htmlspecialchars($filtros['buscar_usuario'], ENT_QUOTES, 'UTF-8') ?>" class="filter-control" style="width: 100%;" placeholder="🔍 Buscar usuario...">
            </div>

            <select name="filtro_incompleto" class="filter-control" style="flex: 0.75;">
                <option value="">Incompleto</option>
                <option value="si">Solo incompletos</option>
            </select>

            <select name="filtro_re" class="filter-control" style="flex: 0.85;">
                <option value="">Estados de Re...</option>
                <option value="revisado">Revisado</option>
                <option value="pendiente">Pendiente</option>
            </select>

            <!-- Dropdown con opciones de Carga y Contratación (Exacto a Imagen 3 Inferior) -->
            <select name="estado_carga" class="filter-control" style="flex: 1.2;">
                <option value="">Estado carga de documentos</option>
                <option value="completa" <?= ($filtros['estado_carga'] === 'completa') ? 'selected' : '' ?>>Carga completa</option>
                <option value="incompleta" <?= ($filtros['estado_carga'] === 'incompleta') ? 'selected' : '' ?>>Carga incompleta</option>
            </select>

            <select name="ver_todos" class="filter-control" style="flex: 0.75;">
                <option value="todos">Ver: Todos</option>
                <option value="10">Ver: 10</option>
                <option value="25">Ver: 25</option>
            </select>

            <button type="submit" name="autorizados" value="1" class="btn btn-success" style="background: #10b981; font-size: 0.82rem; font-weight: 700;">
                ⬇️ Autorizados
            </button>
        </div>
    </div>
</form>

<!-- ========================================================================
     Tabla de Seleccionados (Exacto a Imagen 2)
     Columnas: USUARIO, PROCESO, CARGADOS, APROBADOS, PENDIENTES POR REVISIÓN, RECHAZADOS, ACCIÓN
     ======================================================================== -->
<div class="table-responsive">
    <table class="cecar-table" id="contratacionTable">
        <thead>
            <tr>
                <th style="width: 25%;">USUARIO</th>
                <th style="width: 30%;">PROCESO</th>
                <th style="width: 14%; text-align: center;">CARGADOS</th>
                <th style="width: 10%; text-align: center;">APROBADOS</th>
                <th style="width: 11%; text-align: center;">PENDIENTES POR REVISIÓN</th>
                <th style="width: 10%; text-align: center;">RECHAZADOS</th>
                <th style="width: 10%; text-align: center;">ACCIÓN</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($seleccionados)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 3rem 1rem;">
                        <!-- Empty State (Exacto a Imagen 3 Inferior) -->
                        <div id="tableEmptyState">
                            <div style="font-size: 2rem; color: #94a3b8; margin-bottom: 0.5rem;">📄</div>
                            <h4 style="color: #475569; font-size: 1.1rem; font-weight: 700; margin-bottom: 0.25rem;">No hay registros</h4>
                            <p style="color: #94a3b8; font-size: 0.88rem;">No se encontraron registros con los filtros aplicados</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($seleccionados as $item): ?>
                    <tr>
                        <td class="user-col">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div class="user-avatar" style="width: 34px; height: 34px; font-size: 0.85rem; background: #065f46;">
                                    <?= strtoupper(substr($item['nombres'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: #1e293b; font-size: 0.92rem;">
                                        <?= htmlspecialchars($item['nombres'] . ' ' . $item['apellidos'], ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                    <div style="font-size: 0.76rem; color: #64748b;">
                                        CC <?= htmlspecialchars($item['numero_documento'], ENT_QUOTES, 'UTF-8') ?> &bull; <?= htmlspecialchars($item['correo'], ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div style="font-weight: 600; color: #334155; margin-bottom: 0.2rem;">
                                <?= htmlspecialchars($item['cargo'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="font-size: 0.8rem; color: #64748b;">
                                    <?= htmlspecialchars($item['facultad'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                <span class="badge" style="background: #10b981; color: #ffffff; padding: 0.15rem 0.5rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 700;">
                                    Activo
                                </span>
                            </div>
                        </td>

                        <td style="text-align: center;">
                            <!-- Pill "23/23 Contratado" matching screenshot -->
                            <span style="background: #065f46; color: #34d399; padding: 0.35rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 800; display: inline-block;">
                                <?= $item['total_cargados'] ?>/<?= $item['total_cargados'] ?> <?= ucfirst($item['estado_postulacion']) ?>
                            </span>
                        </td>

                        <td style="text-align: center;">
                            <!-- Circle green badge "23" matching screenshot -->
                            <span style="background: #10b981; color: #ffffff; width: 26px; height: 26px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 0.82rem; font-weight: 800;">
                                <?= $item['total_aprobados'] ?>
                            </span>
                        </td>

                        <td style="text-align: center;">
                            <!-- Pendientes Icon / Count -->
                            <span style="color: #f59e0b; font-weight: 700; font-size: 0.9rem;">
                                <?= $item['total_pendientes'] > 0 ? '⚠️ ' . $item['total_pendientes'] : '🕒 0' ?>
                            </span>
                        </td>

                        <td style="text-align: center;">
                            <!-- Rechazados Icon / Count -->
                            <span style="color: #ef4444; font-weight: 700; font-size: 0.9rem;">
                                <?= $item['total_rechazados'] > 0 ? '❌ ' . $item['total_rechazados'] : '🚫 0' ?>
                            </span>
                        </td>

                        <td style="text-align: center;">
                            <div style="display: flex; gap: 0.35rem; justify-content: center;">
                                <a href="<?= BASE_URL ?>/index.php?c=Contratacion&a=expediente&id=<?= $item['postulacion_id'] ?>" class="btn btn-secondary btn-sm" title="Ver Documentos para aprobación (Imagen 3)" style="background: #f1f5f9; border-color: #cbd5e1;">
                                    👁️ Ver
                                </a>
                                <a href="<?= BASE_URL ?>/index.php?c=Kanban&a=lineaTiempo&id=<?= $item['postulacion_id'] ?>" class="btn btn-secondary btn-sm" title="Historial y Trazabilidad (RF-059)">
                                    ⏱️
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
