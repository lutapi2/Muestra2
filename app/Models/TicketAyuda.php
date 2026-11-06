<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class TicketAyuda {
    public static function listar(?int $usuarioId = null): array {
        $pdo = Database::getConnection();
        $sql = "
            SELECT t.*, u.nombres, u.apellidos, u.correo, r.nombres as respondido_nombres, r.apellidos as respondido_apellidos
            FROM tickets_ayuda t
            JOIN usuarios u ON t.usuario_id = u.id
            LEFT JOIN usuarios r ON t.respondido_por = r.id
        ";
        $params = [];
        if ($usuarioId !== null) {
            $sql .= " WHERE t.usuario_id = :uid";
            $params[':uid'] = $usuarioId;
        }
        $sql .= " ORDER BY t.fecha_creacion DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function crear(int $usuarioId, string $categoria, string $asunto, string $mensaje): int {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO tickets_ayuda (usuario_id, categoria, asunto, mensaje, estado, fecha_creacion)
            VALUES (:usuario_id, :categoria, :asunto, :mensaje, 'abierto', CURRENT_TIMESTAMP)
        ");
        $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':categoria' => $categoria,
            ':asunto' => $asunto,
            ':mensaje' => $mensaje
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function responder(int $ticketId, string $respuesta, int $respondidoPor): void {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            UPDATE tickets_ayuda 
            SET respuesta = :respuesta,
                respondido_por = :respondido_por,
                estado = 'resuelto',
                fecha_respuesta = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        $stmt->execute([
            ':id' => $ticketId,
            ':respuesta' => $respuesta,
            ':respondido_por' => $respondidoPor
        ]);
    }
}
