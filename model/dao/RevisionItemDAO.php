<?php

declare(strict_types=1);

namespace Model\DAO;

use Model\VO\RevisionItemVO;
use PDO;

/**
 * DAO para la emisión y consulta de dictámenes de revisión documental por ítem.
 * Satisface: RF-045, RF-046, RF-047, RF-052, RF-053, RN-010.
 */
class RevisionItemDAO extends BaseDAO
{
    /**
     * Registra el dictamen de verificación de un ítem y su soporte por parte del revisor.
     * Satisface: RF-046 (Aprobar ítem), RF-047 (Observar o rechazar con motivo obligatorio), RN-010.
     */
    public function registrarDictamen(RevisionItemVO $rev): RevisionItemVO
    {
        $sql = "INSERT INTO revision_item (id_version_item, id_revisor, resultado, id_motivo, observacion)
                VALUES (:id_version, :id_revisor, :resultado, :id_motivo, :observacion)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_version', $rev->idVersionItem, PDO::PARAM_INT);
        $stmt->bindValue(':id_revisor', $rev->idRevisor, PDO::PARAM_INT);
        $stmt->bindValue(':resultado', $rev->resultado, PDO::PARAM_STR);
        $stmt->bindValue(':id_motivo', $rev->idMotivo, $rev->idMotivo !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':observacion', $rev->observacion, $rev->observacion !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->execute();

        $id = $this->lastInsertId();
        return $this->buscarPorId($id) ?? $rev;
    }

    /**
     * Busca una revisión por su ID.
     */
    public function buscarPorId(int $idRevision): ?RevisionItemVO
    {
        $sql = "SELECT id_revision, id_version_item, id_revisor, resultado, id_motivo, observacion, fecha_revision
                FROM revision_item
                WHERE id_revision = :id_revision
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_revision', $idRevision, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? RevisionItemVO::fromArray($row) : null;
    }

    /**
     * Obtiene el historial de revisiones emitidas para una versión de ítem.
     * Satisface: RF-052 (Revisar nuevamente una corrección), RN-010.
     */
    public function listarPorVersionItem(int $idVersionItem): array
    {
        $sql = "SELECT id_revision, id_version_item, id_revisor, resultado, id_motivo, observacion, fecha_revision
                FROM revision_item
                WHERE id_version_item = :id_version
                ORDER BY fecha_revision DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_version', $idVersionItem, PDO::PARAM_INT);
        $stmt->execute();

        $lista = [];
        while ($row = $stmt->fetch()) {
            $lista[] = RevisionItemVO::fromArray($row);
        }
        return $lista;
    }

    /**
     * Obtiene el último dictamen emitido sobre un ítem en su versión más reciente.
     * Satisface: RF-045, RF-053 (Calcular estado documental).
     */
    public function obtenerUltimoDictamenPorItem(int $idItemHv): ?RevisionItemVO
    {
        $sql = "SELECT r.id_revision, r.id_version_item, r.id_revisor, r.resultado, r.id_motivo, r.observacion, r.fecha_revision
                FROM revision_item r
                JOIN version_item v ON v.id_version_item = r.id_version_item
                WHERE v.id_item_hv = :id_item
                ORDER BY r.fecha_revision DESC, r.id_revision DESC
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_item', $idItemHv, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? RevisionItemVO::fromArray($row) : null;
    }
}
