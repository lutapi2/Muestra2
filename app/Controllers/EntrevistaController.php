<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Helpers\AuthHelper;
use App\Helpers\AuditHelper;
use App\Helpers\NotifyHelper;
use App\Models\Entrevista;
use App\Models\Convocatoria;
use App\Models\Postulacion;
use App\Models\Usuario;

class EntrevistaController {
    public function index(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano', 'entrevistador']);

        $entrevistas = Entrevista::listar();
        $convocatorias = Convocatoria::getAll();

        // Get preselected candidates ready for interview
        $pdo = \App\Core\Database::getConnection();
        $stmtPre = $pdo->query("
            SELECT p.*, u.nombres, u.apellidos, u.numero_documento, c.cargo
            FROM postulaciones p
            JOIN usuarios u ON p.usuario_id = u.id
            JOIN convocatorias c ON p.convocatoria_id = c.id
            WHERE p.estado IN ('preseleccionada', 'habilitada')
              AND p.id NOT IN (SELECT postulacion_id FROM entrevistas WHERE estado IN ('programada', 'confirmada'))
        ");
        $candidatosDisponibles = $stmtPre->fetchAll();
        $entrevistadores = Usuario::getAll('entrevistador');

        View::render('entrevistas.index', [
            'entrevistas' => $entrevistas,
            'convocatorias' => $convocatorias,
            'candidatosDisponibles' => $candidatosDisponibles,
            'entrevistadores' => $entrevistadores
        ], 'main');
    }

    public function programar(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano']);

        $postulacionId = (int)$request->post('postulacion_id');
        $fecha = $request->post('fecha');
        $horaInicio = $request->post('hora_inicio');
        $horaFin = $request->post('hora_fin');
        $modalidad = $request->post('modalidad', 'virtual');
        $ubicacion = trim($request->post('ubicacion', 'Campus CECAR'));
        $enlace = trim($request->post('enlace', ''));
        $entrevistadores = $request->post('entrevistadores', []);

        try {
            $entrevistaId = Entrevista::programar([
                'postulacion_id' => $postulacionId,
                'fecha' => $fecha,
                'hora_inicio' => $horaInicio,
                'hora_fin' => $horaFin,
                'modalidad' => $modalidad,
                'ubicacion' => $ubicacion,
                'enlace' => $enlace,
                'entrevistadores' => $entrevistadores
            ]);

            $postulacion = Postulacion::findById($postulacionId);
            AuditHelper::log('programar_entrevista', 'entrevistas', $entrevistaId);
            AuditHelper::logMovimiento($postulacionId, 'programacion_entrevista', 'preseleccionada', 'preseleccionada', "Entrevista fijada para el $fecha de $horaInicio a $horaFin ($modalidad).");

            NotifyHelper::enviar(
                $postulacion['usuario_id'],
                'Programación de Entrevista de Selección',
                "Se ha agendado su entrevista para el día $fecha a las $horaInicio. Modalidad: $modalidad. " . ($enlace ? "Enlace: $enlace" : "Lugar: $ubicacion"),
                $postulacionId
            );

            AuthHelper::setFlash('success', '¡Entrevista agendada exitosamente sin conflictos de horario!');
        } catch (\Exception $e) {
            AuthHelper::setFlash('danger', 'Error al programar: ' . $e->getMessage());
        }

        Response::redirect('c=Entrevista&a=index');
    }

    public function calificar(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano', 'entrevistador']);

        $entrevistaId = (int)$request->post('entrevista_id');
        $puntaje = (float)$request->post('puntaje');
        $concepto = $request->post('concepto'); // favorable, desfavorable, condicionado
        $observacion = trim($request->post('observacion', ''));

        Entrevista::registrarEvaluacion($entrevistaId, AuthHelper::getUsuarioId(), $puntaje, $concepto, $observacion);
        AuditHelper::log('evaluar_entrevista', 'entrevistas', $entrevistaId, null, ['puntaje' => $puntaje, 'concepto' => $concepto]);

        AuthHelper::setFlash('success', 'Calificación individual de entrevista registrada con éxito.');
        Response::redirect('c=Entrevista&a=index');
    }
}
