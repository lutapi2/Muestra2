<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Convocatoria {
    public static function getAll(array $filtros = []): array {
        $pdo = Database::getConnection();
        $sql = "
            SELECT c.*, d.nombre as dependencia_nombre,
                   (SELECT COUNT(*) FROM postulaciones p WHERE p.convocatoria_id = c.id) as total_postulados
            FROM convocatorias c
            LEFT JOIN dependencias d ON c.dependencia_id = d.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filtros['estado'])) {
            $sql .= " AND c.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        if (!empty($filtros['facultad'])) {
            $sql .= " AND c.facultad LIKE :facultad";
            $params[':facultad'] = '%' . $filtros['facultad'] . '%';
        }

        if (!empty($filtros['buscar'])) {
            $sql .= " AND (c.cargo LIKE :buscar OR c.codigo LIKE :buscar)";
            $params[':buscar'] = '%' . $filtros['buscar'] . '%';
        }

        $sql .= " ORDER BY c.fecha_creacion DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function getPublicadas(): array {
        return self::getAll(['estado' => 'publicada']);
    }

    public static function findById(int $id): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT c.*, d.nombre as dependencia_nombre, d.sede as sede_nombre,
                   pc.formacion_minima, pc.experiencia_minima_meses, pc.competencias, pc.funciones, pc.condiciones
            FROM convocatorias c
            LEFT JOIN dependencias d ON c.dependencia_id = d.id
            LEFT JOIN perfil_cargo pc ON c.id = pc.convocatoria_id
            WHERE c.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function crear(array $datos): int {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO convocatorias (codigo, cargo, dependencia_id, facultad, programa, sede, periodo_academico, vacantes, modalidad, tipo_convocatoria, descripcion, fecha_apertura, fecha_cierre, estado, creado_por, fecha_creacion)
            VALUES (:codigo, :cargo, :dependencia_id, :facultad, :programa, :sede, :periodo_academico, :vacantes, :modalidad, :tipo_convocatoria, :descripcion, :fecha_apertura, :fecha_cierre, :estado, :creado_por, CURRENT_TIMESTAMP)
        ");
        $stmt->execute([
            ':codigo' => trim($datos['codigo']),
            ':cargo' => trim($datos['cargo']),
            ':dependencia_id' => (int)$datos['dependencia_id'],
            ':facultad' => trim($datos['facultad']),
            ':programa' => trim($datos['programa'] ?? ''),
            ':sede' => trim($datos['sede'] ?? 'Sincelejo'),
            ':periodo_academico' => trim($datos['periodo_academico'] ?? '2026-2'),
            ':vacantes' => (int)($datos['vacantes'] ?? 1),
            ':modalidad' => trim($datos['modalidad'] ?? 'Tiempo Completo'),
            ':tipo_convocatoria' => trim($datos['tipo_convocatoria'] ?? 'Docente'),
            ':descripcion' => trim($datos['descripcion'] ?? ''),
            ':fecha_apertura' => $datos['fecha_apertura'],
            ':fecha_cierre' => $datos['fecha_cierre'],
            ':estado' => $datos['estado'] ?? 'publicada',
            ':creado_por' => $datos['creado_por'] ?? null
        ]);

        $convocatoriaId = (int)$pdo->lastInsertId();

        // Registrar perfil
        $stmtPerfil = $pdo->prepare("
            INSERT INTO perfil_cargo (convocatoria_id, formacion_minima, experiencia_minima_meses, competencias, funciones, condiciones)
            VALUES (:convocatoria_id, :formacion_minima, :experiencia_minima_meses, :competencias, :funciones, :condiciones)
        ");
        $stmtPerfil->execute([
            ':convocatoria_id' => $convocatoriaId,
            ':formacion_minima' => trim($datos['formacion_minima'] ?? 'Título universitario afín al área'),
            ':experiencia_minima_meses' => (int)($datos['experiencia_minima_meses'] ?? 12),
            ':competencias' => trim($datos['competencias'] ?? ''),
            ':funciones' => trim($datos['funciones'] ?? ''),
            ':condiciones' => trim($datos['condiciones'] ?? '')
        ]);

        return $convocatoriaId;
    }

    public static function cambiarEstado(int $id, string $nuevoEstado, ?string $motivo = null): void {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            UPDATE convocatorias 
            SET estado = :estado, motivo_retiro = :motivo
            WHERE id = :id
        ");
        $stmt->execute([
            ':id' => $id,
            ':estado' => $nuevoEstado,
            ':motivo' => $motivo
        ]);
    }
}
