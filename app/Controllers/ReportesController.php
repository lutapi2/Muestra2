<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Helpers\AuthHelper;
use App\Models\Convocatoria;

class ReportesController {
    public function index(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano', 'revisor', 'auditor']);

        $tipoReporte = $request->get('tipo', 'no_aprobados'); // no_aprobados, en_proceso, seleccionados, kpis
        $convocatoriaId = $request->get('convocatoria_id', '');
        $facultad = $request->get('facultad', '');
        $periodo = $request->get('periodo', '');

        $pdo = \App\Core\Database::getConnection();

        $params = [];
        $where = " WHERE 1=1 ";

        if (!empty($convocatoriaId)) {
            $where .= " AND c.id = :cid ";
            $params[':cid'] = $convocatoriaId;
        }
        if (!empty($facultad)) {
            $where .= " AND c.facultad LIKE :fac ";
            $params[':fac'] = '%' . $facultad . '%';
        }
        if (!empty($periodo)) {
            $where .= " AND c.periodo_academico = :per ";
            $params[':per'] = $periodo;
        }

        $datosReporte = [];

        if ($tipoReporte === 'no_aprobados') {
            // Reporte 1: No aprobados con etapa y causal (RF-096)
            $sql = "
                SELECT p.codigo_radicado, p.fecha_postulacion, p.etapa_actual, p.motivo_descarte,
                       u.nombres, u.apellidos, u.numero_documento, u.correo,
                       c.cargo, c.codigo as convocatoria_codigo, c.facultad, c.periodo_academico,
                       cat.nombre as causal_descarte_nombre, cat.codigo as causal_codigo
                FROM postulaciones p
                JOIN usuarios u ON p.usuario_id = u.id
                JOIN convocatorias c ON p.convocatoria_id = c.id
                LEFT JOIN catalogos cat ON p.causal_descarte_id = cat.codigo
                {$where} AND p.estado IN ('descartada', 'no_habilitada', 'no_seleccionada')
                ORDER BY p.id DESC
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $datosReporte = $stmt->fetchAll();
        } elseif ($tipoReporte === 'en_proceso') {
            // Reporte 2: En proceso con etapa actual (RF-096)
            $sql = "
                SELECT p.codigo_radicado, p.fecha_postulacion, p.etapa_actual, p.estado, p.porcentaje_avance, p.resultado_documental,
                       u.nombres, u.apellidos, u.numero_documento, u.correo,
                       c.cargo, c.codigo as convocatoria_codigo, c.facultad, c.periodo_academico
                FROM postulaciones p
                JOIN usuarios u ON p.usuario_id = u.id
                JOIN convocatorias c ON p.convocatoria_id = c.id
                {$where} AND p.estado IN ('enviada', 'en_revision', 'subsanacion_requerida', 'habilitada', 'preseleccionada', 'en_formalizacion')
                ORDER BY p.id DESC
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $datosReporte = $stmt->fetchAll();
        } elseif ($tipoReporte === 'seleccionados') {
            // Reporte 3: Seleccionados con estado de formalización (RF-096)
            $sql = "
                SELECT p.codigo_radicado, p.fecha_postulacion, p.estado as estado_postulacion,
                       u.nombres, u.apellidos, u.numero_documento, u.correo,
                       c.cargo, c.codigo as convocatoria_codigo, c.facultad, c.periodo_academico,
                       cnt.estado_formalizacion, cnt.estado_carga_documentos, cnt.salario_asignado, cnt.fecha_inicio_labores,
                       vnc.codigo_colaborador, vnc.fecha_vinculacion
                FROM postulaciones p
                JOIN usuarios u ON p.usuario_id = u.id
                JOIN convocatorias c ON p.convocatoria_id = c.id
                LEFT JOIN contrataciones cnt ON cnt.postulacion_id = p.id
                LEFT JOIN vinculaciones vnc ON vnc.postulacion_id = p.id
                {$where} AND p.estado IN ('seleccionada', 'contratado', 'vinculado', 'en_formalizacion')
                ORDER BY p.id DESC
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $datosReporte = $stmt->fetchAll();
        }

        $convocatorias = Convocatoria::getAll();

        View::render('reportes.index', [
            'tipoReporte' => $tipoReporte,
            'datosReporte' => $datosReporte,
            'convocatorias' => $convocatorias,
            'filtros' => [
                'convocatoria_id' => $convocatoriaId,
                'facultad' => $facultad,
                'periodo' => $periodo
            ]
        ], 'main');
    }

    public function exportarExcel(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano', 'revisor', 'auditor']);

        $tipo = $request->get('tipo', 'general');
        $filename = "Reporte_Institucional_{$tipo}_CECAR_" . date('Ymd_His') . ".csv";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $out = fopen('php://output', 'w');
        fputs($out, "\xEF\xBB\xBF"); // BOM for Excel

        $pdo = \App\Core\Database::getConnection();

        if ($tipo === 'no_aprobados') {
            fputcsv($out, ['Radicado', 'Aspirante', 'Documento', 'Correo', 'Cargo', 'Facultad', 'Etapa', 'Causal de Descarte', 'Motivo Detallado']);
            $sql = "
                SELECT p.codigo_radicado, (u.nombres || ' ' || u.apellidos) as aspirante, u.numero_documento, u.correo,
                       c.cargo, c.facultad, p.etapa_actual, cat.nombre as causal, p.motivo_descarte
                FROM postulaciones p
                JOIN usuarios u ON p.usuario_id = u.id
                JOIN convocatorias c ON p.convocatoria_id = c.id
                LEFT JOIN catalogos cat ON p.causal_descarte_id = cat.codigo
                WHERE p.estado IN ('descartada', 'no_habilitada', 'no_seleccionada')
            ";
            foreach ($pdo->query($sql) as $row) {
                fputcsv($out, $row);
            }
        } elseif ($tipo === 'seleccionados') {
            fputcsv($out, ['Radicado', 'Colaborador', 'Documento', 'Cargo', 'Facultad', 'Estado Postulación', 'Formalización', 'Carga Documentos', 'Código Institucional']);
            $sql = "
                SELECT p.codigo_radicado, (u.nombres || ' ' || u.apellidos) as colaborador, u.numero_documento,
                       c.cargo, c.facultad, p.estado, cnt.estado_formalizacion, cnt.estado_carga_documentos, vnc.codigo_colaborador
                FROM postulaciones p
                JOIN usuarios u ON p.usuario_id = u.id
                JOIN convocatorias c ON p.convocatoria_id = c.id
                LEFT JOIN contrataciones cnt ON cnt.postulacion_id = p.id
                LEFT JOIN vinculaciones vnc ON vnc.postulacion_id = p.id
                WHERE p.estado IN ('seleccionada', 'contratado', 'vinculado', 'en_formalizacion')
            ";
            foreach ($pdo->query($sql) as $row) {
                fputcsv($out, $row);
            }
        } else {
            fputcsv($out, ['Radicado', 'Aspirante', 'Documento', 'Cargo', 'Facultad', 'Estado', 'Etapa', 'Avance %', 'Fecha']);
            $sql = "
                SELECT p.codigo_radicado, (u.nombres || ' ' || u.apellidos) as aspirante, u.numero_documento,
                       c.cargo, c.facultad, p.estado, p.etapa_actual, p.porcentaje_avance, p.fecha_postulacion
                FROM postulaciones p
                JOIN usuarios u ON p.usuario_id = u.id
                JOIN convocatorias c ON p.convocatoria_id = c.id
            ";
            foreach ($pdo->query($sql) as $row) {
                fputcsv($out, $row);
            }
        }

        fclose($out);
        exit;
    }
}
