<?php
namespace App\Core;

use App\Helpers\AuthHelper;

class Router {
    public static function dispatch(): void {
        $request = new Request();

        $controllerName = $request->get('c');
        $actionName = $request->get('a');

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

        $controllerClass = "App\\Controllers\\" . ucfirst($controllerName) . "Controller";

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
}
