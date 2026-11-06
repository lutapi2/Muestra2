<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Entrevista {
    public static function listar(array $filtros = []): array {
        $pdo = Database::getConnection();
        $sql = "
            SELECT e.*, 
                   p.codigo_radicado, p.usuario_id,
                   u.nombres, u.apellidos, u.numero_documento, u.correo,
                   c.cargo, c.codigo as convocatoria_codigo, c.facultad
            FROM entrevistas e
            JOIN postulaciones p ON e.postulacion_id = p.id
            JOIN usuarios u ON p.usuario_id = u.id
            JOIN convocatorias c ON p.convocatoria_id = c.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filtros['convocatoria_id'])) {
            $sql .= " AND c.id = :convocatoria_id";
            $params[':convocatoria_id'] = $filtros['convocatoria_id'];
        }

        if (!empty($filtros['estado'])) {
            $sql .= " AND e.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        $sql .= " ORDER BY e.fecha DESC, e.hora_inicio ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function programar(array $datos): int {
        $pdo = Database::getConnection();

        // Control de conflicto de agenda (RN-017, RF-064)
        $conflictoStmt = $pdo->prepare("
            SELECT COUNT(*) FROM entrevistas 
            WHERE fecha = :fecha 
              AND estado IN ('programada', 'confirmada')
              AND ((hora_inicio < :fin AND hora_fin > :inicio))
        ");
        $conflictoStmt->execute([
            ':fecha' => $datos['fecha'],
            ':inicio' => $datos['hora_inicio'],
            ':fin' => $datos['hora_fin']
        ]);
        if ((int)$conflictoStmt->fetchColumn() > 0) {
            throw new \Exception('Conflicto de agenda: Ya existe una entrevista programada en esa misma fecha y rango de horario.');
        }

        $stmt = $pdo->prepare("
            INSERT INTO entrevistas (postulacion_id, fecha, hora_inicio, hora_fin, modalidad, ubicacion, enlace, estado)
            VALUES (:postulacion_id, :fecha, :hora_inicio, :hora_fin, :modalidad, :ubicacion, :enlace, 'programada')
        ");
        $stmt->execute([
            ':postulacion_id' => (int)$datos['postulacion_id'],
            ':fecha' => $datos['fecha'],
            ':hora_inicio' => $datos['hora_inicio'],
            ':hora_fin' => $datos['hora_fin'],
            ':modalidad' => $datos['modalidad'] ?? 'virtual',
            ':ubicacion' => $datos['ubicacion'] ?? 'Campus CECAR',
            ':enlace' => $datos['enlace'] ?? ''
        ]);

        $entrevistaId = (int)$pdo->lastInsertId();

        // Asignar entrevistadores
        if (!empty($datos['entrevistadores']) && is_array($datos['entrevistadores'])) {
            $entStmt = $pdo->prepare("
                INSERT INTO entrevistadores (entrevista_id, usuario_id, rol_comite)
                VALUES (:entrevista_id, :usuario_id, :rol_comite)
            ");
            foreach ($datos['entrevistadores'] as $entrevistadorId) {
                $entStmt->execute([
                    ':entrevista_id' => $entrevistaId,
                    ':usuario_id' => (int)$entrevistadorId,
                    ':rol_comite' => 'Miembro Comité'
                ]);
            }
        }

        // Actualizar postulacion a 'preseleccionada' / etapa entrevista
        $pdo->prepare("UPDATE postulaciones SET etapa_actual = 'entrevista', estado = 'preseleccionada' WHERE id = :id")
            ->execute([':id' => (int)$datos['postulacion_id']]);

        return $entrevistaId;
    }

    public static function registrarEvaluacion(int $entrevistaId, int $evaluadorId, float $puntaje, string $concepto, string $observacion): void {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO resultados_entrevista (entrevista_id, evaluador_id, puntaje, concepto, observacion, tipo, fecha)
            VALUES (:entrevista_id, :evaluador_id, :puntaje, :concepto, :observacion, 'individual', CURRENT_TIMESTAMP)
        ");
        $stmt->execute([
            ':entrevista_id' => $entrevistaId,
            ':evaluador_id' => $evaluadorId,
            ':puntaje' => $puntaje,
            ':concepto' => $concepto,
            ':observacion' => $observacion
        ]);

        $pdo->prepare("UPDATE entrevistas SET estado = 'realizada' WHERE id = :id")->execute([':id' => $entrevistaId]);
    }
}
