<?php
use App\Helpers\AuthHelper;
use App\Helpers\NotifyHelper;

$usuarioActual = AuthHelper::getUsuarioActual();
$rol = AuthHelper::getRol();
$esGestor = AuthHelper::esTalentoHumano();
$esRevisor = AuthHelper::esRevisor();
$esAdmin = AuthHelper::esAdmin();
$esCandidato = AuthHelper::esCandidato();
$notificaciones = AuthHelper::estaAutenticado() ? NotifyHelper::obtenerNoLeidas(AuthHelper::getUsuarioId()) : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/theme.css">
    <script src="<?= BASE_URL ?>/js/app.js" defer></script>
    <script src="<?= BASE_URL ?>/js/tutorial.js" defer></script>
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar (Left) -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="sidebar-brand-icon">C</div>
                <div class="sidebar-brand-text">
                    <h2>CECAR</h2>
                    <span>Gestión de Talento Humano</span>
                </div>
            </div>

            <ul class="sidebar-menu">
                <div class="sidebar-section-title">Principal</div>
                <li>
                    <a href="<?= BASE_URL ?>/index.php?c=Dashboard&a=index" class="sidebar-link <?= (($_GET['c'] ?? '') === 'Dashboard' || empty($_GET['c'])) ? 'active' : '' ?>">
                        <span class="icon">📊</span>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/index.php?c=MesaAyuda&a=index" class="sidebar-link <?= (($_GET['c'] ?? '') === 'MesaAyuda') ? 'active' : '' ?>">
                        <span class="icon">🎧</span>
                        <span>Mesa de Ayudas</span>
                    </a>
                </li>

                <div class="sidebar-section-title">Contratación y Selección</div>
                <li>
                    <a href="<?= BASE_URL ?>/index.php?c=Contratacion&a=index" class="sidebar-link <?= (($_GET['c'] ?? '') === 'Contratacion') ? 'active' : '' ?>">
                        <span class="icon">📁</span>
                        <span>Procesos de selección</span>
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/index.php?c=Convocatoria&a=index" class="sidebar-link <?= (($_GET['c'] ?? '') === 'Convocatoria') ? 'active' : '' ?>">
                        <span class="icon">📢</span>
                        <span>Convocatorias</span>
                    </a>
                </li>

                <?php if ($esRevisor || $esGestor): ?>
                    <li>
                        <a href="<?= BASE_URL ?>/index.php?c=Revision&a=index" class="sidebar-link <?= (($_GET['c'] ?? '') === 'Revision') ? 'active' : '' ?>">
                            <span class="icon">🔎</span>
                            <span>Revisión Documental</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/index.php?c=Evaluacion&a=matriz" class="sidebar-link <?= (($_GET['c'] ?? '') === 'Evaluacion') ? 'active' : '' ?>">
                            <span class="icon">📝</span>
                            <span>Matriz y Pruebas</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/index.php?c=Entrevista&a=index" class="sidebar-link <?= (($_GET['c'] ?? '') === 'Entrevista') ? 'active' : '' ?>">
                            <span class="icon">👥</span>
                            <span>Entrevistas</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/index.php?c=Kanban&a=index" class="sidebar-link <?= (($_GET['c'] ?? '') === 'Kanban') ? 'active' : '' ?>">
                            <span class="icon">📋</span>
                            <span>Tablero Kanban</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/index.php?c=PlanTrabajo&a=index" class="sidebar-link <?= (($_GET['c'] ?? '') === 'PlanTrabajo') ? 'active' : '' ?>">
                            <span class="icon">🗓️</span>
                            <span>Plan de Trabajo</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($esRevisor || $esGestor || $rol === 'auditor'): ?>
                    <div class="sidebar-section-title">Analítica</div>
                    <li>
                        <a href="<?= BASE_URL ?>/index.php?c=Reportes&a=index" class="sidebar-link <?= (($_GET['c'] ?? '') === 'Reportes') ? 'active' : '' ?>">
                            <span class="icon">📈</span>
                            <span>Reportes e Informes</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($esAdmin): ?>
                    <div class="sidebar-section-title">Sistema</div>
                    <li>
                        <a href="<?= BASE_URL ?>/index.php?c=Admin&a=usuarios" class="sidebar-link <?= (($_GET['c'] ?? '') === 'Admin' && ($_GET['a'] ?? '') === 'usuarios') ? 'active' : '' ?>">
                            <span class="icon">👤</span>
                            <span>Usuarios y Roles</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/index.php?c=Admin&a=catalogos" class="sidebar-link <?= (($_GET['c'] ?? '') === 'Admin' && ($_GET['a'] ?? '') === 'catalogos') ? 'active' : '' ?>">
                            <span class="icon">⚙️</span>
                            <span>Catálogos y Causales</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/index.php?c=Admin&a=auditoria" class="sidebar-link <?= (($_GET['c'] ?? '') === 'Admin' && ($_GET['a'] ?? '') === 'auditoria') ? 'active' : '' ?>">
                            <span class="icon">🛡️</span>
                            <span>Auditoría de Eventos</span>
                        </a>
                    </li>
                <?php endif; ?>

                <li style="margin-top: 1.5rem;">
                    <a href="<?= BASE_URL ?>/index.php?c=Auth&a=logout" class="sidebar-link" style="color: #ef4444;">
                        <span class="icon">🚪</span>
                        <span>Cerrar Sesión</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">
                        <?= strtoupper(substr(AuthHelper::getNombreCompleto(), 0, 1)) ?>
                    </div>
                    <div style="overflow: hidden;">
                        <div style="font-size: 0.82rem; font-weight: 700; color: #ffffff; white-space: nowrap; text-overflow: ellipsis;">
                            <?= htmlspecialchars(AuthHelper::getNombreCompleto(), ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <div style="font-size: 0.72rem; color: #34d399;">
                            <?= htmlspecialchars(AuthHelper::getRolNombre(), ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Topbar -->
            <header class="topbar">
                <div class="topbar-left">
                    <span style="color: #64748b; font-size: 0.88rem; font-weight: 600;">
                        <?= htmlspecialchars(AuthHelper::getDependenciaNombre(), ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <span class="quick-role-badge">
                        Rol Activo: <?= htmlspecialchars(AuthHelper::getRolNombre(), ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </div>

                <div class="topbar-right">
                    <!-- Quick Demo Role Switcher for seamless pair evaluation -->
                    <div style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.78rem;">
                        <span style="color: #64748b; font-weight: 600;">Ver como:</span>
                        <a href="<?= BASE_URL ?>/index.php?c=Auth&a=quickLogin&rol=talento_humano" class="btn btn-secondary btn-sm" style="padding: 0.2rem 0.45rem;">Talento Humano</a>
                        <a href="<?= BASE_URL ?>/index.php?c=Auth&a=quickLogin&rol=revisor" class="btn btn-secondary btn-sm" style="padding: 0.2rem 0.45rem;">Revisor</a>
                        <a href="<?= BASE_URL ?>/index.php?c=Auth&a=quickLogin&rol=candidato" class="btn btn-secondary btn-sm" style="padding: 0.2rem 0.45rem;">Candidato</a>
                        <a href="<?= BASE_URL ?>/index.php?c=Auth&a=quickLogin&rol=admin" class="btn btn-secondary btn-sm" style="padding: 0.2rem 0.45rem;">Admin</a>
                    </div>

                    <!-- Notificaciones -->
                    <div style="position: relative;">
                        <button onclick="document.getElementById('notifDropdown').classList.toggle('open')" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; position: relative;" title="Notificaciones del portal">
                            🔔
                            <?php if (count($notificaciones) > 0): ?>
                                <span style="position: absolute; top: -4px; right: -4px; background: #ef4444; color: #fff; font-size: 0.65rem; border-radius: 50%; width: 16px; height: 16px; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                    <?= count($notificaciones) ?>
                                </span>
                            <?php endif; ?>
                        </button>
                        <div id="notifDropdown" style="display: none; position: absolute; right: 0; top: 35px; width: 320px; background: #fff; border: 1px solid var(--border-color); border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 50; padding: 0.75rem;">
                            <div style="font-size: 0.82rem; font-weight: 700; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.4rem; margin-bottom: 0.5rem; display: flex; justify-content: space-between;">
                                <span>Avisos del Portal</span>
                                <span style="color: #10b981;"><?= count($notificaciones) ?> pendientes</span>
                            </div>
                            <?php if (empty($notificaciones)): ?>
                                <div style="font-size: 0.8rem; color: #94a3b8; text-align: center; padding: 1rem;">No hay avisos pendientes.</div>
                            <?php else: ?>
                                <?php foreach ($notificaciones as $n): ?>
                                    <div style="padding: 0.4rem 0; border-bottom: 1px solid #f8fafc; font-size: 0.78rem;">
                                        <strong><?= htmlspecialchars($n['asunto'], ENT_QUOTES, 'UTF-8') ?></strong>
                                        <div style="color: #64748b;"><?= htmlspecialchars(substr($n['mensaje'], 0, 75), ENT_QUOTES, 'UTF-8') ?>...</div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <a href="<?= BASE_URL ?>/index.php?c=Auth&a=logout" class="btn btn-secondary btn-sm" style="color: #ef4444;" title="Cerrar sesión">
                        Salir
                    </a>
                </div>
            </header>

            <!-- Page Container -->
            <div class="page-container">
                <!-- Flash Messages -->
                <?php $flashes = AuthHelper::getFlashes(); ?>
                <?php if (!empty($flashes)): ?>
                    <?php foreach ($flashes as $f): ?>
                        <div class="alert alert-<?= htmlspecialchars($f['tipo'], ENT_QUOTES, 'UTF-8') ?>">
                            <span><?= htmlspecialchars($f['mensaje'], ENT_QUOTES, 'UTF-8') ?></span>
                            <button onclick="this.parentElement.remove()" style="background: none; border: none; font-size: 1.1rem; cursor: pointer; color: inherit;">&times;</button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Render View Content -->
                <?= $content ?? '' ?>
            </div>
        </div>
    </div>

    <!-- Floating Tutorial Pill (Bottom Left) matching screenshot -->
    <a href="javascript:void(0)" onclick="toggleTutorialModal()" class="tutorial-pill-floating">
        <span style="background: rgba(255,255,255,0.25); border-radius: 50%; width: 18px; height: 18px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem;">ℹ</span>
        Tutorial
    </a>
</body>
</html>
