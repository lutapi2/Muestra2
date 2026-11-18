<?php

declare(strict_types=1);

namespace Model\DAO;

use Model\VO\SolicitudSubsanacionVO;
use Model\VO\DetalleSubsanacionVO;
use PDO;
use RuntimeException;

/**
 * DAO para gestión del flujo de subsanación documental y plazos.
 * Satisface: RF-048, RF-049, RF-050, RF-051, RN-011.
 */
class SolicitudSubsanacionDAO extends BaseDAO
{
    /**
     * Crea un requerimiento de subsanación agrupando los ítems observados y cambiando el estado de la postulación.
     * Satisface: RF-048 (Solicitar corrección), RN-011 (Subsanación limitada a ítems requeridos).
     *
     * @param SolicitudSubsanacionVO $solicitud Objeto de solicitud con fecha límite
     * @param array<array{id_item_hv: int, motivo: string, id_revision?: int|null}> $itemsObservados
     */
    public function crearSolicitudConDetalles(SolicitudSubsanacionVO $solicitud, array $itemsObservados): SolicitudSubsanacionVO
    {
        $this->beginTransaction();
        try {
            $sqlSol = "INSERT INTO solicitud_subsanacion (id_postulacion, id_solicitante, fecha_solicitud, fecha_limite, estado)
                       VALUES (:id_postulacion, :id_solicitante, NOW(), :fecha_limite, 'VIGENTE')";

            $stmtSol = $this->db->prepare($sqlSol);
            $stmtSol->bindValue(':id_postulacion', $solicitud->idPostulacion, PDO::PARAM_INT);
            $stmtSol->bindValue(':id_solicitante', $solicitud->idSolicitante, PDO::PARAM_INT);
            $stmtSol->bindValue(':fecha_limite', $solicitud->fechaLimite, PDO::PARAM_STR);
            $stmtSol->execute();

            $idSolicitud = $this->lastInsertId();

            $sqlDet = "INSERT INTO detalle_subsanacion (id_solicitud, id_item_hv, id_revision, motivo, estado)
                       VALUES (:id_solicitud, :id_item_hv, :id_revision, :motivo, 'PENDIENTE')";
            $stmtDet = $this->db->prepare($sqlDet);

            foreach ($itemsObservados as $obs) {
                $stmtDet->bindValue(':id_solicitud', $idSolicitud, PDO::PARAM_INT);
                $stmtDet->bindValue(':id_item_hv', $obs['id_item_hv'], PDO::PARAM_INT);
                $idRev = $obs['id_revision'] ?? null;
                $stmtDet->bindValue(':id_revision', $idRev, $idRev !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
                $stmtDet->bindValue(':motivo', $obs['motivo'], PDO::PARAM_STR);
                $stmtDet->execute();
            }

            // Cambiar estado de la postulación a SUBSANACION_REQUERIDA
            $sqlPost = "UPDATE postulacion SET estado = 'SUBSANACION_REQUERIDA' WHERE id_postulacion = :id_post";
            $stmtPost = $this->db->prepare($sqlPost);
            $stmtPost->bindValue(':id_post', $solicitud->idPostulacion, PDO::PARAM_INT);
            $stmtPost->execute();

            $this->commit();
            return $this->buscarPorId($idSolicitud) ?? $solicitud;
        } catch (\Throwable $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Busca la solicitud de subsanación vigente de una postulación.
     * Satisface: RF-048, RF-049, RF-051, RN-011.
     */
    public function buscarVigentePorPostulacion(int $idPostulacion): ?SolicitudSubsanacionVO
    {
        $sql = "SELECT id_solicitud, id_postulacion, id_solicitante, fecha_solicitud, fecha_limite,
                       fecha_extension, estado, fecha_respuesta
                FROM solicitud_subsanacion
                WHERE id_postulacion = :id_postulacion AND estado = 'VIGENTE'
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_postulacion', $idPostulacion, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? SolicitudSubsanacionVO::fromArray($row) : null;
    }

    /**
     * Busca una solicitud por su ID.
     */
    public function buscarPorId(int $idSolicitud): ?SolicitudSubsanacionVO
    {
        $sql = "SELECT id_solicitud, id_postulacion, id_solicitante, fecha_solicitud, fecha_limite,
                       fecha_extension, estado, fecha_respuesta
                FROM solicitud_subsanacion
                WHERE id_solicitud = :id_solicitud
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_solicitud', $idSolicitud, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? SolicitudSubsanacionVO::fromArray($row) : null;
    }

    /**
     * Obtiene los ítems autorizados para corrección en una solicitud.
     * Satisface: RF-049 (Corregir únicamente ítems observados), RN-011.
     */
    public function obtenerDetalles(int $idSolicitud): array
    {
        $sql = "SELECT id_detalle, id_solicitud, id_item_hv, id_revision, motivo, estado, id_version_corregida
                FROM detalle_subsanacion
                WHERE id_solicitud = :id_solicitud
                ORDER BY id_detalle ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_solicitud', $idSolicitud, PDO::PARAM_INT);
        $stmt->execute();

        $lista = [];
        while ($row = $stmt->fetch()) {
            $lista[] = DetalleSubsanacionVO::fromArray($row);
        }
        return $lista;
    }

    /**
     * Vincula la nueva versión corregida a un ítem en subsanación.
     * Satisface: RF-049, RF-050.
     */
    public function marcarDetalleCorregido(int $idSolicitud, int $idItemHv, int $idVersionCorregida): bool
    {
        $sql = "UPDATE detalle_subsanacion
                SET estado = 'CORREGIDO', id_version_corregida = :id_version
                WHERE id_solicitud = :id_solicitud AND id_item_hv = :id_item";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_version', $idVersionCorregida, PDO::PARAM_INT);
        $stmt->bindValue(':id_solicitud', $idSolicitud, PDO::PARAM_INT);
        $stmt->bindValue(':id_item', $idItemHv, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Concluye la subsanación por parte del candidato, retornando el expediente a EN_REVISION.
     * Satisface: RF-050 (Enviar subsanación), RN-009, RN-011.
     */
    public function responderSolicitud(int $idSolicitud, int $idPostulacion): bool
    {
        $this->beginTransaction();
        try {
            $sqlSol = "UPDATE solicitud_subsanacion
                       SET estado = 'RESPONDIDA', fecha_respuesta = NOW()
                       WHERE id_solicitud = :id_sol AND estado = 'VIGENTE'";
            $stmtSol = $this->db->prepare($sqlSol);
            $stmtSol->bindValue(':id_sol', $idSolicitud, PDO::PARAM_INT);
            $stmtSol->execute();

            $sqlPost = "UPDATE postulacion SET estado = 'EN_REVISION' WHERE id_postulacion = :id_post";
            $stmtPost = $this->db->prepare($sqlPost);
            $stmtPost->bindValue(':id_post', $idPostulacion, PDO::PARAM_INT);
            $stmtPost->execute();

            $this->commit();
            return true;
        } catch (\Throwable $e) {
            $this->rollBack();
            throw $e;
        }
    }
}
