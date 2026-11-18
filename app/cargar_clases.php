<?php
/**
 * Carga todas las clases del sistema con require_once.
 * Si se crea un archivo nuevo en app/, hay que agregarlo a esta lista.
 */

// Core
require_once __DIR__ . '/Core/Database.php';
require_once __DIR__ . '/Core/Request.php';
require_once __DIR__ . '/Core/Response.php';
require_once __DIR__ . '/Core/Router.php';
require_once __DIR__ . '/Core/View.php';

// Helpers
require_once __DIR__ . '/Helpers/AuditHelper.php';
require_once __DIR__ . '/Helpers/AuthHelper.php';
require_once __DIR__ . '/Helpers/FileHelper.php';
require_once __DIR__ . '/Helpers/FormatHelper.php';
require_once __DIR__ . '/Helpers/IconHelper.php';
require_once __DIR__ . '/Helpers/NotifyHelper.php';

// Models
require_once __DIR__ . '/Models/Contratacion.php';
require_once __DIR__ . '/Models/Convocatoria.php';
require_once __DIR__ . '/Models/Entrevista.php';
require_once __DIR__ . '/Models/ItemHV.php';
require_once __DIR__ . '/Models/PlanTrabajo.php';
require_once __DIR__ . '/Models/Postulacion.php';
require_once __DIR__ . '/Models/TicketAyuda.php';
require_once __DIR__ . '/Models/Usuario.php';

// Controllers
require_once __DIR__ . '/Controllers/AdminController.php';
require_once __DIR__ . '/Controllers/AuthController.php';
require_once __DIR__ . '/Controllers/ContratacionController.php';
require_once __DIR__ . '/Controllers/ConvocatoriaController.php';
require_once __DIR__ . '/Controllers/DashboardController.php';
require_once __DIR__ . '/Controllers/DescargaController.php';
require_once __DIR__ . '/Controllers/EntrevistaController.php';
require_once __DIR__ . '/Controllers/EvaluacionController.php';
require_once __DIR__ . '/Controllers/HomeController.php';
require_once __DIR__ . '/Controllers/KanbanController.php';
require_once __DIR__ . '/Controllers/MesaAyudaController.php';
require_once __DIR__ . '/Controllers/PlanTrabajoController.php';
require_once __DIR__ . '/Controllers/PostulacionController.php';
require_once __DIR__ . '/Controllers/ReportesController.php';
require_once __DIR__ . '/Controllers/RevisionController.php';

