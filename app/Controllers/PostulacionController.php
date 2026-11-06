<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Helpers\AuthHelper;
use App\Helpers\AuditHelper;
use App\Helpers\FileHelper;
use App\Helpers\NotifyHelper;
use App\Models\Postulacion;
use App\Models\Convocatoria;
use App\Models\ItemHV;

class PostulacionController {
    public function index(Request $request): void {
        AuthHelper::exigirAutenticacion();
        $convocatoriaId = (int)$request->get('convocatoria_id');

        if (!$convocatoriaId) {
            Response::redirect('c=Convocatoria&a=index');
        }

        $convocatoria = Convocatoria::findById($convocatoriaId);
        $usuarioId = AuthHelper::getUsuarioId();

        $postulacion = Postulacion::findByUsuarioYConvocatoria($usuarioId, $convocatoriaId);

        if (!$postulacion) {
            $postulacionId = Postulacion::iniciar($usuarioId, $convocatoriaId);
            $postulacion = Postulacion::findById($postulacionId);
            AuditHelper::log('iniciar_postulacion', 'postulaciones', $postulacionId);
            AuditHelper::logMovimiento($postulacionId, 'inicio', null, 'borrador', 'Postulación creada en estado borrador.');
        }

        $items = ItemHV::getOrganizadosPorCategoria($postulacion['id']);
        $postulacionCompleta = Postulacion::findById($postulacion['id']);

        View::render('postulacion.form', [
            'convocatoria' => $convocatoria,
            'postulacion' => $postulacionCompleta,
            'itemsPorCategoria' => $items,
            'esEditable' => in_array($postulacionCompleta['estado'], ['borrador', 'subsanacion_requerida'])
        ], 'main');
    }

    public function agregarItem(Request $request): void {
        AuthHelper::exigirAutenticacion();

        $postulacionId = (int)$request->post('postulacion_id');
        $categoria = $request->post('categoria');
        $tipoItem = $request->post('tipo_item');
        $titulo = trim($request->post('titulo'));
        $datosExtra = $request->post('datos', []);

        $archivoSubido = null;
        if (!empty($_FILES['archivo']) && $_FILES['archivo']['error'] !== UPLOAD_ERR_NO_FILE) {
            try {
                $archivoSubido = FileHelper::guardarArchivo($_FILES['archivo']);
            } catch (\Exception $e) {
                AuthHelper::setFlash('danger', 'Error al cargar soporte: ' . $e->getMessage());
                Response::redirect('c=Postulacion&a=index&convocatoria_id=' . $request->post('convocatoria_id'));
            }
        }

        try {
            ItemHV::registrar([
                'postulacion_id' => $postulacionId,
                'categoria' => $categoria,
                'tipo_item' => $tipoItem,
                'titulo' => $titulo,
                'datos_json' => $datosExtra,
                'version' => 1,
                'estado' => 'borrador'
            ], $archivoSubido);

            Postulacion::calcularProgreso($postulacionId);
            AuditHelper::log('agregar_item_hv', 'items_hv', $postulacionId, null, ['titulo' => $titulo, 'categoria' => $categoria]);

            AuthHelper::setFlash('success', "Ítem y soporte guardados correctamente en la categoría '{$categoria}'.");
        } catch (\Exception $e) {
            AuthHelper::setFlash('danger', 'Error al registrar ítem: ' . $e->getMessage());
        }

        Response::redirect('c=Postulacion&a=index&convocatoria_id=' . $request->post('convocatoria_id'));
    }

    public function enviarDefinitivo(Request $request): void {
        AuthHelper::exigirAutenticacion();

        $postulacionId = (int)$request->post('postulacion_id');
        $postulacion = Postulacion::findById($postulacionId);

        if (!$postulacion || $postulacion['usuario_id'] != AuthHelper::getUsuarioId()) {
            AuthHelper::setFlash('danger', 'Acción no autorizada.');
            Response::redirect('c=Dashboard&a=index');
        }

        $declaracionVeracidad = (bool)$request->post('declaracion_veracidad');
        $bancoTalento = (bool)$request->post('banco_talento_consentimiento');

        if (!$declaracionVeracidad) {
            AuthHelper::setFlash('danger', 'Debe aceptar la declaración bajo gravedad de juramento sobre la veracidad de la información (HV-022, RN-008).');
            Response::redirect('c=Postulacion&a=index&convocatoria_id=' . $postulacion['convocatoria_id']);
        }

        Postulacion::enviar($postulacionId, $declaracionVeracidad, $bancoTalento);

        // Update all items to 'enviado'
        $pdo = \App\Core\Database::getConnection();
        $pdo->prepare("UPDATE items_hv SET estado = 'enviado' WHERE postulacion_id = :id AND estado = 'borrador'")
            ->execute([':id' => $postulacionId]);

        AuditHelper::log('enviar_postulacion', 'postulaciones', $postulacionId);
        AuditHelper::logMovimiento($postulacionId, 'envio_definitivo', 'borrador', 'enviada', 'Postulación confirmada y radicada oficialmente.');

        NotifyHelper::enviar(
            AuthHelper::getUsuarioId(),
            'Confirmación de Radicación de Postulación',
            "Su postulación para la convocatoria '{$postulacion['convocatoria_cargo']}' fue radicada formalmente bajo el radicado {$postulacion['codigo_radicado']}.",
            $postulacionId
        );

        AuthHelper::setFlash('success', "¡Postulación radicada con éxito! Su código de radicado es {$postulacion['codigo_radicado']}.");
        Response::redirect('c=Postulacion&a=constancia&id=' . $postulacionId);
    }

