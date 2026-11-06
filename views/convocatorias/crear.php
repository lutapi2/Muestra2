<div style="max-width: 800px; margin: 0 auto; background: #ffffff; border: 1px solid var(--border-color); border-radius: 10px; padding: 2rem;">
    <div style="margin-bottom: 1.5rem;">
        <a href="<?= BASE_URL ?>/index.php?c=Convocatoria&a=index" style="color: #10b981; font-size: 0.88rem; font-weight: 700; text-decoration: none;">
            &larr; Volver
        </a>
        <h1 style="color: #0b6b44; font-size: 1.5rem; font-weight: 800; margin-top: 0.25rem;">
            Crear Nueva Convocatoria Institucional
        </h1>
        <p style="color: #64748b; font-size: 0.88rem;">Configure los datos básicos, perfil y fechas para habilitar postulaciones.</p>
    </div>

    <form action="<?= BASE_URL ?>/index.php?c=Convocatoria&a=guardar" method="POST">
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Código</label>
                <input type="text" name="codigo" required class="filter-control" style="width: 100%;" value="CONV-<?= date('Y') ?>-DOC-<?= rand(10, 99) ?>">
            </div>
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Nombre del Cargo</label>
                <input type="text" name="cargo" required class="filter-control" style="width: 100%;" placeholder="Ej: Docente de Cátedra - Derecho Constitucional">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Dependencia / Facultad Solicitante</label>
                <select name="dependencia_id" required class="filter-control" style="width: 100%;">
                    <?php foreach ($dependencias as $dep): ?>
                        <option value="<?= $dep['id'] ?>"><?= htmlspecialchars($dep['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Facultad</label>
                <input type="text" name="facultad" required class="filter-control" style="width: 100%;" value="Facultad de Educación y Ciencias Sociales">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Vacantes</label>
                <input type="number" name="vacantes" value="1" min="1" class="filter-control" style="width: 100%;">
            </div>
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Modalidad</label>
                <select name="modalidad" class="filter-control" style="width: 100%;">
                    <option value="Tiempo Completo">Tiempo Completo</option>
                    <option value="Medio Tiempo">Medio Tiempo</option>
                    <option value="Cátedra">Cátedra</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Periodo Académico</label>
                <input type="text" name="periodo_academico" value="2026-2" class="filter-control" style="width: 100%;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Fecha Apertura</label>
                <input type="datetime-local" name="fecha_apertura" required class="filter-control" style="width: 100%;" value="<?= date('Y-m-d\T08:00') ?>">
            </div>
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Fecha Cierre</label>
                <input type="datetime-local" name="fecha_cierre" required class="filter-control" style="width: 100%;" value="<?= date('Y-m-d\T18:00', strtotime('+30 days')) ?>">
            </div>
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Descripción General</label>
            <textarea name="descripcion" rows="3" class="filter-control" style="width: 100%;" placeholder="Detalle de la convocatoria..."></textarea>
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Formación Mínima Exigida</label>
            <input type="text" name="formacion_minima" required class="filter-control" style="width: 100%;" placeholder="Título universitario y posgrado requerido">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Experiencia Mínima (Meses)</label>
                <input type="number" name="experiencia_minima_meses" value="24" class="filter-control" style="width: 100%;">
            </div>
            <div>
                <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Estado Inicial</label>
                <select name="estado" class="filter-control" style="width: 100%;">
                    <option value="publicada">Publicada (abierta a postulaciones)</option>
                    <option value="borrador">Borrador (en preparación)</option>
                </select>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="<?= BASE_URL ?>/index.php?c=Convocatoria&a=index" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.5rem;">Guardar y Publicar</button>
        </div>
    </form>
</div>
