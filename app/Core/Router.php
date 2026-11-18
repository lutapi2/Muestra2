<?php

class Router {
    public static function dispatch(): void {
        $request = new Request();

        $controllerName = $request->get('c');
        $actionName = $request->get('a');

        // Soporte automático para URLs amigables (/registro, /login, /convocatorias, etc.)
        if (empty($controllerName)) {
            $path = self::resolvePath();
            $friendlyRoute = self::matchFriendlyRoute($path);
            if ($friendlyRoute !== null) {
                $controllerName = $friendlyRoute[0];
                $actionName = $friendlyRoute[1];
            }
        }

        if (empty($controllerName)) {
            if (AuthHelper::estaAutenticado()) {
                $controllerName = 'Dashboard';
                $actionName = $actionName ?: 'index';
            } else {
                $controllerName = 'Home';
                $actionName = $actionName ?: 'landing';
            }
        }

        if (empty($actionName)) {
            $actionName = 'index';
        }

        $controllerClass = ucfirst($controllerName) . "Controller";

        if (!class_exists($controllerClass)) {
            http_response_code(404);
            View::render('home.404', ['mensaje' => "Controlador '{$controllerName}' no encontrado"], 'main');
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $actionName)) {
            http_response_code(404);
            View::render('home.404', ['mensaje' => "Acción '{$actionName}' no encontrada en {$controllerName}"], 'main');
            return;
        }

        // Call action
        $controller->$actionName($request);
    }

    private static function resolvePath(): string {
        if (!empty($_SERVER['PATH_INFO'])) {
            return '/' . trim($_SERVER['PATH_INFO'], '/');
        }

        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);
        if ($path === false || $path === null) {
            $path = '/';
        }

        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $scriptDir = str_replace('\\', '/', dirname($scriptName));
        if ($scriptDir !== '/' && !empty($scriptDir) && strpos($path, $scriptDir) === 0) {
            $path = substr($path, strlen($scriptDir));
        }

        if (strpos($path, '/index.php') === 0) {
            $path = substr($path, strlen('/index.php'));
        }

        $clean = '/' . trim($path, '/');
        return $clean === '//' ? '/' : $clean;
    }

    private static function matchFriendlyRoute(string $path): ?array {
        $clean = strtolower(trim($path, '/'));
        if (empty($clean)) {
            return null;
        }

        $map = [
            'registro'              => ['Auth', 'register'],
            'register'              => ['Auth', 'register'],
            'login'                 => ['Auth', 'login'],
            'logout'                => ['Auth', 'logout'],
            'recuperar'             => ['Auth', 'recuperar'],
            'convocatorias'         => ['Convocatoria', 'index'],
            'convocatorias/crear'   => ['Convocatoria', 'crear'],
            'dashboard'             => ['Dashboard', 'index'],
            'admin/usuarios'        => ['Admin', 'usuarios'],
            'admin/catalogos'       => ['Admin', 'catalogos'],
            'admin/auditoria'       => ['Admin', 'auditoria'],
            'como-funciona'         => ['Home', 'comoFunciona'],
            'tutorial'              => ['Home', 'tutorial'],
        ];

        return $map[$clean] ?? null;
    }
}
