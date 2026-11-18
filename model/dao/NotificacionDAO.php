<?php

declare(strict_types=1);

namespace Model\DAO;

use Model\VO\NotificacionVO;
use PDO;

/**
 * DAO para gestión del buzón de notificaciones en portal y colas de avisos.
 * Satisface: RF-066, RF-074, RF-081, RF-100, RN-022, RN-028, RNF-030.
 */
class NotificacionDAO extends BaseDAO
{
    /**
     * Registra un aviso para el usuario en el portal web (y en cola de correo si aplica).
     * Satisface: RF-066 (Notificar citación), RF-074 (Notificar estado), RF-100 (Avisos automáticos), RN-022.
     */
    public function crear(NotificacionVO $notif): NotificacionVO
    {
        $sql = "INSERT INTO notificacion (id_destinatario, id_postulacion, canal, asunto, mensaje, fecha_creacion, estado_envio)
                VALUES (:id_destinatario, :id_postulacion, :canal, :asunto, :mensaje, NOW(), :estado_envio)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_destinatario', $notif->idDestinatario, PDO::PARAM_INT);
        $stmt->bindValue(':id_postulacion', $notif->idPostulacion, $notif->idPostulacion !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':canal', $notif->canal, PDO::PARAM_STR);
        $stmt->bindValue(':asunto', $notif->asunto, PDO::PARAM_STR);
        $stmt->bindValue(':mensaje', $notif->mensaje, PDO::PARAM_STR);
        $stmt->bindValue(':estado_envio', $notif->estadoEnvio, PDO::PARAM_STR);
        $stmt->execute();

        $id = $this->lastInsertId();
        return $this->buscarPorId($id) ?? $notif;
    }

    /**
     * Busca una notificación por ID.
     */
    public function buscarPorId(int $idNotificacion): ?NotificacionVO
    {
        $sql = "SELECT id_notificacion, id_destinatario, id_postulacion, canal, asunto, mensaje,
                       fecha_creacion, leida_en, estado_envio
                FROM notificacion
                WHERE id_notificacion = :id_notificacion
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_notificacion', $idNotificacion, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? NotificacionVO::fromArray($row) : null;
    }

    /**
     * Obtiene el buzón de notificaciones de un usuario con filtro opcional de pendientes de lectura.
     * Satisface: RF-066, RF-074, RN-022 (Portal como canal principal).
     */
    public function listarPorDestinatario(int $idDestinatario, bool $soloNoLeidas = false): array
    {
        $sql = "SELECT id_notificacion, id_destinatario, id_postulacion, canal, asunto, mensaje,
                       fecha_creacion, leida_en, estado_envio
                FROM notificacion
                WHERE id_destinatario = :id_destinatario"
                . ($soloNoLeidas ? " AND leida_en IS NULL" : "") . "
                ORDER BY fecha_creacion DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_destinatario', $idDestinatario, PDO::PARAM_INT);
        $stmt->execute();

        $lista = [];
        while ($row = $stmt->fetch()) {
            $lista[] = NotificacionVO::fromArray($row);
        }
        return $lista;
    }

    /**
     * Marca una notificación como leída con timestamp.
     * Satisface: RF-066, RN-022.
     */
    public function marcarLeida(int $idNotificacion): bool
    {
        $sql = "UPDATE notificacion SET leida_en = NOW() WHERE id_notificacion = :id_notif";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_notif', $idNotificacion, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
