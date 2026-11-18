<?php

declare(strict_types=1);

namespace Model\DAO;

use Model\VO\EntrevistaVO;
use Model\VO\FranjaEntrevistaVO;
use Model\VO\EntrevistadorVO;
use Model\VO\ResultadoEntrevistaVO;
use PDO;
use RuntimeException;

/**
 * DAO para disponibilidad, programación, comités y calificación de entrevistas.
 * Satisface: RF-060 a RF-071, RF-090, RN-016, RN-017, RN-018.
 */
class EntrevistaDAO extends BaseDAO
{
    /**
     * Registra una franja de disponibilidad para entrevistas.
     * Satisface: RF-061 (Crear disponibilidad de entrevistas), RN-017.
     */
    public function crearFranja(FranjaEntrevistaVO $franja): FranjaEntrevistaVO
    {
        $sql = "INSERT INTO franja_entrevista (id_convocatoria, id_etapa, inicio, fin, cupos, modalidad, recurso)
                VALUES (:id_convocatoria, :id_etapa, :inicio, :fin, :cupos, :modalidad, :recurso)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_convocatoria', $franja->idConvocatoria, PDO::PARAM_INT);
        $stmt->bindValue(':id_etapa', $franja->idEtapa, PDO::PARAM_INT);
        $stmt->bindValue(':inicio', $franja->inicio, PDO::PARAM_STR);
        $stmt->bindValue(':fin', $franja->fin, PDO::PARAM_STR);
        $stmt->bindValue(':cupos', $franja->cupos, PDO::PARAM_INT);
        $stmt->bindValue(':modalidad', $franja->modalidad, PDO::PARAM_STR);
        $stmt->bindValue(':recurso', $franja->recurso, $franja->recurso !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->execute();

        $id = $this->lastInsertId();
        return $this->buscarFranjaPorId($id) ?? $franja;
    }

    /**
     * Busca una franja por ID.
     */
    public function buscarFranjaPorId(int $idFranja): ?FranjaEntrevistaVO
    {
        $sql = "SELECT id_franja, id_convocatoria, id_etapa, inicio, fin, cupos, modalidad, recurso
                FROM franja_entrevista
                WHERE id_franja = :id_franja
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_franja', $idFranja, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? FranjaEntrevistaVO::fromArray($row) : null;
    }

    /**
     * Programa una entrevista presencial o virtual validando traslapes de agenda e insertando su comité en transacción.
     * Satisface: RF-062 (Asignar entrevistadores), RF-063 (Programar entrevista), RF-064 (Evitar conflictos), RF-065 (Lugar o enlace), RN-016, RN-017.
     *
     * @param EntrevistaVO $entrevista Datos de la cita
     * @param array<array{id_usuario: int, rol_comite?: string}> $comite Lista de integrantes asignados
     */
    public function programarEntrevista(EntrevistaVO $entrevista, array $comite): EntrevistaVO
    {
        $this->beginTransaction();
        try {
            // RF-064 / RN-017: Validar que no haya conflictos de agenda mediante procedimiento transaccional
            $sqlVal = "CALL sp_validar_agenda(NULL, :postulacion, :inicio, :fin, :modalidad, :ubicacion)";
            $stmtVal = $this->db->prepare($sqlVal);
            $stmtVal->bindValue(':postulacion', $entrevista->idPostulacion, PDO::PARAM_INT);
            $stmtVal->bindValue(':inicio', $entrevista->inicio, PDO::PARAM_STR);
            $stmtVal->bindValue(':fin', $entrevista->fin, PDO::PARAM_STR);
            $stmtVal->bindValue(':modalidad', $entrevista->modalidad, PDO::PARAM_STR);
            $stmtVal->bindValue(':ubicacion', $entrevista->ubicacion, $entrevista->ubicacion !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmtVal->execute();
            $stmtVal->closeCursor(); // Liberar cursor para permitir el siguiente statement

            $sqlEnt = "INSERT INTO entrevista (id_postulacion, id_etapa, id_franja, inicio, fin, modalidad, ubicacion, enlace_virtual, estado, confirmacion_candidato)
                       VALUES (:id_postulacion, :id_etapa, :id_franja, :inicio, :fin, :modalidad, :ubicacion, :enlace_virtual, 'PROGRAMADA', 'PENDIENTE')";

            $stmtEnt = $this->db->prepare($sqlEnt);
            $stmtEnt->bindValue(':id_postulacion', $entrevista->idPostulacion, PDO::PARAM_INT);
            $stmtEnt->bindValue(':id_etapa', $entrevista->idEtapa, PDO::PARAM_INT);
            $stmtEnt->bindValue(':id_franja', $entrevista->idFranja, $entrevista->idFranja !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmtEnt->bindValue(':inicio', $entrevista->inicio, PDO::PARAM_STR);
            $stmtEnt->bindValue(':fin', $entrevista->fin, PDO::PARAM_STR);
            $stmtEnt->bindValue(':modalidad', $entrevista->modalidad, PDO::PARAM_STR);
            $stmtEnt->bindValue(':ubicacion', $entrevista->ubicacion, $entrevista->ubicacion !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmtEnt->bindValue(':enlace_virtual', $entrevista->enlaceVirtual, $entrevista->enlaceVirtual !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmtEnt->execute();

            $idEntrevista = $this->lastInsertId();

            // RF-062: Asignar comité
            $sqlCom = "INSERT INTO entrevistador (id_entrevista, id_usuario, rol_comite, estado)
                       VALUES (:id_entrevista, :id_usuario, :rol_comite, 'ASIGNADO')";
            $stmtCom = $this->db->prepare($sqlCom);

            foreach ($comite as $miembro) {
                $rol = $miembro['rol_comite'] ?? 'MIEMBRO';
                $stmtCom->bindValue(':id_entrevista', $idEntrevista, PDO::PARAM_INT);
                $stmtCom->bindValue(':id_usuario', $miembro['id_usuario'], PDO::PARAM_INT);
                $stmtCom->bindValue(':rol_comite', $rol, PDO::PARAM_STR);
                $stmtCom->execute();
            }

            $this->commit();
            return $this->buscarPorId($idEntrevista) ?? $entrevista;
        } catch (\Throwable $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Busca la entrevista por su ID.
     * Satisface: RF-063, RF-066.
     */
    public function buscarPorId(int $idEntrevista): ?EntrevistaVO
    {
        $sql = "SELECT id_entrevista, id_postulacion, id_etapa, id_franja, inicio, fin, modalidad,
                       ubicacion, enlace_virtual, estado, confirmacion_candidato, reprogramaciones
                FROM entrevista
                WHERE id_entrevista = :id_entrevista
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_entrevista', $idEntrevista, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? EntrevistaVO::fromArray($row) : null;
    }

    /**
     * Busca la entrevista programada para una postulación.
     * Satisface: RF-063, RF-067, RF-070.
     */
    public function buscarPorPostulacion(int $idPostulacion): ?EntrevistaVO
    {
        $sql = "SELECT id_entrevista, id_postulacion, id_etapa, id_franja, inicio, fin, modalidad,
                       ubicacion, enlace_virtual, estado, confirmacion_candidato, reprogramaciones
                FROM entrevista
                WHERE id_postulacion = :id_postulacion
                ORDER BY id_entrevista DESC
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_postulacion', $idPostulacion, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? EntrevistaVO::fromArray($row) : null;
    }

    /**
     * Permite al candidato confirmar su asistencia o solicitar reprogramación.
     * Satisface: RF-067 (Confirmar asistencia o solicitar reprogramación).
     */
    public function actualizarConfirmacionCandidato(int $idEntrevista, string $confirmacion): bool
    {
        $sql = "UPDATE entrevista SET confirmacion_candidato = :confirmacion WHERE id_entrevista = :id_entrevista";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':confirmacion', $confirmacion, PDO::PARAM_STR);
        $stmt->bindValue(':id_entrevista', $idEntrevista, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Registra la evaluación individual o consolidada de una entrevista.
     * Satisface: RF-070 (Registrar evaluación individual), RF-071 (Consolidar resultado), RF-090, RN-018.
     */
    public function registrarResultado(ResultadoEntrevistaVO $res): ResultadoEntrevistaVO
    {
        $sql = "INSERT INTO resultado_entrevista (id_entrevista, id_evaluador, tipo, puntaje, concepto, observacion)
                VALUES (:id_entrevista, :id_evaluador, :tipo, :puntaje, :concepto, :observacion)
                ON DUPLICATE KEY UPDATE puntaje = :puntaje_upd, concepto = :concepto_upd, observacion = :obs_upd, fecha = NOW()";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_entrevista', $res->idEntrevista, PDO::PARAM_INT);
        $stmt->bindValue(':id_evaluador', $res->idEvaluador, $res->idEvaluador !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':tipo', $res->tipo, PDO::PARAM_STR);
        $stmt->bindValue(':puntaje', $res->puntaje, $res->puntaje !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':concepto', $res->concepto, PDO::PARAM_STR);
        $stmt->bindValue(':observacion', $res->observacion, $res->observacion !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':puntaje_upd', $res->puntaje, $res->puntaje !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':concepto_upd', $res->concepto, PDO::PARAM_STR);
        $stmt->bindValue(':obs_upd', $res->observacion, $res->observacion !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->execute();

        $id = $this->lastInsertId() ?: ($res->idResultado ?? 0);
        return $this->buscarResultadoPorId($id) ?? $res;
    }

    /**
     * Busca un resultado de entrevista por su ID.
     */
    public function buscarResultadoPorId(int $idResultado): ?ResultadoEntrevistaVO
    {
        $sql = "SELECT id_resultado, id_entrevista, id_evaluador, tipo, puntaje, concepto, observacion, fecha
                FROM resultado_entrevista
                WHERE id_resultado = :id_resultado
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_resultado', $idResultado, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? ResultadoEntrevistaVO::fromArray($row) : null;
    }

    /**
     * Obtiene los resultados de entrevista (individuales y consolidados).
     * Satisface: RF-071, RN-018.
     */
    public function listarResultadosPorEntrevista(int $idEntrevista): array
    {
        $sql = "SELECT id_resultado, id_entrevista, id_evaluador, tipo, puntaje, concepto, observacion, fecha
                FROM resultado_entrevista
                WHERE id_entrevista = :id_entrevista
                ORDER BY tipo DESC, id_resultado ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_entrevista', $idEntrevista, PDO::PARAM_INT);
        $stmt->execute();

        $lista = [];
        while ($row = $stmt->fetch()) {
            $lista[] = ResultadoEntrevistaVO::fromArray($row);
        }
        return $lista;
    }
}
