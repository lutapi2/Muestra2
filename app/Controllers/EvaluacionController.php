<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Helpers\AuthHelper;
use App\Helpers\AuditHelper;
use App\Helpers\NotifyHelper;
use App\Models\Postulacion;
use App\Models\Convocatoria;

class EvaluacionController {
    public function matriz(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano', 'entrevistador', 'desarrollo_profesoral']);

        $convocatoriaId = (int)$request->get('convocatoria_id', 1);
        $convocatorias = Convocatoria::getAll();

        $pdo = \App\Core\Database::getConnection();
        $stmtCriterios = $pdo->prepare("SELECT * FROM criterios_evaluacion WHERE convocatoria_id = :cid ORDER BY etapa, id ASC");
        $stmtCriterios->execute([':cid' => $convocatoriaId]);
        $criterios = $stmtCriterios->fetchAll();

        $stmtPost = $pdo->prepare("
            SELECT p.*, u.nombres, u.apellidos, u.numero_documento,
                   (SELECT calificacion FROM pruebas WHERE postulacion_id = p.id AND tipo = 'psicotecnica' LIMIT 1) as nota_psicotecnica,
                   (SELECT calificacion FROM pruebas WHERE postulacion_id = p.id AND tipo = 'aula_docente' LIMIT 1) as nota_aula,
                   (SELECT puntaje FROM resultados_entrevista WHERE entrevista_id IN (SELECT id FROM entrevistas WHERE postulacion_id = p.id) AND tipo = 'consolidado' LIMIT 1) as nota_entrevista
            FROM postulaciones p
            JOIN usuarios u ON p.usuario_id = u.id
            WHERE p.convocatoria_id = :cid AND p.estado NOT IN ('borrador', 'retirada')
            ORDER BY p.id ASC
        ");
        $stmtPost->execute([':cid' => $convocatoriaId]);
        $postulaciones = $stmtPost->fetchAll();

        // Get causales de descarte
        $causales = $pdo->query("SELECT * FROM catalogos WHERE grupo = 'causales_descarte' AND activo = 1 ORDER BY orden ASC")->fetchAll();

        View::render('evaluacion.matriz', [
            'convocatorias' => $convocatorias,
            'convocatoriaId' => $convocatoriaId,
            'criterios' => $criterios,
            'postulaciones' => $postulaciones,
            'causales' => $causales
        ], 'main');
    }

    public function registrarPrueba(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano', 'entrevistador', 'desarrollo_profesoral']);

        $postulacionId = (int)$request->post('postulacion_id');
        $tipo = $request->post('tipo'); // psicotecnica, conocimiento, aula_docente
        $calificacion = (float)$request->post('calificacion', 0);
        $dictamen = $request->post('dictamen', 'aprobado');
        $observaciones = trim($request->post('observaciones', ''));

        $pdo = \App\Core\Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO pruebas (postulacion_id, tipo, responsable_id, calificacion, dictamen, observaciones, fecha_registro)
            VALUES (:pid, :tipo, :rid, :calif, :dictamen, :obs, CURRENT_TIMESTAMP)
        ");
        $stmt->execute([
            ':pid' => $postulacionId,
            ':tipo' => $tipo,
            ':rid' => AuthHelper::getUsuarioId(),
            ':calif' => $calificacion,
            ':dictamen' => $dictamen,
            ':obs' => $observaciones
        ]);

        AuditHelper::log('registrar_prueba', 'pruebas', $postulacionId, null, ['tipo' => $tipo, 'calificacion' => $calificacion, 'dictamen' => $dictamen]);
        AuthHelper::setFlash('success', "Prueba de " . str_replace('_', ' ', $tipo) . " registrada exitosamente.");
        Response::redirect('c=Evaluacion&a=matriz&convocatoria_id=' . $request->post('convocatoria_id'));
    }

    public function tomarDecision(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano']);

        $postulacionId = (int)$request->post('postulacion_id');
        $decision = $request->post('decision'); // preseleccionar, descartar, seleccionar, elegible
        $fundamento = trim($request->post('fundamento', ''));
        $causalId = $request->post('causal_descarte_id', null);

        $postulacion = Postulacion::findById($postulacionId);
        $pdo = \App\Core\Database::getConnection();

        if ($decision === 'preseleccionar') {
            Postulacion::actualizarEstado($postulacionId, 'preseleccionada', 'entrevista');
            AuditHelper::logMovimiento($postulacionId, 'preseleccion', $postulacion['estado'], 'preseleccionada', $fundamento);
            AuthHelper::setFlash('success', 'Aspirante marcado como Preseleccionado para fase de entrevistas.');
        } elseif ($decision === 'descartar') {
            if (empty($causalId)) {
                AuthHelper::setFlash('danger', 'Debe seleccionar una causal de descarte del catálogo institucional (RN-024).');
                Response::redirect('c=Evaluacion&a=matriz&convocatoria_id=' . $postulacion['convocatoria_id']);
            }
            Postulacion::actualizarEstado($postulacionId, 'descartada', null, $fundamento, $causalId);
            AuditHelper::logMovimiento($postulacionId, 'descarte', $postulacion['estado'], 'descartada', "Descarte aplicado. Causal: {$causalId}. Fundamento: {$fundamento}");
            AuthHelper::setFlash('warning', 'Postulación descartada con causal oficial registrada.');
        } elseif ($decision === 'seleccionar') {
            Postulacion::actualizarEstado($postulacionId, 'seleccionada', 'contratacion');

            $stmtDec = $pdo->prepare("
                INSERT OR REPLACE INTO decisiones_finales (postulacion_id, resultado, fundamento, usuario_id, fecha_publicacion)
                VALUES (:pid, 'seleccionado', :fundamento, :uid, CURRENT_TIMESTAMP)
            ");
            $stmtDec->execute([
                ':pid' => $postulacionId,
                ':fundamento' => $fundamento,
                ':uid' => AuthHelper::getUsuarioId()
            ]);

            // Ensure contratacion row exists
            $stmtCnt = $pdo->prepare("
                INSERT OR IGNORE INTO contrataciones (postulacion_id, responsable_id, estado_formalizacion, estado_carga_documentos)
                VALUES (:pid, :uid, 'pendiente', 'incompleta')
            ");
            $stmtCnt->execute([':pid' => $postulacionId, ':uid' => AuthHelper::getUsuarioId()]);

            AuditHelper::logMovimiento($postulacionId, 'decision_final', $postulacion['estado'], 'seleccionada', "Candidato seleccionado formalmente: {$fundamento}");
            NotifyHelper::enviar(
                $postulacion['usuario_id'],
                '¡Ha sido Seleccionado en la Convocatoria!',
                "Nos complace informarle que ha sido Seleccionado en el proceso de convocatoria para {$postulacion['convocatoria_cargo']}. Por favor ingrese a cargar sus documentos de contratación.",
                $postulacionId
            );

            AuthHelper::setFlash('success', 'Candidato formalmente Seleccionado. Pasa a módulo de Contratación de Seleccionados.');
        }

        Response::redirect('c=Evaluacion&a=matriz&convocatoria_id=' . $postulacion['convocatoria_id']);
    }
}
