<?php

declare(strict_types=1);

/**
 * Front Controller del Sistema de Selección de Personal CECAR v3.0 (2026).
 * Punto único de entrada para todas las peticiones HTTP (API y Vistas HTML5).
 * 
 * Satisface:
 *   - Enrutamiento desacoplado sin frameworks (PHP 8.2).
 *   - Despacho de Vistas HTML5 (Landing, Registro RF-001, Login RF-002, Convocatorias RF-011, Dashboard).
 *   - Despacho de API JSON con códigos de respuesta HTTP: 200, 201, 400, 401, 403, 404, 405, 500.
 *   - RNF-024: Manejo centralizado de excepciones sin fuga de trazas internas ni credenciales.
 */

require_once __DIR__ . '/../autoload.php';

use Controller\AuthController;

// Configurar encabezados globales de seguridad
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Responder inmediatamente a peticiones preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

/**
 * Normaliza la ruta solicitada extrayéndola de PATH_INFO o REQUEST_URI,
 * descontando el subdirectorio de despliegue en servidores como Apache/XAMPP.
 */
function resolveRequestPath(): string
{
    if (!empty($_SERVER['PATH_INFO'])) {
        return '/' . trim($_SERVER['PATH_INFO'], '/');
    }

    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $parsedPath = parse_url($uri, PHP_URL_PATH);
    if ($parsedPath === false || $parsedPath === null) {
        $parsedPath = '/';
    }

    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $scriptDir = dirname($scriptName);

    // Si el script está en un subdirectorio (ej. /seleccion_cecar/public)
    if ($scriptDir !== '/' && $scriptDir !== '\\' && strpos($parsedPath, $scriptDir) === 0) {
        $parsedPath = substr($parsedPath, strlen($scriptDir));
    }

    // Si la URI incluye "index.php" explícitamente
    if (strpos($parsedPath, '/index.php') === 0) {
        $parsedPath = substr($parsedPath, strlen('/index.php'));
    }

    $cleanPath = '/' . trim($parsedPath, '/');
    return $cleanPath === '//' ? '/' : $cleanPath;
}

/**
 * Emite respuestas de error JSON estructuradas.
 */
function sendJsonError(int $statusCode, string $message, ?array $error = null): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=UTF-8');

    $timezone = new DateTimeZone('America/Bogota');
    $now = new DateTimeImmutable('now', $timezone);

    echo json_encode([
        'success'   => false,
        'status'    => $statusCode,
        'message'   => $message,
        'data'      => null,
        'error'     => $error,
        'timestamp' => $now->format(DateTimeInterface::ATOM),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Renderiza una vista PHP en el directorio view/.
 */
function renderView(string $viewPath): void
{
    $fullPath = __DIR__ . '/../view/' . ltrim($viewPath, '/');
    if (file_exists($fullPath)) {
        require_once $fullPath;
        exit;
    }
    sendJsonError(404, "La vista solicitada no fue encontrada en el sistema.");
}

try {
    $requestMethod = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    $requestPath   = resolveRequestPath();

    // Tabla de enrutamiento estricta del sistema CECAR
    $routes = [
        // =====================================================================
        // ENDPOINTS DE API (JSON)
        // =====================================================================
        'POST' => [
            '/api/auth/register' => [AuthController::class, 'register'],
            '/api/auth/login'    => [AuthController::class, 'login'],
        ],
        'GET' => [
            // Endpoints API
            '/api/health' => function () {
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode([
                    'status'    => 'UP',
                    'sistema'   => 'Sistema de Selección CECAR v3.0',
                    'timestamp' => (new DateTimeImmutable('now', new DateTimeZone('America/Bogota')))->format(DateTimeInterface::ATOM),
                    'version'   => '3.0.0',
                ]);
                exit;
            },

            // =================================================================
            // VISTAS HTML5 (Inspiradas en los Mockups Oficiales de CECAR)
            // =================================================================
            '/'              => fn() => renderView('home/index.php'),
            '/registro'      => fn() => renderView('auth/registro.php'),
            '/login'         => fn() => renderView('auth/login.php'),
            '/convocatorias' => fn() => renderView('convocatorias/index.php'),
            '/dashboard'     => fn() => renderView('candidato/dashboard.php'),
        ],
    ];

    // Verificar si la ruta solicitada coincide directamente
    if (isset($routes[$requestMethod][$requestPath])) {
        $handler = $routes[$requestMethod][$requestPath];
        if (is_callable($handler)) {
            $handler();
        } elseif (is_array($handler)) {
            [$controllerClass, $action] = $handler;
            $controller = new $controllerClass();
            $controller->$action();
        }
        exit;
    }

    // Verificar si la ruta existe bajo otro método HTTP (405 Method Not Allowed)
    $allowedMethods = [];
    foreach ($routes as $method => $paths) {
        if (isset($paths[$requestPath])) {
            $allowedMethods[] = $method;
        }
    }

    if (!empty($allowedMethods)) {
        header('Allow: ' . implode(', ', $allowedMethods));
        sendJsonError(
            405,
            "Método HTTP '{$requestMethod}' no permitido para la ruta '{$requestPath}'. Métodos permitidos: " . implode(', ', $allowedMethods) . '.',
            ['metodos_permitidos' => $allowedMethods]
        );
    }

    // Si no se encuentra la ruta en ningún método: 404 Not Found
    sendJsonError(
        404,
        "El recurso o endpoint solicitado '{$requestPath}' no existe en el Sistema de Selección CECAR.",
        ['ruta' => $requestPath]
    );

} catch (Throwable $e) {
    // Satisface RNF-024: Registro interno seguro del error sin fugar trazas al cliente
    error_log(sprintf(
        "[CECAR 500 ERROR] [%s] %s en %s:%d\nTraza:\n%s",
        date('Y-m-d H:i:s'),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine(),
        $e->getTraceAsString()
    ));

    sendJsonError(
        500,
        'Ocurrió un error interno en el servidor. Por favor intente más tarde o reporte este evento a soporte técnico.',
        ['codigo_error' => 'ERR_INTERNAL_SERVER']
    );
}
