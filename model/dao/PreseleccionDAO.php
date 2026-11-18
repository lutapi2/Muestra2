<?php

declare(strict_types=1);

namespace Model\DAO;

use Model\VO\PreseleccionVO;
use PDO;

/**
 * DAO para la autorización formal de preselección hacia etapas de pruebas y entrevistas.
 * Satisface: RF-057 (Preseleccionar candidatos), RN-012 (Cumplimiento mínimo), RN-016 (Preselección previa).
 */
class PreseleccionDAO extends BaseDAO
{
    /**
     * Registra la autorización de preselección cambiando la postulación a PRESELECCIONADA.
     * Satisface: RF-057, RN-012, RN-016.
     */
    public function autorizar(PreseleccionVO $pres): PreseleccionVO
    {
        $this->beginTransaction();
        try {
            $sql = "INSERT INTO preseleccion (id_postulacion, id_usuario, fundamento, es_excepcion, estado)
                    VALUES (:id_postulacion, :id_usuario, :fundamento, :es_excepcion, 'VIGENTE')";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id_postulacion', $pres->idPostulacion, PDO::PARAM_INT);
            $stmt->bindValue(':id_usuario', $pres->idUsuario, PDO::PARAM_INT);
            $stmt->bindValue(':fundamento', $pres->fundamento, PDO::PARAM_STR);
            $stmt->bindValue(':es_excepcion', $pres->esExcepcion, PDO::PARAM_BOOL);
            $stmt->execute();

            $id = $this->lastInsertId();

            $sqlPost = "UPDATE postulacion SET estado = 'PRESELECCIONADA' WHERE id_postulacion = :id_post";
            $stmtPost = $this->db->prepare($sqlPost);
            $stmtPost->bindValue(':id_post', $pres->idPostulacion, PDO::PARAM_INT);
            $stmtPost->execute();

            $this->commit();
            return $this->buscarPorId($id) ?? $pres;
        } catch (\Throwable $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Busca la preselección por su ID.
     */
    public function buscarPorId(int $idPreseleccion): ?PreseleccionVO
    {
        $sql = "SELECT id_preseleccion, id_postulacion, id_usuario, fecha, fundamento, es_excepcion, estado
                FROM preseleccion
                WHERE id_preseleccion = :id_preseleccion
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_preseleccion', $idPreseleccion, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? PreseleccionVO::fromArray($row) : null;
    }

    /**
     * Busca la preselección vigente de una postulación.
     * Satisface: RF-057, RF-063, RN-016.
     */
    public function buscarPorPostulacion(int $idPostulacion): ?PreseleccionVO
    {
        $sql = "SELECT id_preseleccion, id_postulacion, id_usuario, fecha, fundamento, es_excepcion, estado
                FROM preseleccion
                WHERE id_postulacion = :id_postulacion AND estado = 'VIGENTE'
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_postulacion', $idPostulacion, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? PreseleccionVO::fromArray($row) : null;
    }
}
