<?php
namespace App\Helpers;

use App\Core\Database;

class NotifyHelper {
    public static function enviar(int $usuarioId, string $asunto, string $mensaje, ?int $postulacionId = null, string $canal = 'portal'): void {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                INSERT INTO notificaciones (usuario_id, postulacion_id, canal, asunto, mensaje, leida, fecha_envio)
                VALUES (:usuario_id, :postulacion_id, :canal, :asunto, :mensaje, 0, CURRENT_TIMESTAMP)
            ");
            $stmt->execute([
                ':usuario_id' => $usuarioId,
                ':postulacion_id' => $postulacionId,
                ':canal' => $canal,
                ':asunto' => $asunto,
                ':mensaje' => $mensaje
            ]);
        } catch (\Exception $e) {
            error_log("Error notificación: " . $e->getMessage());
        }
    }

    public static function obtenerNoLeidas(int $usuarioId): array {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                SELECT * FROM notificaciones 
                WHERE usuario_id = :usuario_id 
                ORDER BY fecha_envio DESC LIMIT 10
            ");
            $stmt->execute([':usuario_id' => $usuarioId]);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            return [];
        }
    }
}
