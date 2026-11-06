<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Postulacion {
    public static function findById(int $id): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT p.*, 
                   c.codigo as convocatoria_codigo, c.cargo as convocatoria_cargo, c.facultad, c.programa, c.sede, c.periodo_academico, c.modalidad, c.fecha_cierre,
                   u.nombres, u.apellidos, u.correo, u.telefono, u.numero_documento, u.tipo_documento, u.ciudad, u.direccion
            FROM postulaciones p
            JOIN convocatorias c ON p.convocatoria_id = c.id
            JOIN usuarios u ON p.usuario_id = u.id
            WHERE p.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function findByUsuarioYConvocatoria(int $usuarioId, int $convocatoriaId): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT * FROM postulaciones 
            WHERE usuario_id = :usuario_id AND convocatoria_id = :convocatoria_id 
            LIMIT 1
        ");
        $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':convocatoria_id' => $convocatoriaId
        ]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function iniciar(int $usuarioId, int $convocatoriaId): int {
        $pdo = Database::getConnection();
        $codigoRadicado = sprintf('RAD-%s-%04d', date('Y'), rand(100, 9999));

        $stmt = $pdo->prepare("
            INSERT INTO postulaciones (convocatoria_id, usuario_id, codigo_radicado, estado, resultado_documental, porcentaje_avance, fecha_postulacion)
            VALUES (:convocatoria_id, :usuario_id, :codigo_radicado, 'borrador', 'pendiente', 15, CURRENT_TIMESTAMP)
        ");
        $stmt->execute([
            ':convocatoria_id' => $convocatoriaId,
            ':usuario_id' => $usuarioId,
            ':codigo_radicado' => $codigoRadicado
        ]);

        return (int)$pdo->lastInsertId();
    }

    public static function calcularProgreso(int $postulacionId): int {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total_items,
                   SUM(CASE WHEN estado = 'aprobado' THEN 1 ELSE 0 END) as aprobados
            FROM items_hv 
            WHERE postulacion_id = :id
        ");
        $stmt->execute([':id' => $postulacionId]);
        $row = $stmt->fetch();

        $total = (int)($row['total_items'] ?? 0);
        if ($total === 0) return 10;
        
        $pct = min(100, (int)round(($total / 10) * 100)); // Base scaling
        
        $upStmt = $pdo->prepare("UPDATE postulaciones SET porcentaje_avance = :pct WHERE id = :id");
        $upStmt->execute([':pct' => $pct, ':id' => $postulacionId]);

        return $pct;
    }

    public static function enviar(int $id, bool $declaracionVeracidad, bool $bancoTalento): void {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            UPDATE postulaciones 
            SET estado = 'enviada',
                resultado_documental = 'pendiente',
                fecha_envio = CURRENT_TIMESTAMP,
                declaracion_veracidad = :veracidad,
                fecha_declaracion = CURRENT_TIMESTAMP,
                banco_talento_consentimiento = :banco,
                porcentaje_avance = 100
            WHERE id = :id
        ");
        $stmt->execute([
            ':id' => $id,
            ':veracidad' => $declaracionVeracidad ? 1 : 0,
            ':banco' => $bancoTalento ? 1 : 0
        ]);
    }

    public static function retirar(int $id, string $motivo): void {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            UPDATE postulaciones 
            SET estado = 'retirada', motivo_descarte = :motivo
            WHERE id = :id
        ");
        $stmt->execute([':id' => $id, ':motivo' => $motivo]);
    }

    public static function actualizarEstado(int $id, string $nuevoEstado, ?string $etapa = null, ?string $motivo = null, ?string $causalId = null): void {
        $pdo = Database::getConnection();
        $sql = "UPDATE postulaciones SET estado = :estado";
        $params = [':id' => $id, ':estado' => $nuevoEstado];

        if ($etapa !== null) {
            $sql .= ", etapa_actual = :etapa";
            $params[':etapa'] = $etapa;
        }
        if ($motivo !== null) {
            $sql .= ", motivo_descarte = :motivo";
            $params[':motivo'] = $motivo;
        }
        if ($causalId !== null) {
            $sql .= ", causal_descarte_id = :causal_id";
            $params[':causal_id'] = $causalId;
        }

        $sql .= " WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }

    public static function getStatsGlobales(): array {
        $pdo = Database::getConnection();
        return [
            'total_postulados' => (int)$pdo->query("SELECT COUNT(*) FROM postulaciones")->fetchColumn(),
            'en_proceso' => (int)$pdo->query("SELECT COUNT(*) FROM postulaciones WHERE estado IN ('enviada', 'en_revision', 'habilitada', 'preseleccionada', 'en_formalizacion')")->fetchColumn(),
            'no_aprobados' => (int)$pdo->query("SELECT COUNT(*) FROM postulaciones WHERE estado IN ('descartada', 'no_habilitada', 'no_seleccionada')")->fetchColumn(),
            'seleccionados' => (int)$pdo->query("SELECT COUNT(*) FROM postulaciones WHERE estado IN ('seleccionada', 'contratado', 'vinculado')")->fetchColumn(),
            'vinculados' => (int)$pdo->query("SELECT COUNT(*) FROM vinculaciones WHERE estado = 'vinculado'")->fetchColumn(),
            'total_convocatorias' => (int)$pdo->query("SELECT COUNT(*) FROM convocatorias WHERE estado = 'publicada'")->fetchColumn()
        ];
    }
}
