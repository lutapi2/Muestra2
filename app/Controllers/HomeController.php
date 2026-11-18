<?php

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
        $layout = AuthHelper::estaAutenticado() ? 'main' : 'landing';
        View::render('home.tutorial', [], $layout);
    }
}
