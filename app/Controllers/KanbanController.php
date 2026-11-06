<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\View;
use App\Helpers\AuthHelper;
use App\Models\Convocatoria;

class KanbanController {
    public function index(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano', 'revisor', 'entrevistador']);

        $convocatoriaId = (int)$request->get('convocatoria_id', 1);
        $convocatorias = Convocatoria::getAll();

        $pdo = \App\Core\Database::getConnection();

        $stmt = $pdo->prepare("
            SELECT p.*, u.nombres, u.apellidos, u.numero_documento, u.correo, c.cargo, c.facultad
            FROM postulaciones p
            JOIN usuarios u ON p.usuario_id = u.id
            JOIN convocatorias c ON p.convocatoria_id = c.id
            WHERE p.convocatoria_id = :cid
            ORDER BY p.fecha_postulacion DESC
        ");
        $stmt->execute([':cid' => $convocatoriaId]);
        $postulaciones = $stmt->fetchAll();

        // Organize into Kanban columns
        $columnas = [
            'postulados' => ['titulo' => 'Postulados / Enviados', 'badge' => '#3B82F6', 'items' => []],
            'revision' => ['titulo' => 'Revisión Documental', 'badge' => '#F59E0B', 'items' => []],
            'evaluacion' => ['titulo' => 'Pruebas y Entrevistas', 'badge' => '#8B5CF6', 'items' => []],
            'seleccionados' => ['titulo' => 'Seleccionados', 'badge' => '#10B981', 'items' => []],
            'contratados' => ['titulo' => 'Contratación y Vinculación', 'badge' => '#047857', 'items' => []],
            'descartados' => ['titulo' => 'No Continúa / Descartados', 'badge' => '#EF4444', 'items' => []]
        ];

        foreach ($postulaciones as $p) {
            $est = $p['estado'];
            if (in_array($est, ['borrador', 'enviada'])) {
                $columnas['postulados']['items'][] = $p;
            } elseif (in_array($est, ['en_revision', 'subsanacion_requerida', 'habilitada'])) {
                $columnas['revision']['items'][] = $p;
            } elseif (in_array($est, ['preseleccionada'])) {
                $columnas['evaluacion']['items'][] = $p;
            } elseif ($est === 'seleccionada') {
                $columnas['seleccionados']['items'][] = $p;
            } elseif (in_array($est, ['en_formalizacion', 'contratado', 'vinculado'])) {
                $columnas['contratados']['items'][] = $p;
            } else {
                $columnas['descartados']['items'][] = $p;
            }
        }

        View::render('kanban.index', [
            'columnas' => $columnas,
            'convocatorias' => $convocatorias,
            'convocatoriaId' => $convocatoriaId
        ], 'main');
    }

    public function lineaTiempo(Request $request): void {
        AuthHelper::exigirRol(['admin', 'talento_humano', 'revisor', 'entrevistador']);

        $postulacionId = (int)$request->get('id');
        $pdo = \App\Core\Database::getConnection();

        $stmtPost = $pdo->prepare("
            SELECT p.*, u.nombres, u.apellidos, u.numero_documento, c.cargo, c.facultad
            FROM postulaciones p
            JOIN usuarios u ON p.usuario_id = u.id
            JOIN convocatorias c ON p.convocatoria_id = c.id
            WHERE p.id = :id
        ");
        $stmtPost->execute([':id' => $postulacionId]);
        $postulacion = $stmtPost->fetch();

        $stmtMov = $pdo->prepare("
            SELECT m.*, u.nombres, u.apellidos, u.cargo_interno
            FROM movimientos_historial m
            LEFT JOIN usuarios u ON m.usuario_id = u.id
            WHERE m.postulacion_id = :id
            ORDER BY m.fecha_hora DESC
        ");
        $stmtMov->execute([':id' => $postulacionId]);
        $movimientos = $stmtMov->fetchAll();

        View::render('kanban.linea_tiempo', [
            'postulacion' => $postulacion,
            'movimientos' => $movimientos
        ], 'main');
    }
}
