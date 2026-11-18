<?php

class DashboardController {
    public function index(Request $request): void {
        AuthHelper::exigirAutenticacion();

        $stats = Postulacion::getStatsGlobales();
        $convocatoriasRecientes = Convocatoria::getAll();
        $esCandidato = AuthHelper::esCandidato();

        $misPostulaciones = [];
        $ultimasPostulaciones = [];

        $pdo = Database::getConnection();

        if ($esCandidato) {
            $stmt = $pdo->prepare("
                SELECT p.*, c.cargo, c.codigo as convocatoria_codigo, c.facultad, c.fecha_cierre
                FROM postulaciones p
                JOIN convocatorias c ON p.convocatoria_id = c.id
                WHERE p.usuario_id = :uid
                ORDER BY p.fecha_postulacion DESC
            ");
            $stmt->execute([':uid' => AuthHelper::getUsuarioId()]);
            $misPostulaciones = $stmt->fetchAll();
        } else {
            // Para administradores y talento humano: reflejar las últimas postulaciones recibidas
            $stmtUlt = $pdo->prepare("
                SELECT p.*, c.cargo as convocatoria_cargo, c.codigo as convocatoria_codigo, c.facultad,
                       u.nombres, u.apellidos, u.numero_documento, u.correo
                FROM postulaciones p
                JOIN convocatorias c ON p.convocatoria_id = c.id
                JOIN usuarios u ON p.usuario_id = u.id
                ORDER BY p.fecha_postulacion DESC
                LIMIT 15
            ");
            $stmtUlt->execute();
            $ultimasPostulaciones = $stmtUlt->fetchAll();
        }

        View::render('dashboard.index', [
            'stats' => $stats,
            'convocatoriasRecientes' => $convocatoriasRecientes,
            'misPostulaciones' => $misPostulaciones,
            'ultimasPostulaciones' => $ultimasPostulaciones,
            'esCandidato' => $esCandidato
        ], 'main');
    }
}
