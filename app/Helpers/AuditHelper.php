<?php
namespace App\Helpers;

use App\Core\Database;

class AuditHelper {
    public static function log(string $accion, string $entidad, $entidadId, $datosAntes = null, $datosDespues = null): void {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                INSERT INTO auditoria (usuario_id, accion, entidad, entidad_id, datos_antes, datos_despues, ip, user_agent, fecha_hora)
                VALUES (:usuario_id, :accion, :entidad, :entidad_id, :datos_antes, :datos_despues, :ip, :user_agent, CURRENT_TIMESTAMP)
            ");
            $stmt->execute([
                ':usuario_id' => AuthHelper::getUsuarioId(),
                ':accion' => $accion,
                ':entidad' => $entidad,
                ':entidad_id' => (string)$entidadId,
                ':datos_antes' => $datosAntes ? (is_string($datosAntes) ? $datosAntes : json_encode($datosAntes, JSON_UNESCAPED_UNICODE)) : null,
                ':datos_despues' => $datosDespues ? (is_string($datosDespues) ? $datosDespues : json_encode($datosDespues, JSON_UNESCAPED_UNICODE)) : null,
                ':ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                ':user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255)
            ]);
        } catch (\Exception $e) {
            // Fail silently without stopping business flow
            error_log("Error auditoria: " . $e->getMessage());
        }
    }

    public static function logMovimiento(int $postulacionId, string $accion, ?string $estadoAnterior, ?string $estadoNuevo, ?string $detalle = null, bool $esInterno = false): void {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                INSERT INTO movimientos_historial (postulacion_id, usuario_id, accion, estado_anterior, estado_nuevo, detalle, es_interno, fecha_hora)
                VALUES (:postulacion_id, :usuario_id, :accion, :estado_anterior, :estado_nuevo, :detalle, :es_interno, CURRENT_TIMESTAMP)
            ");
            $stmt->execute([
                ':postulacion_id' => $postulacionId,
                ':usuario_id' => AuthHelper::getUsuarioId() ?? 1,
                ':accion' => $accion,
                ':estado_anterior' => $estadoAnterior,
                ':estado_nuevo' => $estadoNuevo,
                ':detalle' => $detalle,
                ':es_interno' => $esInterno ? 1 : 0
            ]);
        } catch (\Exception $e) {
            error_log("Error movimiento: " . $e->getMessage());
        }
    }
}