    public function constancia(Request $request): void {
        AuthHelper::exigirAutenticacion();

        $postulacionId = (int)$request->get('id');
        $postulacion = Postulacion::findById($postulacionId);

        if (!$postulacion) {
            AuthHelper::setFlash('danger', 'Postulación no encontrada.');
            Response::redirect('c=Dashboard&a=index');
        }

        $items = ItemHV::getByPostulacion($postulacionId);

        View::render('postulacion.constancia', [
            'postulacion' => $postulacion,
            'items' => $items
        ], 'main');
    }

    public function subsanar(Request $request): void {
        AuthHelper::exigirAutenticacion();

        $postulacionId = (int)$request->get('id');
        $postulacion = Postulacion::findById($postulacionId);

        $pdo = \App\Core\Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT s.*, u.nombres as solicitante_nombres, u.apellidos as solicitante_apellidos
            FROM solicitudes_subsanacion s
            JOIN usuarios u ON s.solicitante_id = u.id
            WHERE s.postulacion_id = :pid AND s.estado = 'pendiente'
            ORDER BY s.fecha_solicitud DESC LIMIT 1
        ");
        $stmt->execute([':pid' => $postulacionId]);
        $solicitud = $stmt->fetch();

        // Get only observed items (RN-011)
        $stmtItems = $pdo->prepare("
            SELECT i.*, d.motivo_observacion, s.nombre_original as soporte_nombre
            FROM items_hv i
            JOIN detalles_subsanacion d ON d.item_hv_id = i.id
            LEFT JOIN soportes s ON s.item_hv_id = i.id
            WHERE i.postulacion_id = :pid AND d.subsanado = 0
        ");
        $stmtItems->execute([':pid' => $postulacionId]);
        $itemsObservados = $stmtItems->fetchAll();

        View::render('postulacion.subsanar', [
            'postulacion' => $postulacion,
            'solicitud' => $solicitud,
            'itemsObservados' => $itemsObservados
        ], 'main');
    }

    public function guardarSubsanacion(Request $request): void {
        AuthHelper::exigirAutenticacion();

        $itemId = (int)$request->post('item_id');
        $postulacionId = (int)$request->post('postulacion_id');
        $item = ItemHV::findById($itemId);

        $archivoSubido = null;
        if (!empty($_FILES['nuevo_soporte']) && $_FILES['nuevo_soporte']['error'] !== UPLOAD_ERR_NO_FILE) {
            try {
                $archivoSubido = FileHelper::guardarArchivo($_FILES['nuevo_soporte']);
            } catch (\Exception $e) {
                AuthHelper::setFlash('danger', 'Error al cargar soporte de subsanación: ' . $e->getMessage());
                Response::redirect('c=Postulacion&a=subsanar&id=' . $postulacionId);
            }
        }

        ItemHV::actualizar($itemId, [
            'titulo' => $request->post('titulo', $item['titulo']),
            'datos_json' => $item['datos_json'],
            'estado' => 'enviado'
        ], $archivoSubido);

        // Mark detalle subsanacion as subsanado
        $pdo = \App\Core\Database::getConnection();
        $pdo->prepare("UPDATE detalles_subsanacion SET subsanado = 1 WHERE item_hv_id = :id")->execute([':id' => $itemId]);

        // If no more items to subsanar, update postulacion back to 'en_revision'
        $pendientes = (int)$pdo->query("SELECT COUNT(*) FROM detalles_subsanacion d JOIN items_hv i ON d.item_hv_id = i.id WHERE i.postulacion_id = $postulacionId AND d.subsanado = 0")->fetchColumn();
        if ($pendientes === 0) {
            $pdo->prepare("UPDATE postulaciones SET estado = 'en_revision' WHERE id = :id")->execute([':id' => $postulacionId]);
            $pdo->prepare("UPDATE solicitudes_subsanacion SET estado = 'respondida' WHERE postulacion_id = :id AND estado = 'pendiente'")->execute([':id' => $postulacionId]);
            AuditHelper::logMovimiento($postulacionId, 'subsanacion_enviada', 'subsanacion_requerida', 'en_revision', 'Subsanación documental completada y remitida al revisor.');
        }

        AuthHelper::setFlash('success', 'Ítem corregido y remitido a verificación.');
        Response::redirect('c=Postulacion&a=subsanar&id=' . $postulacionId);
    }
}
