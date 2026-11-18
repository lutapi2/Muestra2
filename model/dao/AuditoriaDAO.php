<?php

declare(strict_types=1);

namespace Model\DAO;

use Model\VO\AuditoriaVO;
use PDO;

/**
 * DAO para la bitácora técnica de seguridad y auditoría inmutable de transacciones críticas.
 * Satisface: RF-059, RNF-019 (Historial íntegro e inalterable).
 */
class AuditoriaDAO extends BaseDAO
{
    /**
     * Registra un evento técnico de auditoría con datos de estado anterior y posterior.
     * Satisface: RNF-019.
     */
    public function registrar(AuditoriaVO $aud): AuditoriaVO
    {
        $sql = "INSERT INTO auditoria (id_usuario, accion, entidad, id_entidad, datos_antes, datos_despues, ip, fecha_hora)
                VALUES (:id_usuario, :accion, :entidad, :id_entidad, :datos_antes, :datos_despues, :ip, NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_usuario', $aud->idUsuario, $aud->idUsuario !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':accion', $aud->accion, PDO::PARAM_STR);
        $stmt->bindValue(':entidad', $aud->entidad, PDO::PARAM_STR);
        $stmt->bindValue(':id_entidad', $aud->idEntidad, $aud->idEntidad !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':datos_antes', $aud->datosAntes !== null ? json_encode($aud->datosAntes, JSON_UNESCAPED_UNICODE) : null, $aud->datosAntes !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':datos_despues', $aud->datosDespues !== null ? json_encode($aud->datosDespues, JSON_UNESCAPED_UNICODE) : null, $aud->datosDespues !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':ip', $aud->ip, $aud->ip !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->execute();

        $id = $this->lastInsertId();
        return $this->buscarPorId($id) ?? $aud;
    }

    /**
     * Busca registro de auditoría por su ID.
     */
    public function buscarPorId(int $idAuditoria): ?AuditoriaVO
    {
        $sql = "SELECT id_auditoria, id_usuario, accion, entidad, id_entidad, datos_antes, datos_despues, ip, fecha_hora
                FROM auditoria
                WHERE id_auditoria = :id_auditoria
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_auditoria', $idAuditoria, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? AuditoriaVO::fromArray($row) : null;
    }
}
