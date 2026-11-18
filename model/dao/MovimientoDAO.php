<?php

declare(strict_types=1);

namespace Model\DAO;

use Model\VO\MovimientoVO;
use PDO;

/**
 * DAO para la bitácora funcional y línea de tiempo de la postulación.
 * Satisface: RF-059 (Mostrar historial del proceso), RN-019 (Separación de visibilidad), RN-020 (Eventos críticos auditados).
 */
class MovimientoDAO extends BaseDAO
{
    /**
     * Registra un nuevo evento funcional en la línea de tiempo.
     * Satisface: RF-059, RN-020.
     */
    public function registrar(MovimientoVO $mov): MovimientoVO
    {
        $sql = "INSERT INTO movimiento (id_postulacion, id_usuario, accion, estado_anterior, estado_nuevo, id_motivo, detalle, visible_candidato, fecha_hora)
                VALUES (:id_postulacion, :id_usuario, :accion, :estado_anterior, :estado_nuevo, :id_motivo, :detalle, :visible, NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_postulacion', $mov->idPostulacion, PDO::PARAM_INT);
        $stmt->bindValue(':id_usuario', $mov->idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':accion', $mov->accion, PDO::PARAM_STR);
        $stmt->bindValue(':estado_anterior', $mov->estadoAnterior, $mov->estadoAnterior !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':estado_nuevo', $mov->estadoNuevo, $mov->estadoNuevo !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':id_motivo', $mov->idMotivo, $mov->idMotivo !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':detalle', $mov->detalle, $mov->detalle !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':visible', $mov->visibleCandidato, PDO::PARAM_BOOL);
        $stmt->execute();

        $id = $this->lastInsertId();
        return $this->buscarPorId($id) ?? $mov;
    }

    /**
     * Busca movimiento por ID.
     */
    public function buscarPorId(int $idMovimiento): ?MovimientoVO
    {
        $sql = "SELECT id_movimiento, id_postulacion, id_usuario, accion, estado_anterior, estado_nuevo,
                       id_motivo, detalle, visible_candidato, fecha_hora
                FROM movimiento
                WHERE id_movimiento = :id_movimiento
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_movimiento', $idMovimiento, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? MovimientoVO::fromArray($row) : null;
    }

    /**
     * Consulta la línea de tiempo de una postulación, permitiendo filtrar solo los eventos autorizados para el candidato.
     * Satisface: RF-059, RN-019 (Notas internas no visibles para candidatos).
     */
    public function listarPorPostulacion(int $idPostulacion, bool $soloVisiblesCandidato = false): array
    {
        $sql = "SELECT id_movimiento, id_postulacion, id_usuario, accion, estado_anterior, estado_nuevo,
                       id_motivo, detalle, visible_candidato, fecha_hora
                FROM movimiento
                WHERE id_postulacion = :id_postulacion"
                . ($soloVisiblesCandidato ? " AND visible_candidato = 1" : "") . "
                ORDER BY fecha_hora ASC, id_movimiento ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_postulacion', $idPostulacion, PDO::PARAM_INT);
        $stmt->execute();

        $lista = [];
        while ($row = $stmt->fetch()) {
            $lista[] = MovimientoVO::fromArray($row);
        }
        return $lista;
    }
}
