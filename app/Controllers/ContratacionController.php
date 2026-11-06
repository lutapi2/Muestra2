<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Helpers\AuthHelper;
use App\Helpers\AuditHelper;
use App\Helpers\NotifyHelper;
use App\Models\Contratacion;
use App\Models\Convocatoria;
use App\Models\Postulacion;
use App\Models\ItemHV;

class ContratacionController {
    public function index(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano', 'revisor']);

        $filtros = [
            'proceso' => $request->get('proceso', ''),
            'unidad' => $request->get('unidad', ''),
            'estado_proceso' => $request->get('estado_proceso', ''),
            'estado_carga' => $request->get('estado_carga', ''),
            'estado_contratacion' => $request->get('estado_contratacion', ''),
            'buscar_usuario' => $request->get('buscar_usuario', ''),
            'autorizados' => $request->get('autorizados', '')
        ];

        $kpis = Contratacion::getKPIs();
        $seleccionados = Contratacion::listarSeleccionados($filtros);
        $convocatorias = Convocatoria::getAll();

        View::render('contratacion.index', [
            'kpis' => $kpis,
            'seleccionados' => $seleccionados,
            'convocatorias' => $convocatorias,
            'filtros' => $filtros
        ], 'main');
    }

    public function expediente(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano', 'revisor']);

        $postulacionId = (int)$request->get('id', 1);
        $expediente = Contratacion::getExpedienteContratacion($postulacionId);

        if (!$expediente['postulacion']) {
            AuthHelper::setFlash('danger', 'Postulación no encontrada.');
            Response::redirect('c=Contratacion&a=index');
        }

        View::render('contratacion.expediente', [
            'postulacion' => $expediente['postulacion'],
            'categorias' => $expediente['categorias'],
            'contratacion' => $expediente['contratacion'],
            'examen' => $expediente['examen'],
            'vinculacion' => $expediente['vinculacion'],
            'kpis' => Contratacion::getKPIs()
        ], 'main');
    }

    public function cambiarFormalizacion(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano']);

        $postulacionId = (int)$request->post('postulacion_id');
        $estado = $request->post('estado_formalizacion', 'firmado');
        $obs = trim($request->post('observaciones', ''));

        Contratacion::cambiarEstadoFormalizacion($postulacionId, $estado, $obs);
        AuditHelper::log('cambio_formalizacion', 'contrataciones', $postulacionId, null, ['estado' => $estado]);
        AuditHelper::logMovimiento($postulacionId, 'formalizacion', null, $estado, "Estado de formalización cambiado a $estado: $obs");

        AuthHelper::setFlash('success', "Estado de formalización actualizado a: " . ucfirst($estado));
        Response::redirect('c=Contratacion&a=expediente&id=' . $postulacionId);
    }

    public function vincular(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano']);

        $postulacionId = (int)$request->post('postulacion_id');
        $fechaInicio = $request->post('fecha_inicio_labores', date('Y-m-d'));

        try {
            $codigo = Contratacion::vincularColaborador($postulacionId, $fechaInicio, AuthHelper::getUsuarioId());
            AuditHelper::log('vinculacion_colaborador', 'vinculaciones', $postulacionId, null, ['codigo' => $codigo]);
            AuditHelper::logMovimiento($postulacionId, 'vinculacion', 'contratado', 'vinculado', "Colaborador vinculado formalmente con código $codigo y transferido a HV institucional.");

            $postulacion = Postulacion::findById($postulacionId);
            NotifyHelper::enviar(
                $postulacion['usuario_id'],
                '¡Vinculación Laboral Confirmada!',
                "Felicitaciones. Su vinculación ha sido autorizada formalmente bajo el código institucional {$codigo}. Sus datos han sido integrados con la hoja de vida de CECAR.",
                $postulacionId
            );

            AuthHelper::setFlash('success', "¡Colaborador vinculado exitosamente! Código generado: {$codigo}. Sincronización con Hoja de Vida institucional completada.");
        } catch (\Exception $e) {
            AuthHelper::setFlash('danger', 'Error al procesar vinculación: ' . $e->getMessage());
        }

        Response::redirect('c=Contratacion&a=expediente&id=' . $postulacionId);
    }

    public function exportarExcel(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano', 'revisor', 'auditor']);

        $seleccionados = Contratacion::listarSeleccionados();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Contratacion_Seleccionados_CECAR_' . date('Ymd_His') . '.csv');

        $output = fopen('php://output', 'w');
        // UTF-8 BOM for Excel
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, ['ID Postulación', 'Código Radicado', 'Aspirante / Colaborador', 'Documento', 'Correo', 'Convocatoria', 'Facultad', 'Cargados', 'Aprobados', 'Pendientes', 'Rechazados', 'Estado Carga', 'Estado Contratación']);

        foreach ($seleccionados as $row) {
            fputcsv($output, [
                $row['postulacion_id'],
                $row['codigo_radicado'],
                $row['nombres'] . ' ' . $row['apellidos'],
                $row['numero_documento'],
                $row['correo'],
                $row['cargo'],
                $row['facultad'],
                $row['total_cargados'],
                $row['total_aprobados'],
                $row['total_pendientes'],
                $row['total_rechazados'],
                ucfirst($row['estado_carga_documentos'] ?? 'completa'),
                ucfirst($row['estado_postulacion'])
            ]);
        }

        fclose($output);
        exit;
    }
}
