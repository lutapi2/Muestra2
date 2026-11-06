<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class PlanTrabajo {
    public static function listarPorConvocatoria(?int $convocatoriaId = null): array {
        $pdo = Database::getConnection();
        $sql = "
            SELECT p.*, c.cargo, c.codigo as convocatoria_codigo, u.nombres, u.apellidos
            FROM planes_trabajo p
            JOIN convocatorias c ON p.convocatoria_id = c.id
            JOIN usuarios u ON p.responsable_id = u.id
        ";
        $params = [];
        if ($convocatoriaId !== null) {
            $sql .= " WHERE p.convocatoria_id = :cid";
            $params[':cid'] = $convocatoriaId;
        }
        $sql .= " ORDER BY p.fecha_inicio DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function getActividades(int $planId): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT a.*, u.nombres, u.apellidos
            FROM actividades_plan a
            JOIN usuarios u ON a.responsable_id = u.id
            WHERE a.plan_id = :plan_id
            ORDER BY a.fecha_limite ASC
        ");
        $stmt->execute([':plan_id' => $planId]);
        return $stmt->fetchAll();
    }
}
