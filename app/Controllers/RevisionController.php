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
use App\Models\ItemHV;

class RevisionController {
    public function index(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano', 'revisor']);

        $pdo = \App\Core\Database::getConnection();
        $sql = "
            SELECT p.*, c.cargo, c.codigo as convocatoria_codigo, c.facultad,
                   u.nombres, u.apellidos, u.correo, u.numero_documento,
                   (SELECT COUNT(*) FROM items_hv i WHERE i.postulacion_id = p.id) as total_items,
                   (SELECT COUNT(*) FROM items_hv i WHERE i.postulacion_id = p.id AND i.estado = 'aprobado') as aprobados,
                   (SELECT COUNT(*) FROM items_hv i WHERE i.postulacion_id = p.id AND i.estado = 'observado') as observados,
                   (SELECT COUNT(*) FROM items_hv i WHERE i.postulacion_id = p.id AND i.estado = 'rechazado') as rechazados
            FROM postulaciones p
            JOIN convocatorias c ON p.convocatoria_id = c.id
            JOIN usuarios u ON p.usuario_id = u.id
            WHERE p.estado NOT IN ('borrador', 'retirada')
            ORDER BY p.fecha_envio DESC
        ";
        $postulaciones = $pdo->query($sql)->fetchAll();

        View::render('revision.index', [
            'postulaciones' => $postulaciones
        ], 'main');
    }

    public function expediente(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano', 'revisor']);

        $postulacionId = (int)$request->get('id');
        $postulacion = Postulacion::findById($postulacionId);

        if (!$postulacion) {
            AuthHelper::setFlash('danger', 'Postulación no encontrada.');
            Response::redirect('c=Revision&a=index');
        }

        $itemsPorCategoria = ItemHV::getOrganizadosPorCategoria($postulacionId);

        // Calculate counts
        $totalItems = 0;
        $aprobados = 0;
        $observados = 0;
        $rechazados = 0;
        $pendientes = 0;

        foreach ($itemsPorCategoria as $cat) {
            foreach ($cat['items'] as $item) {
                $totalItems++;
                if ($item['estado'] === 'aprobado') $aprobados++;
                elseif ($item['estado'] === 'observado') $observados++;
                elseif ($item['estado'] === 'rechazado') $rechazados++;
                else $pendientes++;
            }
        }

        View::render('revision.expediente', [
            'postulacion' => $postulacion,
            'itemsPorCategoria' => $itemsPorCategoria,
            'totalItems' => $totalItems,
            'aprobados' => $aprobados,
            'observados' => $observados,
            'rechazados' => $rechazados,
            'pendientes' => $pendientes
        ], 'main');
    }

