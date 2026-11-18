<div style="max-width: 860px; margin: 0 auto; background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 2.25rem; box-shadow: 0 1px 4px rgba(0,0,0,0.03);">
    <div style="margin-bottom: 1.5rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1rem;">
        <a href="<?= BASE_URL ?>/index.php?c=Convocatoria&a=detalle&id=<?= $convocatoria['id'] ?>" style="color: #10b981; font-size: 0.88rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
            <?= IconHelper::render('arrow-left', 14) ?> Volver a la convocatoria
        </a>
        <h1 style="color: #0b6b44; font-size: 1.5rem; font-weight: 800; margin-top: 0.5rem;">
            Editar Convocatoria: <?= htmlspecialchars($convocatoria['codigo'], ENT_QUOTES, 'UTF-8') ?>
        </h1>
        <p style="color: #64748b; font-size: 0.88rem;">Modifique los datos de la vacante. Los cambios se actualizarán permanentemente en la base de datos.</p>
    </div>

    <form action="<?= BASE_URL ?>/index.php?c=Convocatoria&a=actualizar" method="POST">
        <input type="hidden" name="id" value="<?= $convocatoria['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <!-- Fila 1: Código y Cargo -->
        <div style="display: grid; grid-template-columns: 240px 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Código Único *</label>
                <input type="text" name="codigo" required class="filter-control" style="width: 100%; font-family: monospace; font-weight: 700;" value="<?= htmlspecialchars($convocatoria['codigo'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Nombre del Cargo / Vacante *</label>
                <input type="text" name="cargo" required class="filter-control" style="width: 100%; font-size: 0.92rem;" value="<?= htmlspecialchars($convocatoria['cargo'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>

        <!-- Fila 2: Dependencia, Tipo y Facultad -->
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Dependencia Solicitante *</label>
                <select name="dependencia_id" id="selDependencia" required class="filter-control" style="width: 100%;">
                    <?php foreach ($dependencias as $dep): ?>
                        <option value="<?= $dep['id'] ?>" data-facultad="<?= htmlspecialchars($dep['facultad'], ENT_QUOTES, 'UTF-8') ?>" data-sede="<?= htmlspecialchars($dep['sede'], ENT_QUOTES, 'UTF-8') ?>" <?= ($convocatoria['dependencia_id'] == $dep['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dep['nombre'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Tipo de Convocatoria</label>
                <select name="tipo_convocatoria" class="filter-control" style="width: 100%;">
                    <option value="Docente" <?= ($convocatoria['tipo_convocatoria'] === 'Docente') ? 'selected' : '' ?>>Docente</option>
                    <option value="Administrativo" <?= ($convocatoria['tipo_convocatoria'] === 'Administrativo') ? 'selected' : '' ?>>Administrativo</option>
                    <option value="Directivo" <?= ($convocatoria['tipo_convocatoria'] === 'Directivo') ? 'selected' : '' ?>>Directivo</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Facultad / División *</label>
                <input type="text" name="facultad" id="txtFacultad" required class="filter-control" style="width: 100%;" value="<?= htmlspecialchars($convocatoria['facultad'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>

        <!-- Fila 3: Vacantes, Modalidad, Periodo y Sede -->
        <div style="display: grid; grid-template-columns: 100px 1fr 120px 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Vacantes *</label>
                <input type="number" name="vacantes" value="<?= (int)$convocatoria['vacantes'] ?>" min="1" required class="filter-control" style="width: 100%;">
            </div>
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Modalidad</label>
                <select name="modalidad" class="filter-control" style="width: 100%;">
                    <option value="Tiempo Completo" <?= ($convocatoria['modalidad'] === 'Tiempo Completo') ? 'selected' : '' ?>>Tiempo Completo</option>
                    <option value="Medio Tiempo" <?= ($convocatoria['modalidad'] === 'Medio Tiempo') ? 'selected' : '' ?>>Medio Tiempo</option>
                    <option value="Cátedra" <?= ($convocatoria['modalidad'] === 'Cátedra') ? 'selected' : '' ?>>Cátedra</option>
                    <option value="Ocasional" <?= ($convocatoria['modalidad'] === 'Ocasional') ? 'selected' : '' ?>>Ocasional</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Periodo</label>
                <input type="text" name="periodo_academico" value="<?= htmlspecialchars($convocatoria['periodo_academico'], ENT_QUOTES, 'UTF-8') ?>" class="filter-control" style="width: 100%;">
            </div>
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Sede</label>
                <input type="text" name="sede" id="txtSede" value="<?= htmlspecialchars($convocatoria['sede'], ENT_QUOTES, 'UTF-8') ?>" class="filter-control" style="width: 100%;">
            </div>
        </div>

        <!-- Fila 4: Programa Académico y Fechas -->
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Programa Académico / Unidad</label>
                <input type="text" name="programa" class="filter-control" style="width: 100%;" value="<?= htmlspecialchars($convocatoria['programa'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Fecha Apertura *</label>
                <input type="datetime-local" name="fecha_apertura" required class="filter-control" style="width: 100%;" value="<?= date('Y-m-d\TH:i', strtotime($convocatoria['fecha_apertura'])) ?>">
            </div>
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Fecha Cierre *</label>
                <input type="datetime-local" name="fecha_cierre" required class="filter-control" style="width: 100%;" value="<?= date('Y-m-d\TH:i', strtotime($convocatoria['fecha_cierre'])) ?>">
            </div>
        </div>

        <!-- Fila 5: Descripción -->
        <div style="margin-bottom: 1.25rem;">
            <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Descripción General de la Vacante</label>
            <textarea name="descripcion" rows="3" class="filter-control" style="width: 100%; line-height: 1.45;"><?= htmlspecialchars($convocatoria['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <!-- Sección Perfil Requerido -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem;">
            <h3 style="font-size: 0.95rem; font-weight: 800; color: #0b6b44; margin-bottom: 0.85rem;">
                Requisitos del Perfil del Cargo
            </h3>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Formación Mínima Exigida *</label>
                    <input type="text" name="formacion_minima" required class="filter-control" style="width: 100%;" value="<?= htmlspecialchars($convocatoria['formacion_minima'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Experiencia Mínima (Meses)</label>
                    <input type="number" name="experiencia_minima_meses" value="<?= (int)($convocatoria['experiencia_minima_meses'] ?? 12) ?>" class="filter-control" style="width: 100%;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Competencias Institucionales</label>
                    <textarea name="competencias" rows="2" class="filter-control" style="width: 100%;"><?= htmlspecialchars($convocatoria['competencias'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Funciones Principales</label>
                    <textarea name="funciones" rows="2" class="filter-control" style="width: 100%;"><?= htmlspecialchars($convocatoria['funciones'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Condiciones Laborales</label>
                <input type="text" name="condiciones" class="filter-control" style="width: 100%;" value="<?= htmlspecialchars($convocatoria['condiciones'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 1.25rem;">
            <div>
                <label style="font-size: 0.82rem; font-weight: 700; color: #334155; margin-right: 0.5rem;">Estado de la Convocatoria:</label>
                <select name="estado" class="filter-control" style="display: inline-block; width: auto;">
                    <option value="publicada" <?= ($convocatoria['estado'] === 'publicada') ? 'selected' : '' ?>>Publicada (Abierta)</option>
                    <option value="borrador" <?= ($convocatoria['estado'] === 'borrador') ? 'selected' : '' ?>>Borrador (En preparación)</option>
                    <option value="cerrada" <?= ($convocatoria['estado'] === 'cerrada') ? 'selected' : '' ?>>Cerrada (Finalizada)</option>
                    <option value="retirada" <?= ($convocatoria['estado'] === 'retirada') ? 'selected' : '' ?>>Retirada (Cancelada)</option>
                </select>
            </div>

            <div style="display: flex; gap: 0.75rem;">
                <a href="<?= BASE_URL ?>/index.php?c=Convocatoria&a=detalle&id=<?= $convocatoria['id'] ?>" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary" style="padding: 0.65rem 1.75rem; font-weight: 700;">
                    <?= IconHelper::render('check', 16) ?> Actualizar Cambios
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
