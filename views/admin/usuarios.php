
<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="color: #0b6b44; font-size: 1.5rem; font-weight: 800;">
            Gestión de Usuarios y Roles Institucionales
        </h1>
        <p style="color: #64748b; font-size: 0.88rem;">
            Administración permanente de credenciales, roles asignados y dependencias (RF-004, RF-005).
        </p>
    </div>
    <button type="button" class="btn btn-primary" onclick="document.getElementById('modalNuevoUsuario').style.display = 'flex';">
        <?= IconHelper::render('plus', 16) ?> Registrar Nuevo Usuario
    </button>
</div>

<!-- Modal Registrar Nuevo Usuario -->
<div id="modalNuevoUsuario" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; justify-content: center; align-items: center; padding: 1.5rem;">
    <div style="background: #ffffff; border-radius: 12px; max-width: 650px; width: 100%; max-height: 90vh; overflow-y: auto; padding: 2rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.75rem;">
            <h3 style="color: #0b6b44; font-size: 1.25rem; font-weight: 800; margin: 0;">
                Registrar Nuevo Usuario en Base de Datos
            </h3>
            <button type="button" onclick="document.getElementById('modalNuevoUsuario').style.display = 'none';" style="background: transparent; border: none; font-size: 1.5rem; cursor: pointer; color: #94a3b8;">&times;</button>
        </div>

        <form action="<?= BASE_URL ?>/index.php?c=Admin&a=guardarUsuario" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Nombres *</label>
                    <input type="text" name="nombres" required class="filter-control" style="width: 100%;" placeholder="Nombres del usuario">
                </div>
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Apellidos *</label>
                    <input type="text" name="apellidos" required class="filter-control" style="width: 100%;" placeholder="Apellidos del usuario">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 110px 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Tipo Doc.</label>
                    <select name="tipo_documento" class="filter-control" style="width: 100%;">
                        <option value="CC">CC</option>
                        <option value="CE">CE</option>
                        <option value="PA">PA</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Número de Documento *</label>
                    <input type="text" name="numero_documento" required class="filter-control" style="width: 100%;" placeholder="Número de cédula o pasaporte">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Correo Institucional / Electrónico *</label>
                    <input type="email" name="correo" required class="filter-control" style="width: 100%;" placeholder="usuario@cecar.edu.co">
                </div>
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Teléfono de Contacto</label>
                    <input type="tel" name="telefono" class="filter-control" style="width: 100%;" placeholder="300 123 4567">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Rol Asignado *</label>
                    <select name="rol" required class="filter-control" style="width: 100%;">
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= htmlspecialchars($r['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($r['nombre'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($r['id'], ENT_QUOTES, 'UTF-8') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Dependencia / Facultad</label>
                    <select name="dependencia_id" class="filter-control" style="width: 100%;">
                        <option value="">-- Sin Dependencia Asignada --</option>
                        <?php foreach ($dependencias as $dep): ?>
                            <option value="<?= $dep['id'] ?>"><?= htmlspecialchars($dep['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Cargo Interno</label>
                    <input type="text" name="cargo_interno" class="filter-control" style="width: 100%;" placeholder="Ej: Docente de Área / Analista">
                </div>
                <div>
                    <label style="display: block; font-size: 0.82rem; font-weight: 600; color: #475569; margin-bottom: 0.25rem;">Contraseña de Acceso</label>
                    <input type="password" name="password" class="filter-control" style="width: 100%;" placeholder="Por defecto: cecar<?= date('Y') ?>">
                </div>
            </div>

            <div style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <input type="checkbox" name="activo" value="1" id="chkActivo" checked style="accent-color: #10b981;">
                <label for="chkActivo" style="font-size: 0.85rem; color: #334155; cursor: pointer;">Usuario Activo para iniciar sesión inmediatamente</label>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid #e2e8f0; padding-top: 1rem;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modalNuevoUsuario').style.display = 'none';">Cancelar</button>
                <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.5rem;">Guardar Usuario para Siempre</button>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="cecar-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>Documento</th>
                <th>Correo Institucional</th>
                <th>Rol</th>
                <th>Dependencia</th>
                <th>Último Acceso</th>
                <th>Estado</th>
                <th style="text-align: center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $currentUserId = AuthHelper::getUsuarioId();
            foreach ($usuarios as $u): 
            ?>
                <tr>
                    <td>#<?= $u['id'] ?></td>
                    <td>
                        <strong style="color: #1e293b;"><?= htmlspecialchars($u['nombres'] . ' ' . $u['apellidos'], ENT_QUOTES, 'UTF-8') ?></strong>
                        <?php if (!empty($u['cargo_interno'])): ?>
                            <div style="font-size: 0.75rem; color: #64748b;"><?= htmlspecialchars($u['cargo_interno'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($u['tipo_documento'] . ' ' . $u['numero_documento'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($u['correo'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <span class="badge" style="background: #e0f2fe; color: #0369a1; font-weight: 700;">
                            <?= htmlspecialchars($u['rol_nombre'] ?? $u['rol'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($u['dependencia_nombre'] ?? 'Sin dependencia', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= FormatHelper::fecha($u['ultimo_acceso']) ?></td>
                    <td>
                        <span class="badge" style="background: <?= ($u['activo'] == 1) ? '#10b981' : '#ef4444' ?>; color: #fff;">
                            <?= ($u['activo'] == 1) ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td style="text-align: center; white-space: nowrap;">
                        <?php if ($u['id'] === $currentUserId): ?>
                            <span style="font-size: 0.75rem; color: #94a3b8; font-style: italic;">Sesión Actual</span>
                        <?php else: ?>
                            <form action="<?= BASE_URL ?>/index.php?c=Admin&a=eliminarUsuario" method="POST" style="display: inline;" onsubmit="return confirm('¿Está seguro de eliminar permanentemente al usuario #<?= $u['id'] ?> (<?= htmlspecialchars($u['nombres'] . ' ' . $u['apellidos'], ENT_QUOTES, 'UTF-8') ?>) de la base de datos? Esta acción borrará para siempre sus registros.');">
                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" class="btn btn-sm" style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; font-weight: 600; padding: 0.25rem 0.65rem; border-radius: 4px; cursor: pointer;">
                                    <?= IconHelper::render('trash', 13) ?> Eliminar
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
