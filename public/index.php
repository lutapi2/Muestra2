<?php
/**
 * Plataforma de Selección y Contratación de Personal - CECAR
 * Punto de entrada principal (Front Controller)
 */

declare(strict_types=1);

// Cargar configuración general
require_once dirname(__DIR__) . '/config/app.php';

// Autoloader PSR-4 para clases del espacio de nombres App\
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = ROOT_PATH . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Inicializar sesión segura (RNF-012)
\App\Helpers\AuthHelper::initSession();

// Asegurar base de datos inicializada y archivos de soporte semilla
try {
    \App\Core\Database::getConnection();
    \App\Helpers\FileHelper::asegurarArchivosSemilla();
} catch (\Throwable $e) {
    error_log("Error inicialización: " . $e->getMessage());
}

// Despachar la solicitud mediante el Router
try {
    \App\Core\Router::dispatch();
} catch (\Throwable $e) {
    error_log("Error en la ejecución de la aplicación: " . $e->getMessage() . " en " . $e->getFile() . ":" . $e->getLine());
    http_response_code(500);

    if (APP_ENV === 'development') {
        echo "<div style='font-family: sans-serif; padding: 2rem; background: #fee2e2; border: 1px solid #ef4444; border-radius: 8px; margin: 2rem auto; max-width: 800px;'>";
        echo "<h2 style='color: #991b1b;'>Error 500 en la Aplicación</h2>";
        echo "<p><strong>Mensaje:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>Archivo:</strong> " . htmlspecialchars($e->getFile()) . " (Línea: " . $e->getLine() . ")</p>";
        echo "<pre style='background: #fff; padding: 1rem; border-radius: 6px; overflow: auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        echo "</div>";
    } else {
        echo "<div style='font-family: sans-serif; padding: 3rem; text-align: center;'>";
        echo "<h2>Error Inesperado</h2><p>Ocurrió un error en el servidor. Por favor comuníquese con Mesa de Ayuda CECAR.</p>";
        echo "</div>";
    }
}
