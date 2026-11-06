<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Helpers\AuthHelper;
use App\Helpers\AuditHelper;
use App\Models\TicketAyuda;

class MesaAyudaController {
    public function index(Request $request): void {
        AuthHelper::exigirAutenticacion();

        $usuarioId = AuthHelper::esCandidato() ? AuthHelper::getUsuarioId() : null;
        $tickets = TicketAyuda::listar($usuarioId);

        View::render('mesa_ayuda.index', [
            'tickets' => $tickets,
            'esGestor' => AuthHelper::esTalentoHumano()
        ], 'main');
    }

    public function crear(Request $request): void {
        AuthHelper::exigirAutenticacion();

        $categoria = $request->post('categoria', 'soporte_postulacion');
        $asunto = trim($request->post('asunto', ''));
        $mensaje = trim($request->post('mensaje', ''));

        if (empty($asunto) || empty($mensaje)) {
            AuthHelper::setFlash('danger', 'Debe diligenciar el asunto y la descripción de su consulta.');
            Response::redirect('c=MesaAyuda&a=index');
        }

        $ticketId = TicketAyuda::crear(AuthHelper::getUsuarioId(), $categoria, $asunto, $mensaje);
        AuditHelper::log('crear_ticket_ayuda', 'tickets_ayuda', $ticketId);

        AuthHelper::setFlash('success', 'Su solicitud ha sido registrada con el ticket #' . $ticketId . '. El equipo de Talento Humano le responderá a la brevedad.');
        Response::redirect('c=MesaAyuda&a=index');
    }

    public function responder(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano']);

        $ticketId = (int)$request->post('ticket_id');
        $respuesta = trim($request->post('respuesta', ''));

        if (empty($respuesta)) {
            AuthHelper::setFlash('danger', 'La respuesta no puede estar vacía.');
            Response::redirect('c=MesaAyuda&a=index');
        }

        TicketAyuda::responder($ticketId, $respuesta, AuthHelper::getUsuarioId());
        AuditHelper::log('responder_ticket_ayuda', 'tickets_ayuda', $ticketId);

        AuthHelper::setFlash('success', 'Respuesta emitida y notificada al usuario.');
        Response::redirect('c=MesaAyuda&a=index');
    }
}
