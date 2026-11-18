<?php

declare(strict_types=1);

namespace Model\DAO;

use Model\VO\PostulacionVO;
use PDO;
use RuntimeException;

/**
 * DAO para la gestión del ciclo de vida y máquina de estados de las postulaciones.
 * Satisface: RF-014, RF-038, RF-039, RF-041, RF-042, RF-043, RF-044, RF-053, RF-057, RF-058, RF-072, RF-073.
 * Aplica Reglas: RN-001, RN-003, RN-008, RN-009, RN-012, RN-015, RN-024.
 */
class PostulacionDAO extends BaseDAO
{
    /**
     * Inicia una postulación en estado BORRADOR garantizando unicidad activa.
     * Satisface: RF-014 (Iniciar postulación), RN-001 (Una postulación activa por convocatoria).
     */
    public function iniciarPostulacion(int $idCandidato, int $idConvocatoria): PostulacionVO
    {
        // RN-001: Verificar si ya existe postulación previa activa
        $existente = $this->buscarPorCandidatoYConvocatoria($idCandidato, $idConvocatoria);
        if ($existente !== null && $existente->estado !== 'RETIRADA') {
            throw new RuntimeException("El candidato ya tiene una postulación activa en esta convocatoria (RN-001).");
        }

        $sql = "INSERT INTO postulacion (id_candidato, id_convocatoria, estado, resultado_documental, version, fecha_inicio)
                VALUES (:id_candidato, :id_convocatoria, 'BORRADOR', 'PENDIENTE', 1, NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_candidato', $idCandidato, PDO::PARAM_INT);
        $stmt->bindValue(':id_convocatoria', $idConvocatoria, PDO::PARAM_INT);
        $stmt->execute();

        $id = $this->lastInsertId();
        return $this->buscarPorId($id) ?? throw new RuntimeException("Error al recuperar la postulación creada.");
    }

    /**
     * Busca la postulación por su identificador primario.
     * Satisface: RF-014, RF-043.
     */
    public function buscarPorId(int $idPostulacion): ?PostulacionVO
    {
        $sql = "SELECT id_postulacion, id_candidato, id_convocatoria, id_etapa_actual, estado,
                       resultado_documental, version, fecha_inicio, fecha_envio
                FROM postulacion
                WHERE id_postulacion = :id_postulacion
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_postulacion', $idPostulacion, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? PostulacionVO::fromArray($row) : null;
    }

    /**
     * Busca la postulación vinculada a un candidato y una convocatoria específica.
     * Satisface: RF-014, RN-001.
     */
    public function buscarPorCandidatoYConvocatoria(int $idCandidato, int $idConvocatoria): ?PostulacionVO
    {
        $sql = "SELECT id_postulacion, id_candidato, id_convocatoria, id_etapa_actual, estado,
                       resultado_documental, version, fecha_inicio, fecha_envio
                FROM postulacion
                WHERE id_candidato = :id_candidato AND id_convocatoria = :id_convocatoria
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_candidato', $idCandidato, PDO::PARAM_INT);
        $stmt->bindValue(':id_convocatoria', $idConvocatoria, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? PostulacionVO::fromArray($row) : null;
    }

    /**
     * Realiza el envío definitivo de la postulación ejecutando la lógica transaccional segura de MySQL.
     * Satisface: RF-038 (Validar requisitos), RF-039 (Enviar definitiva), RF-041 (Bloquear cambios), RN-008.
     */
    public function enviarPostulacionDefinitiva(int $idPostulacion, int $idUsuario): bool
    {
        // Se ejecuta el procedimiento almacenado transaccional que valida consentimientos, soportes y fechas
        $sql = "CALL sp_cambiar_estado_postulacion(:postulacion, 'ENVIADA', :usuario, 'Envío formal definitivo por el candidato', NULL, NULL, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':postulacion', $idPostulacion, PDO::PARAM_INT);
        $stmt->bindValue(':usuario', $idUsuario, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Ejecuta una transición de estado de la postulación usando el procedimiento transaccional.
     * Satisface: RF-057 (Preselección), RF-058 (Descarte con motivo), RF-072 (Avanzar etapa), RF-073 (Decisión final), RN-012, RN-015, RN-024.
     */
    public function cambiarEstado(
        int $idPostulacion,
        string $nuevoEstado,
        int $idUsuario,
        string $detalle,
        ?int $idMotivo = null,
        ?int $idEtapa = null
    ): bool {
        $sql = "CALL sp_cambiar_estado_postulacion(:postulacion, :nuevo, :usuario, :detalle, :motivo, :etapa, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':postulacion', $idPostulacion, PDO::PARAM_INT);
        $stmt->bindValue(':nuevo', $nuevoEstado, PDO::PARAM_STR);
        $stmt->bindValue(':usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':detalle', $detalle, PDO::PARAM_STR);
        $stmt->bindValue(':motivo', $idMotivo, $idMotivo !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':etapa', $idEtapa, $idEtapa !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        return $stmt->execute();
    }

    /**
     * Recalcula el estado documental consolidado (CONFORME, OBSERVADO, NO_CONFORME, PENDIENTE).
     * Satisface: RF-053 (Calcular estado documental), RN-010, RN-012.
     */
    public function recalcularEstadoDocumental(int $idPostulacion): bool
    {
        $sql = "CALL sp_recalcular_estado_documental(:postulacion)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':postulacion', $idPostulacion, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Permite al candidato retirar voluntariamente su postulación antes de la decisión final.
     * Satisface: RF-042 (Retirar postulación), RN-001.
     */
    public function retirarPostulacion(int $idPostulacion, int $idUsuario, string $motivo): bool
    {
        return $this->cambiarEstado($idPostulacion, 'RETIRADA', $idUsuario, "Retiro voluntario: " . $motivo);
    }

    /**
     * Lista postulaciones de una convocatoria con soporte de filtrado por estado.
     * Satisface: RF-043 (Bandeja de postulaciones), RF-044 (Buscar y filtrar candidatos), RF-078.
     */
    public function listarPorConvocatoria(int $idConvocatoria, ?string $estado = null): array
    {
        $sql = "SELECT id_postulacion, id_candidato, id_convocatoria, id_etapa_actual, estado,
                       resultado_documental, version, fecha_inicio, fecha_envio
                FROM postulacion
                WHERE id_convocatoria = :id_convocatoria"
                . ($estado !== null ? " AND estado = :estado" : "") . "
                ORDER BY fecha_inicio DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_convocatoria', $idConvocatoria, PDO::PARAM_INT);
        if ($estado !== null) {
            $stmt->bindValue(':estado', $estado, PDO::PARAM_STR);
        }
        $stmt->execute();

        $lista = [];
        while ($row = $stmt->fetch()) {
            $lista[] = PostulacionVO::fromArray($row);
        }
        return $lista;
    }

    /**
     * Lista todas las postulaciones realizadas por un candidato determinado.
     * Satisface: RF-014, RF-040, RF-085.
     */
    public function listarPorCandidato(int $idCandidato): array
    {
        $sql = "SELECT id_postulacion, id_candidato, id_convocatoria, id_etapa_actual, estado,
                       resultado_documental, version, fecha_inicio, fecha_envio
                FROM postulacion
                WHERE id_candidato = :id_candidato
                ORDER BY fecha_inicio DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_candidato', $idCandidato, PDO::PARAM_INT);
        $stmt->execute();

        $lista = [];
        while ($row = $stmt->fetch()) {
            $lista[] = PostulacionVO::fromArray($row);
        }
        return $lista;
    }
}
