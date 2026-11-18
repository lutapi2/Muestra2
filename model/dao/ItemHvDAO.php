<?php

declare(strict_types=1);

namespace Model\DAO;

use Model\VO\ItemHvVO;
use Model\VO\VersionItemVO;
use PDO;
use RuntimeException;

/**
 * DAO para la gestión atómica de ítems de hoja de vida y su control de versiones.
 * Satisface: RF-016 a RF-031, RF-045, RF-049, RF-050, RF-052.
 * Aplica Reglas: RN-005 (Ítems independientes), RN-009 (Bloqueo y versionado), RN-011 (Subsanación limitada).
 */
class ItemHvDAO extends BaseDAO
{
    /**
     * Registra un nuevo ítem atómico junto con su primera versión de datos en una sola transacción.
     * Satisface: RF-016 a RF-030 (Registro de estudios, experiencia, etc.), RF-031 (Múltiples registros), RN-005.
     *
     * @param int $idPostulacion ID de la postulación
     * @param int $idTipoItem ID del catálogo tipo_item_hv (HV-001 a HV-026)
     * @param array<string, mixed> $datos Arreglo de campos estructurados del ítem
     * @return array{item: ItemHvVO, version: VersionItemVO}
     */
    public function registrarItemConVersion(int $idPostulacion, int $idTipoItem, array $datos): array
    {
        $this->beginTransaction();
        try {
            $sqlItem = "INSERT INTO item_hv (id_postulacion, id_tipo_item, estado_revision)
                        VALUES (:id_postulacion, :id_tipo_item, 'PENDIENTE')";
            $stmtItem = $this->db->prepare($sqlItem);
            $stmtItem->bindValue(':id_postulacion', $idPostulacion, PDO::PARAM_INT);
            $stmtItem->bindValue(':id_tipo_item', $idTipoItem, PDO::PARAM_INT);
            $stmtItem->execute();

            $idItemHv = $this->lastInsertId();

            $sqlVer = "INSERT INTO version_item (id_item_hv, numero_version, datos)
                       VALUES (:id_item_hv, 1, :datos)";
            $stmtVer = $this->db->prepare($sqlVer);
            $stmtVer->bindValue(':id_item_hv', $idItemHv, PDO::PARAM_INT);
            $stmtVer->bindValue(':datos', json_encode($datos, JSON_UNESCAPED_UNICODE), PDO::PARAM_STR);
            $stmtVer->execute();

            $idVersion = $this->lastInsertId();

            $this->commit();

            $itemVO = $this->buscarPorId($idItemHv) ?? throw new RuntimeException("Error al recuperar el ítem creado.");
            $versionVO = $this->buscarVersionPorId($idVersion) ?? throw new RuntimeException("Error al recuperar la versión creada.");

            return ['item' => $itemVO, 'version' => $versionVO];
        } catch (\Throwable $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Registra una nueva versión corregida de un ítem existente dentro de un proceso de subsanación.
     * Satisface: RF-049 (Corregir únicamente ítems observados), RF-050 (Enviar subsanación), RN-009, RN-011.
     */
    public function crearVersionSubsanada(int $idItemHv, array $nuevosDatos): VersionItemVO
    {
        $this->beginTransaction();
        try {
            // Calcular número de versión consecutivo
            $sqlNum = "SELECT IFNULL(MAX(numero_version), 0) + 1 FROM version_item WHERE id_item_hv = :id_item";
            $stmtNum = $this->db->prepare($sqlNum);
            $stmtNum->bindValue(':id_item', $idItemHv, PDO::PARAM_INT);
            $stmtNum->execute();
            $siguienteVersion = (int)$stmtNum->fetchColumn();

            $sqlVer = "INSERT INTO version_item (id_item_hv, numero_version, datos)
                       VALUES (:id_item_hv, :num_version, :datos)";
            $stmtVer = $this->db->prepare($sqlVer);
            $stmtVer->bindValue(':id_item_hv', $idItemHv, PDO::PARAM_INT);
            $stmtVer->bindValue(':num_version', $siguienteVersion, PDO::PARAM_INT);
            $stmtVer->bindValue(':datos', json_encode($nuevosDatos, JSON_UNESCAPED_UNICODE), PDO::PARAM_STR);
            $stmtVer->execute();

            $idVersion = $this->lastInsertId();

            // Actualizar estado del ítem a PENDIENTE de nueva revisión
            $sqlUpd = "UPDATE item_hv SET estado_revision = 'PENDIENTE' WHERE id_item_hv = :id_item";
            $stmtUpd = $this->db->prepare($sqlUpd);
            $stmtUpd->bindValue(':id_item', $idItemHv, PDO::PARAM_INT);
            $stmtUpd->execute();

            $this->commit();

            return $this->buscarVersionPorId($idVersion) ?? throw new RuntimeException("Error al recuperar versión subsanada.");
        } catch (\Throwable $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Busca un ítem por su ID.
     */
    public function buscarPorId(int $idItemHv): ?ItemHvVO
    {
        $sql = "SELECT id_item_hv, id_postulacion, id_tipo_item, estado_revision, fecha_registro
                FROM item_hv
                WHERE id_item_hv = :id_item_hv
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_item_hv', $idItemHv, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? ItemHvVO::fromArray($row) : null;
    }

    /**
     * Busca una versión específica por su ID.
     */
    public function buscarVersionPorId(int $idVersionItem): ?VersionItemVO
    {
        $sql = "SELECT id_version_item, id_item_hv, numero_version, datos, creado_en
                FROM version_item
                WHERE id_version_item = :id_version
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_version', $idVersionItem, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? VersionItemVO::fromArray($row) : null;
    }

    /**
     * Obtiene la versión más reciente de un ítem.
     * Satisface: RF-045 (Revisar dato y soporte juntos), RF-052.
     */
    public function obtenerUltimaVersion(int $idItemHv): ?VersionItemVO
    {
        $sql = "SELECT id_version_item, id_item_hv, numero_version, datos, creado_en
                FROM version_item
                WHERE id_item_hv = :id_item
                ORDER BY numero_version DESC
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_item', $idItemHv, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? VersionItemVO::fromArray($row) : null;
    }

    /**
     * Lista todos los ítems registrados en una postulación.
     * Satisface: RF-031, RF-043 (Expediente del candidato).
     */
    public function listarPorPostulacion(int $idPostulacion): array
    {
        $sql = "SELECT id_item_hv, id_postulacion, id_tipo_item, estado_revision, fecha_registro
                FROM item_hv
                WHERE id_postulacion = :id_postulacion
                ORDER BY id_tipo_item ASC, id_item_hv ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_postulacion', $idPostulacion, PDO::PARAM_INT);
        $stmt->execute();

        $lista = [];
        while ($row = $stmt->fetch()) {
            $lista[] = ItemHvVO::fromArray($row);
        }
        return $lista;
    }

    /**
     * Actualiza el dictamen de revisión del ítem (VERIFICADO, OBSERVADO, RECHAZADO).
     * Satisface: RF-046 (Aprobar ítem), RF-047 (Observar o rechazar ítem), RN-010.
     */
    public function actualizarEstadoRevision(int $idItemHv, string $estadoRevision): bool
    {
        $sql = "UPDATE item_hv SET estado_revision = :estado WHERE id_item_hv = :id_item";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':estado', $estadoRevision, PDO::PARAM_STR);
        $stmt->bindValue(':id_item', $idItemHv, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
