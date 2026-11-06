<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Helpers\AuthHelper;
use App\Helpers\AuditHelper;
use App\Models\Convocatoria;

class ConvocatoriaController {
    public function index(Request $request): void {
        $filtros = [
            'facultad' => $request->get('facultad', ''),
            'buscar' => $request->get('buscar', '')
        ];

        if (!AuthHelper::estaAutenticado() || AuthHelper::esCandidato()) {
            $filtros['estado'] = 'publicada';
        } else {
            $filtros['estado'] = $request->get('estado', '');
        }

        $convocatorias = Convocatoria::getAll($filtros);

        View::render('convocatorias.index', [
            'convocatorias' => $convocatorias,
            'filtros' => $filtros,
            'esGestor' => AuthHelper::esTalentoHumano()
        ], 'main');
    }

    public function detalle(Request $request): void {
        $id = (int)$request->get('id');
        $convocatoria = Convocatoria::findById($id);

        if (!$convocatoria) {
            AuthHelper::setFlash('danger', 'Convocatoria no encontrada.');
            Response::redirect('c=Convocatoria&a=index');
        }

        $pdo = \App\Core\Database::getConnection();
        $stmtReq = $pdo->prepare("SELECT * FROM requisitos_convocatoria WHERE convocatoria_id = :id");
        $stmtReq->execute([':id' => $id]);
        $requisitos = $stmtReq->fetchAll();

        $stmtCrit = $pdo->prepare("SELECT * FROM criterios_evaluacion WHERE convocatoria_id = :id");
        $stmtCrit->execute([':id' => $id]);
        $criterios = $stmtCrit->fetchAll();

        View::render('convocatorias.detalle', [
            'convocatoria' => $convocatoria,
            'requisitos' => $requisitos,
            'criterios' => $criterios
        ], 'main');
    }

    public function crear(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano']);

        $pdo = \App\Core\Database::getConnection();
        $dependencias = $pdo->query("SELECT * FROM dependencias WHERE activa = 1 ORDER BY nombre ASC")->fetchAll();

        View::render('convocatorias.crear', [
            'dependencias' => $dependencias
        ], 'main');
    }

    public function guardar(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano']);

        try {
            $convId = Convocatoria::crear([
                'codigo' => $request->post('codigo'),
                'cargo' => $request->post('cargo'),
                'dependencia_id' => $request->post('dependencia_id'),
                'facultad' => $request->post('facultad'),
                'programa' => $request->post('programa'),
                'sede' => $request->post('sede', 'Sincelejo'),
                'periodo_academico' => $request->post('periodo_academico', '2026-2'),
                'vacantes' => $request->post('vacantes', 1),
                'modalidad' => $request->post('modalidad', 'Tiempo Completo'),
                'tipo_convocatoria' => $request->post('tipo_convocatoria', 'Docente'),
                'descripcion' => $request->post('descripcion'),
                'fecha_apertura' => $request->post('fecha_apertura'),
                'fecha_cierre' => $request->post('fecha_cierre'),
                'estado' => $request->post('estado', 'publicada'),
                'formacion_minima' => $request->post('formacion_minima'),
                'experiencia_minima_meses' => $request->post('experiencia_minima_meses', 12),
                'competencias' => $request->post('competencias'),
                'funciones' => $request->post('funciones'),
                'condiciones' => $request->post('condiciones'),
                'creado_por' => AuthHelper::getUsuarioId()
            ]);

            AuditHelper::log('crear_convocatoria', 'convocatorias', $convId);
            AuthHelper::setFlash('success', '¡Convocatoria creada y publicada exitosamente!');
            Response::redirect('c=Convocatoria&a=detalle&id=' . $convId);
        } catch (\Exception $e) {
            AuthHelper::setFlash('danger', 'Error al guardar convocatoria: ' . $e->getMessage());
            Response::redirect('c=Convocatoria&a=crear');
        }
    }

    public function cambiarEstado(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano']);

        $id = (int)$request->post('id');
        $estado = $request->post('estado');
        $motivo = $request->post('motivo_retiro', null);

        Convocatoria::cambiarEstado($id, $estado, $motivo);
        AuditHelper::log('cambiar_estado_convocatoria', 'convocatorias', $id, null, ['estado' => $estado, 'motivo' => $motivo]);

        AuthHelper::setFlash('success', "Estado de la convocatoria modificado a: {$estado}.");
        Response::redirect('c=Convocatoria&a=detalle&id=' . $id);
    }
}