    public function calificarItem(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano', 'revisor']);

        $itemId = (int)$request->post('item_id');
        $postulacionId = (int)$request->post('postulacion_id');
        $resultado = $request->post('resultado'); // aprobado, observado, rechazado
        $motivo = trim($request->post('motivo', ''));
        $observacion = trim($request->post('observacion', ''));

        if (in_array($resultado, ['observado', 'rechazado']) && empty($motivo)) {
            AuthHelper::setFlash('danger', 'Debe indicar el motivo claro de la observación o rechazo (RF-047).');
            Response::redirect('c=Revision&a=expediente&id=' . $postulacionId);
        }

        $pdo = \App\Core\Database::getConnection();

        // Register revision
        $stmtRev = $pdo->prepare("
            INSERT INTO revisiones_item (item_hv_id, revisor_id, resultado, motivo, observacion, fecha_revision)
            VALUES (:item_id, :revisor_id, :resultado, :motivo, :observacion, CURRENT_TIMESTAMP)
        ");
        $stmtRev->execute([
            ':item_id' => $itemId,
            ':revisor_id' => AuthHelper::getUsuarioId(),
            ':resultado' => $resultado,
            ':motivo' => $motivo,
            ':observacion' => $observacion
        ]);

        // Update item status
        $reqSub = ($resultado === 'observado') ? 1 : 0;
        $pdo->prepare("UPDATE items_hv SET estado = :estado, requiere_subsanacion = :req WHERE id = :id")
            ->execute([':estado' => $resultado, ':req' => $reqSub, ':id' => $itemId]);

        // Check if all items are verified to update postulation documental status
        $total = (int)$pdo->query("SELECT COUNT(*) FROM items_hv WHERE postulacion_id = $postulacionId")->fetchColumn();
        $aprobados = (int)$pdo->query("SELECT COUNT(*) FROM items_hv WHERE postulacion_id = $postulacionId AND estado = 'aprobado'")->fetchColumn();
        $observados = (int)$pdo->query("SELECT COUNT(*) FROM items_hv WHERE postulacion_id = $postulacionId AND estado = 'observado'")->fetchColumn();
        $rechazados = (int)$pdo->query("SELECT COUNT(*) FROM items_hv WHERE postulacion_id = $postulacionId AND estado = 'rechazado'")->fetchColumn();

        $estadoDoc = 'pendiente';
        if ($aprobados === $total && $total > 0) {
            $estadoDoc = 'conforme';
            $pdo->prepare("UPDATE postulaciones SET resultado_documental = 'conforme', estado = 'habilitada' WHERE id = :id")->execute([':id' => $postulacionId]);
        } elseif ($rechazados > 0) {
            $estadoDoc = 'no_conforme';
            $pdo->prepare("UPDATE postulaciones SET resultado_documental = 'no_conforme' WHERE id = :id")->execute([':id' => $postulacionId]);
        } elseif ($observados > 0) {
            $estadoDoc = 'observado';
            $pdo->prepare("UPDATE postulaciones SET resultado_documental = 'observado' WHERE id = :id")->execute([':id' => $postulacionId]);
        }

        AuditHelper::log('calificar_item_revision', 'items_hv', $itemId, null, ['resultado' => $resultado]);
        AuthHelper::setFlash('success', "Ítem marcado como: " . ucfirst($resultado));
        Response::redirect('c=Revision&a=expediente&id=' . $postulacionId);
    }

    public function solicitarSubsanacion(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano', 'revisor']);

        $postulacionId = (int)$request->post('postulacion_id');
        $diasLimite = (int)$request->post('dias_limite', DEFAULT_SUBSANACION_DIAS);
        $observaciones = trim($request->post('observaciones', ''));

        $fechaLimite = date('Y-m-d 23:59:59', strtotime("+{$diasLimite} weekdays"));

        $pdo = \App\Core\Database::getConnection();

        // Find all observed items
        $stmtObs = $pdo->prepare("SELECT id, titulo FROM items_hv WHERE postulacion_id = :id AND estado = 'observado'");
        $stmtObs->execute([':id' => $postulacionId]);
        $observados = $stmtObs->fetchAll();

        if (empty($observados)) {
            AuthHelper::setFlash('warning', 'No hay ítems con estado Observado para subsanar.');
            Response::redirect('c=Revision&a=expediente&id=' . $postulacionId);
        }

        $stmtSol = $pdo->prepare("
            INSERT INTO solicitudes_subsanacion (postulacion_id, solicitante_id, fecha_limite, estado, observaciones)
            VALUES (:pid, :sid, :flimite, 'pendiente', :obs)
        ");
        $stmtSol->execute([
            ':pid' => $postulacionId,
            ':sid' => AuthHelper::getUsuarioId(),
            ':flimite' => $fechaLimite,
            ':obs' => $observaciones
        ]);
        $solicitudId = (int)$pdo->lastInsertId();

        $stmtDet = $pdo->prepare("
            INSERT INTO detalles_subsanacion (solicitud_id, item_hv_id, motivo_observacion)
            VALUES (:sid, :iid, :motivo)
        ");
        foreach ($observados as $obs) {
            $stmtDet->execute([
                ':sid' => $solicitudId,
                ':iid' => $obs['id'],
                ':motivo' => "Corrección requerida para: {$obs['titulo']}"
            ]);
        }

        // Update postulation state to 'subsanacion_requerida' (RF-048)
        $pdo->prepare("UPDATE postulaciones SET estado = 'subsanacion_requerida', resultado_documental = 'observado' WHERE id = :id")->execute([':id' => $postulacionId]);

        AuditHelper::logMovimiento($postulacionId, 'solicitud_subsanacion', 'en_revision', 'subsanacion_requerida', "Solicitud de subsanación emitida con plazo hasta $fechaLimite.");

        $postulacion = Postulacion::findById($postulacionId);
        NotifyHelper::enviar(
            $postulacion['usuario_id'],
            'Requerimiento de Subsanación Documental',
            "Se ha emitido una solicitud de subsanación para su postulación a {$postulacion['convocatoria_cargo']}. Por favor ingrese al portal y corrija los ítems observados antes del " . date('d/m/Y', strtotime($fechaLimite)) . ".",
            $postulacionId
        );

        AuthHelper::setFlash('success', "Solicitud de subsanación enviada con plazo hasta " . date('d/m/Y', strtotime($fechaLimite)));
        Response::redirect('c=Revision&a=expediente&id=' . $postulacionId);
    }
}
