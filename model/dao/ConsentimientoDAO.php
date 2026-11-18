<?php

declare(strict_types=1);

namespace Model\DAO;

use Model\VO\ConsentimientoVO;
use PDO;

/**
 * DAO para la gestión y auditoría inmutable de consentimientos legales (Habeas Data).
 * Satisface: RF-015 (Aceptar tratamiento de datos), RF-076 (Banco de candidatos), RN-004, RN-021, RNF-015.
 */
class ConsentimientoDAO extends BaseDAO
{
    /**
     * Registra la aceptación electrónica de consentimiento con timestamp e IP.
     * Satisface: RF-015, RN-004, RNF-015.
     */
    public function registrarConsentimiento(ConsentimientoVO $cons): ConsentimientoVO
    {
        $sql = "INSERT INTO consentimiento (id_candidato, id_postulacion, finalidad, version_texto, aceptado, fecha_hora, ip)
                VALUES (:id_candidato, :id_postulacion, :finalidad, :version_texto, :aceptado, NOW(), :ip)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_candidato', $cons->idCandidato, PDO::PARAM_INT);
        $stmt->bindValue(':id_postulacion', $cons->idPostulacion, $cons->idPostulacion !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':finalidad', $cons->finalidad, PDO::PARAM_STR);
        $stmt->bindValue(':version_texto', $cons->versionTexto, PDO::PARAM_STR);
        $stmt->bindValue(':aceptado', $cons->aceptado, PDO::PARAM_BOOL);
        $stmt->bindValue(':ip', $cons->ip, $cons->ip !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->execute();

        $id = $this->lastInsertId();
        return $this->buscarPorId($id) ?? $cons;
    }

    /**
     * Busca consentimiento por ID.
     */
    public function buscarPorId(int $idConsentimiento): ?ConsentimientoVO
    {
        $sql = "SELECT id_consentimiento, id_candidato, id_postulacion, finalidad, version_texto, aceptado, fecha_hora, ip, revocado_en
                FROM consentimiento
                WHERE id_consentimiento = :id_consentimiento
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_consentimiento', $idConsentimiento, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? ConsentimientoVO::fromArray($row) : null;
    }

    /**
     * Consulta si existe un consentimiento aceptado y no revocado para una finalidad.
     * Satisface: RF-015, RF-038 (Validación pre-envío), RN-004, RN-021.
     */
    public function obtenerVigentePorCandidato(int $idCandidato, string $finalidad): ?ConsentimientoVO
    {
        $sql = "SELECT id_consentimiento, id_candidato, id_postulacion, finalidad, version_texto, aceptado, fecha_hora, ip, revocado_en
                FROM consentimiento
                WHERE id_candidato = :id_candidato
                  AND finalidad = :finalidad
                  AND aceptado = 1
                  AND revocado_en IS NULL
                ORDER BY fecha_hora DESC
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_candidato', $idCandidato, PDO::PARAM_INT);
        $stmt->bindValue(':finalidad', $finalidad, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? ConsentimientoVO::fromArray($row) : null;
    }
}
