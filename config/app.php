<?php
/**
 * Plataforma de Contratación y Selección de Personal - CECAR
 * Configuración General del Sistema
 */

date_default_timezone_set('America/Bogota');

define('APP_NAME', 'Plataforma de Contratación CECAR');
define('APP_VERSION', '3.0.0');
define('APP_ENV', 'development');

define('ROOT_PATH', dirname(__DIR__));
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('UPLOADS_PATH', STORAGE_PATH . '/uploads');
define('VIEWS_PATH', ROOT_PATH . '/views');

// Base URL calculation
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
if ($scriptDir === '/' || $scriptDir === '') {
    $baseUrl = '';
} else {
    $baseUrl = rtrim($scriptDir, '/');
}
define('BASE_URL', $baseUrl);

// Subsanación y cargas
define('MAX_FILE_SIZE_BYTES', 10 * 1024 * 1024); // 10 MB
define('ALLOWED_MIME_TYPES', [
    'application/pdf' => 'pdf',
    'image/jpeg' => 'jpg',
    'image/png' => 'png'
]);

define('DEFAULT_SUBSANACION_DIAS', 5);
define('SESSION_TIMEOUT_SECONDS', 3600); // 1 hora
