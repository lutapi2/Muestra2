<?php

declare(strict_types=1);

namespace Model\DAO;

use Model\VO\SoporteVO;
use PDO;

/**
 * DAO para el registro, validación y control de acceso a archivos de soporte documental.
 * Satisface: RF-032, RF-033, RF-034, RF-035, RN-006, RNF-006, RNF-013, RNF-014.
 */
class SoporteDAO extends BaseDAO
{
    /**
     * Registra un archivo de soporte documental asociado a la versión de un ítem.
     * Satisface: RF-032 (Adjuntar soporte por ítem), RF-034 (Validar archivos), RN-006, RNF-013.
     */
    public function crear(SoporteVO $soporte): SoporteVO
    {
        $sql = "INSERT INTO soporte (id_version_item, nombre_original, nombre_almacenado, mime, tamano_bytes, hash_sha256, ruta_segura, estado_archivo)
                VALUES (:id_version, :nombre_original, :nombre_almacenado, :mime, :tamano_bytes, :hash, :ruta_segura, :estado_archivo)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_version', $soporte->idVersionItem, PDO::PARAM_INT);
        $stmt->bindValue(':nombre_original', $soporte->nombreOriginal, PDO::PARAM_STR);
        $stmt->bindValue(':nombre_almacenado', $soporte->nombreAlmacenado, PDO::PARAM_STR);
        $stmt->bindValue(':mime', $soporte->mime, PDO::PARAM_STR);
        $stmt->bindValue(':tamano_bytes', $soporte->tamanoBytes, PDO::PARAM_INT);
        $stmt->bindValue(':hash', $soporte->hashSha256, PDO::PARAM_STR);
        $stmt->bindValue(':ruta_segura', $soporte->rutaSegura, PDO::PARAM_STR);
        $stmt->bindValue(':estado_archivo', $soporte->estadoArchivo, PDO::PARAM_STR);
        $stmt->execute();

        $id = $this->lastInsertId();
        return $this->buscarPorId($id) ?? $soporte;
    }

    /**
     * Busca los metadatos de un soporte por su identificador primario.
     * Satisface: RF-035 (Previsualizar y descargar soportes), RNF-010, RNF-013.
     */
    public function buscarPorId(int $idSoporte): ?SoporteVO
    {
        $sql = "SELECT id_soporte, id_version_item, nombre_original, nombre_almacenado, mime,
                       tamano_bytes, hash_sha256, ruta_segura, estado_archivo, fecha_carga
                FROM soporte
                WHERE id_soporte = :id_soporte
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_soporte', $idSoporte, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? SoporteVO::fromArray($row) : null;
    }

    /**
     * Lista todos los soportes documentales asociados a una versión específica de ítem.
     * Satisface: RF-035, RF-045 (Revisar dato y soporte juntos).
     */
    public function listarPorVersionItem(int $idVersionItem): array
    {
        $sql = "SELECT id_soporte, id_version_item, nombre_original, nombre_almacenado, mime,
                       tamano_bytes, hash_sha256, ruta_segura, estado_archivo, fecha_carga
                FROM soporte
                WHERE id_version_item = :id_version
                ORDER BY fecha_carga ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_version', $idVersionItem, PDO::PARAM_INT);
        $stmt->execute();

        $lista = [];
        while ($row = $stmt->fetch()) {
            $lista[] = SoporteVO::fromArray($row);
        }
        return $lista;
    }

    /**
     * Cuenta la cantidad de archivos válidos asociados a una versión para validar obligatoriedad.
     * Satisface: RF-033 (Aplicar obligatoriedad de soporte), RF-038, RN-007, RN-008.
     */
    public function contarSoportesValidosPorVersion(int $idVersionItem): int
    {
        $sql = "SELECT COUNT(*) FROM soporte WHERE id_version_item = :id_version AND estado_archivo = 'VALIDO'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_version', $idVersionItem, PDO::PARAM_INT);
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }
}
