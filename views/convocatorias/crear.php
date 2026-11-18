<div style="max-width: 860px; margin: 0 auto; background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 2.25rem; box-shadow: 0 1px 4px rgba(0,0,0,0.03);">
    <div style="margin-bottom: 1.5rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1rem;">
        <a href="<?= BASE_URL ?>/index.php?c=Convocatoria&a=index" style="color: #10b981; font-size: 0.88rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
            <?= IconHelper::render('arrow-left', 14) ?> Volver al listado
        </a>
        <h1 style="color: #0b6b44; font-size: 1.5rem; font-weight: 800; margin-top: 0.5rem;">
            Crear y Registrar Nueva Convocatoria Institucional
        </h1>
        <p style="color: #64748b; font-size: 0.88rem;">Configure todos los datos de la vacante, perfil exigido y fechas. La vacante quedará guardada permanentemente en la base de datos.</p>
    </div>

    <form action="<?= BASE_URL ?>/index.php?c=Convocatoria&a=guardar" method="POST" id="formCrearConvocatoria">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <!-- Fila 1: Código y Cargo -->
        <div style="display: grid; grid-template-columns: 240px 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Código Único *</label>
                <div style="display: flex; gap: 0.35rem;">
                    <input type="text" name="codigo" id="codigoConv" required class="filter-control" style="width: 100%; font-family: monospace; font-weight: 700;" value="<?= htmlspecialchars($form['codigo'] ?? $codigoSugerido, ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <small style="color: #64748b; font-size: 0.72rem;">Identificador único institucional</small>
            </div>
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Nombre del Cargo / Vacante *</label>
                <input type="text" name="cargo" required class="filter-control" style="width: 100%; font-size: 0.92rem;" placeholder="Ej: Docente de Cátedra - Derecho Constitucional" value="<?= htmlspecialchars($form['cargo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>

        <!-- Fila 2: Dependencia, Tipo y Facultad -->
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Dependencia Solicitante *</label>
                <select name="dependencia_id" id="selDependencia" required class="filter-control" style="width: 100%;">
                    <?php 
                    $selDepId = $form['dependencia_id'] ?? 1;
                    foreach ($dependencias as $dep): 
                    ?>
                        <option value="<?= $dep['id'] ?>" data-facultad="<?= htmlspecialchars($dep['facultad'], ENT_QUOTES, 'UTF-8') ?>" data-sede="<?= htmlspecialchars($dep['sede'], ENT_QUOTES, 'UTF-8') ?>" <?= $selDepId == $dep['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dep['nombre'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Tipo de Convocatoria</label>
                <select name="tipo_convocatoria" class="filter-control" style="width: 100%;">
                    <?php $tipoVal = $form['tipo_convocatoria'] ?? 'Docente'; ?>
                    <option value="Docente" <?= $tipoVal === 'Docente' ? 'selected' : '' ?>>Docente</option>
                    <option value="Administrativo" <?= $tipoVal === 'Administrativo' ? 'selected' : '' ?>>Administrativo</option>
                    <option value="Directivo" <?= $tipoVal === 'Directivo' ? 'selected' : '' ?>>Directivo</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Facultad / División *</label>
                <input type="text" name="facultad" id="txtFacultad" required class="filter-control" style="width: 100%;" value="<?= htmlspecialchars($form['facultad'] ?? ($dependencias[0]['facultad'] ?? 'Facultad de Educación y Ciencias Sociales'), ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>

        <!-- Fila 3: Vacantes, Modalidad, Periodo y Sede -->
        <div style="display: grid; grid-template-columns: 100px 1fr 120px 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Vacantes *</label>
                <input type="number" name="vacantes" value="<?= htmlspecialchars($form['vacantes'] ?? 1, ENT_QUOTES, 'UTF-8') ?>" min="1" required class="filter-control" style="width: 100%;">
            </div>
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Modalidad</label>
                <select name="modalidad" class="filter-control" style="width: 100%;">
                    <?php $modVal = $form['modalidad'] ?? 'Tiempo Completo'; ?>
                    <option value="Tiempo Completo" <?= $modVal === 'Tiempo Completo' ? 'selected' : '' ?>>Tiempo Completo</option>
                    <option value="Medio Tiempo" <?= $modVal === 'Medio Tiempo' ? 'selected' : '' ?>>Medio Tiempo</option>
                    <option value="Cátedra" <?= $modVal === 'Cátedra' ? 'selected' : '' ?>>Cátedra</option>
                    <option value="Ocasional" <?= $modVal === 'Ocasional' ? 'selected' : '' ?>>Ocasional</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Periodo</label>
                <input type="text" name="periodo_academico" value="<?= htmlspecialchars($form['periodo_academico'] ?? '2026-2', ENT_QUOTES, 'UTF-8') ?>" class="filter-control" style="width: 100%;">
            </div>
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Sede</label>
                <input type="text" name="sede" id="txtSede" value="<?= htmlspecialchars($form['sede'] ?? 'Sincelejo', ENT_QUOTES, 'UTF-8') ?>" class="filter-control" style="width: 100%;">
            </div>
        </div>

        <!-- Fila 4: Programa Académico y Fechas -->
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Programa Académico / Unidad</label>
                <input type="text" name="programa" class="filter-control" style="width: 100%;" placeholder="Ej: Licenciatura en Pedagogía / Sistemas" value="<?= htmlspecialchars($form['programa'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Fecha Apertura *</label>
                <input type="datetime-local" name="fecha_apertura" required class="filter-control" style="width: 100%;" value="<?= htmlspecialchars($form['fecha_apertura'] ?? date('Y-m-d\T08:00'), ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Fecha Cierre *</label>
                <input type="datetime-local" name="fecha_cierre" required class="filter-control" style="width: 100%;" value="<?= htmlspecialchars($form['fecha_cierre'] ?? date('Y-m-d\T18:00', strtotime('+30 days')), ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>

        <!-- Fila 5: Descripción -->
        <div style="margin-bottom: 1.25rem;">
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Descripción General de la Vacante</label>
            <textarea name="descripcion" rows="3" class="filter-control" style="width: 100%; line-height: 1.45;" placeholder="Detalles de la convocatoria pública de méritos..."><?= htmlspecialchars($form['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <!-- Sección Perfil Requerido -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem;">
            <h3 style="font-size: 0.95rem; font-weight: 800; color: #0b6b44; margin-bottom: 0.85rem;">
                Requisitos del Perfil del Cargo
            </h3>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Formación Mínima Exigida *</label>
                    <input type="text" name="formacion_minima" required class="filter-control" style="width: 100%;" placeholder="Ej: Título universitario y maestría en área afín" value="<?= htmlspecialchars($form['formacion_minima'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Experiencia Mínima (Meses)</label>
                    <input type="number" name="experiencia_minima_meses" value="<?= htmlspecialchars($form['experiencia_minima_meses'] ?? 24, ENT_QUOTES, 'UTF-8') ?>" class="filter-control" style="width: 100%;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Competencias Institucionales</label>
                    <textarea name="competencias" rows="2" class="filter-control" style="width: 100%;" placeholder="Competencias pedagógicas, ética, trabajo en equipo..."><?= htmlspecialchars($form['competencias'] ?? 'Competencias pedagógicas, ética, trabajo colaborativo y manejo de TIC.', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Funciones Principales</label>
                    <textarea name="funciones" rows="2" class="filter-control" style="width: 100%;" placeholder="Orientar asignaturas, investigación formativa..."><?= htmlspecialchars($form['funciones'] ?? 'Orientar asignaturas asignadas, participar en comités curriculares y proyectos institucionales.', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Condiciones Laborales</label>
                <input type="text" name="condiciones" class="filter-control" style="width: 100%;" placeholder="Disponibilidad horaria, sede..." value="<?= htmlspecialchars($form['condiciones'] ?? 'Disponibilidad horaria diurna y nocturna según programación académica.', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 1.25rem;">
            <div>
                <label style="font-size: 0.82rem; font-weight: 700; color: #334155; margin-right: 0.5rem;">Estado Inicial:</label>
                <select name="estado" class="filter-control" style="display: inline-block; width: auto;">
                    <?php $estVal = $form['estado'] ?? 'publicada'; ?>
                    <option value="publicada" <?= $estVal === 'publicada' ? 'selected' : '' ?>>Publicada (Habilitada para postulaciones)</option>
                    <option value="borrador" <?= $estVal === 'borrador' ? 'selected' : '' ?>>Borrador (En preparación interna)</option>
                </select>
            </div>

            <div style="display: flex; gap: 0.75rem;">
                <a href="<?= BASE_URL ?>/index.php?c=Convocatoria&a=index" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary" style="padding: 0.65rem 1.75rem; font-weight: 700;">
                    <?= IconHelper::render('check', 16) ?> Guardar y Registrar Vacante
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var selDep = document.getElementById('selDependencia');
    var txtFac = document.getElementById('txtFacultad');
    var txtSede = document.getElementById('txtSede');

    if (selDep && txtFac) {
        selDep.addEventListener('change', function() {
            var opt = selDep.options[selDep.selectedIndex];
            if (opt) {
                var fac = opt.getAttribute('data-facultad');
                var sede = opt.getAttribute('data-sede');
                if (fac) txtFac.value = fac;
                if (sede && txtSede) txtSede.value = sede;
            }
        });
    }
});
</script>
