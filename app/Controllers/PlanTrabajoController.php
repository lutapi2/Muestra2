<?php

class PlanTrabajoController {
    public function index(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano', 'revisor']);

        $convocatoriaId = (int)$request->get('convocatoria_id', 1);
        $planes = PlanTrabajo::listarPorConvocatoria($convocatoriaId);
        $convocatorias = Convocatoria::getAll();

        $actividades = [];
        if (!empty($planes[0]['id'])) {
            $actividades = PlanTrabajo::getActividades($planes[0]['id']);
        }

        View::render('plan_trabajo.index', [
            'planes' => $planes,
            'actividades' => $actividades,
            'convocatorias' => $convocatorias,
            'convocatoriaId' => $convocatoriaId
        ], 'main');
    }
}
