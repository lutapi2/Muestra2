<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\View;
use App\Models\Convocatoria;

class HomeController {
    public function landing(Request $request): void {
        $convocatorias = Convocatoria::getPublicadas();
        View::render('home.landing', [
            'convocatorias' => $convocatorias
        ], 'landing');
    }

    public function comoFunciona(Request $request): void {
        View::render('home.como_funciona', [], 'landing');
    }

    public function tutorial(Request $request): void {
        $layout = \App\Helpers\AuthHelper::estaAutenticado() ? 'main' : 'landing';
        View::render('home.tutorial', [], $layout);
    }
}
