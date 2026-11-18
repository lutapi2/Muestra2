<?php

declare(strict_types=1);

namespace Model\DAO;

use Model\VO\DecisionFinalVO;
use PDO;

/**
 * DAO para el registro y publicación de la decisión final humana autorizada.
 * Satisface: RF-073 (Registrar decisión final), RF-074 (Notificar estado al candidato), RN-015, RNF-018.
 */
class DecisionFinalDAO extends BaseDAO
{
    /**
     * Registra el veredicto final humano (SELECCIONADO, NO_SELECCIONADO, ELEGIBLE).
     * Satisface: RF-073, RN-015 (Decisión humana responsable), RNF-018.
     */
    public function registrarDecision(DecisionFinalVO $dec): DecisionFinalVO
    {
        $this->beginTransaction();
        try {
            $sql = "INSERT INTO decision_final (id_postulacion, resultado, fundamento, id_usuario, fecha_decision, fecha_publicacion)
                    VALUES (:id_postulacion, :resultado, :fundamento, :id_usuario, NOW(), :fecha_publicacion)
                    ON DUPLICATE KEY UPDATE resultado = :resultado_upd, fundamento = :fundamento_upd, id_usuario = :usuario_upd, fecha_decision = NOW()";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id_postulacion', $dec->idPostulacion, PDO::PARAM_INT);
            $stmt->bindValue(':resultado', $dec->resultado, PDO::PARAM_STR);
            $stmt->bindValue(':fundamento', $dec->fundamento, PDO::PARAM_STR);
            $stmt->bindValue(':id_usuario', $dec->idUsuario, PDO::PARAM_INT);
            $stmt->bindValue(':fecha_publicacion', $dec->fechaPublicacion, $dec->fechaPublicacion !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':resultado_upd', $dec->resultado, PDO::PARAM_STR);
            $stmt->bindValue(':fundamento_upd', $dec->fundamento, PDO::PARAM_STR);
            $stmt->bindValue(':usuario_upd', $dec->idUsuario, PDO::PARAM_INT);
            $stmt->execute();

            $id = $this->lastInsertId() ?: ($dec->idDecision ?? 0);

            // Actualizar estado de la postulación al resultado emitido
            $sqlPost = "UPDATE postulacion SET estado = :estado WHERE id_postulacion = :id_post";
            $stmtPost = $this->db->prepare($sqlPost);
            $stmtPost->bindValue(':estado', $dec->resultado, PDO::PARAM_STR);
            $stmtPost->bindValue(':id_post', $dec->idPostulacion, PDO::PARAM_INT);
            $stmtPost->execute();

            $this->commit();
            return $this->buscarPorId($id) ?? $dec;
        } catch (\Throwable $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Busca la decisión por su ID.
     */
    public function buscarPorId(int $idDecision): ?DecisionFinalVO
    {
        $sql = "SELECT id_decision, id_postulacion, resultado, fundamento, id_usuario, fecha_decision, fecha_publicacion
                FROM decision_final
                WHERE id_decision = :id_decision
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_decision', $idDecision, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? DecisionFinalVO::fromArray($row) : null;
    }

    /**
     * Busca la decisión final registrada para una postulación.
     * Satisface: RF-073, RF-074.
     */
    public function buscarPorPostulacion(int $idPostulacion): ?DecisionFinalVO
    {
        $sql = "SELECT id_decision, id_postulacion, resultado, fundamento, id_usuario, fecha_decision, fecha_publicacion
                FROM decision_final
                WHERE id_postulacion = :id_postulacion
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_postulacion', $idPostulacion, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? DecisionFinalVO::fromArray($row) : null;
    }

    /**
     * Publica la decisión en el portal del aspirante.
     * Satisface: RF-074 (Notificar estado al candidato).
     */
    public function publicarDecision(int $idDecision): bool
    {
        $sql = "UPDATE decision_final SET fecha_publicacion = NOW() WHERE id_decision = :id_decision";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_decision', $idDecision, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
