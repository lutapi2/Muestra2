<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\View;
use App\Helpers\AuthHelper;
use App\Models\Postulacion;
use App\Models\Convocatoria;

class DashboardController {
    public function index(Request $request): void {
        AuthHelper::exigirAutenticacion();

        $stats = Postulacion::getStatsGlobales();
        $convocatoriasRecientes = Convocatoria::getAll();
        $esCandidato = AuthHelper::esCandidato();

        $misPostulaciones = [];
        if ($esCandidato) {
            $pdo = \App\Core\Database::getConnection();
            $stmt = $pdo->prepare("
                SELECT p.*, c.cargo, c.codigo as convocatoria_codigo, c.facultad, c.fecha_cierre
                FROM postulaciones p
                JOIN convocatorias c ON p.convocatoria_id = c.id
                WHERE p.usuario_id = :uid
                ORDER BY p.fecha_postulacion DESC
            ");
            $stmt->execute([':uid' => AuthHelper::getUsuarioId()]);
            $misPostulaciones = $stmt->fetchAll();
        }

        View::render('dashboard.index', [
            'stats' => $stats,
            'convocatoriasRecientes' => $convocatoriasRecientes,
            'misPostulaciones' => $misPostulaciones,
            'esCandidato' => $esCandidato
        ], 'main');
    }
}
