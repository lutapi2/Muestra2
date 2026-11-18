<?php

declare(strict_types=1);

namespace Model\DAO;

use Model\VO\EvaluacionVO;
use Model\VO\PruebaVO;
use Model\VO\ResultadoPruebaVO;
use Model\VO\PruebaAulaVO;
use Model\VO\ResultadoEtapaVO;
use PDO;

/**
 * DAO para evaluaciones de perfil, pruebas psicotécnicas/conocimiento, pruebas de aula y dictamen de etapa.
 * Satisface: RF-054, RF-055, RF-087, RF-088, RF-089, RF-093, RN-013, RN-014, RN-024.
 */
class EvaluacionDAO extends BaseDAO
{
    /**
     * Registra la valoración de un aspirante frente a un criterio de evaluación.
     * Satisface: RF-054 (Verificar perfil), RF-055 (Matriz de evaluación ponderada), RN-013, RN-014.
     */
    public function registrarEvaluacion(EvaluacionVO $eval): EvaluacionVO
    {
        $sql = "INSERT INTO evaluacion (id_postulacion, id_criterio, id_evaluador, resultado, puntaje, evidencia, observacion)
                VALUES (:id_postulacion, :id_criterio, :id_evaluador, :resultado, :puntaje, :evidencia, :observacion)
                ON DUPLICATE KEY UPDATE resultado = :resultado_upd, puntaje = :puntaje_upd, observacion = :obs_upd, fecha_evaluacion = NOW()";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_postulacion', $eval->idPostulacion, PDO::PARAM_INT);
        $stmt->bindValue(':id_criterio', $eval->idCriterio, PDO::PARAM_INT);
        $stmt->bindValue(':id_evaluador', $eval->idEvaluador, PDO::PARAM_INT);
        $stmt->bindValue(':resultado', $eval->resultado, PDO::PARAM_STR);
        $stmt->bindValue(':puntaje', $eval->puntaje, $eval->puntaje !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':evidencia', $eval->evidencia, $eval->evidencia !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':observacion', $eval->observacion, $eval->observacion !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':resultado_upd', $eval->resultado, PDO::PARAM_STR);
        $stmt->bindValue(':puntaje_upd', $eval->puntaje, $eval->puntaje !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':obs_upd', $eval->observacion, $eval->observacion !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->execute();

        $id = $this->lastInsertId() ?: ($eval->idEvaluacion ?? 0);
        return $this->buscarEvaluacionPorId($id) ?? $eval;
    }

    /**
     * Busca evaluación por ID.
     */
    public function buscarEvaluacionPorId(int $idEvaluacion): ?EvaluacionVO
    {
        $sql = "SELECT id_evaluacion, id_postulacion, id_criterio, id_evaluador, resultado, puntaje,
                       evidencia, observacion, fecha_evaluacion
                FROM evaluacion
                WHERE id_evaluacion = :id_evaluacion
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_evaluacion', $idEvaluacion, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? EvaluacionVO::fromArray($row) : null;
    }

    /**
     * Lista las evaluaciones asignadas a una postulación.
     * Satisface: RF-055.
     */
    public function listarPorPostulacion(int $idPostulacion): array
    {
        $sql = "SELECT id_evaluacion, id_postulacion, id_criterio, id_evaluador, resultado, puntaje,
                       evidencia, observacion, fecha_evaluacion
                FROM evaluacion
                WHERE id_postulacion = :id_postulacion
                ORDER BY id_criterio ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_postulacion', $idPostulacion, PDO::PARAM_INT);
        $stmt->execute();

        $lista = [];
        while ($row = $stmt->fetch()) {
            $lista[] = EvaluacionVO::fromArray($row);
        }
        return $lista;
    }

    /**
     * Registra el resultado formal de una prueba psicotécnica o de conocimiento.
     * Satisface: RF-087, RF-088.
     */
    public function registrarResultadoPrueba(ResultadoPruebaVO $rp): ResultadoPruebaVO
    {
        $sql = "INSERT INTO resultado_prueba (id_prueba, id_postulacion, calificacion, dictamen, observacion, soporte_evidencia)
                VALUES (:id_prueba, :id_postulacion, :calificacion, :dictamen, :observacion, :soporte)
                ON DUPLICATE KEY UPDATE calificacion = :calif_upd, dictamen = :dictamen_upd, observacion = :obs_upd, soporte_evidencia = :soporte_upd";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_prueba', $rp->idPrueba, PDO::PARAM_INT);
        $stmt->bindValue(':id_postulacion', $rp->idPostulacion, PDO::PARAM_INT);
        $stmt->bindValue(':calificacion', $rp->calificacion, $rp->calificacion !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':dictamen', $rp->dictamen, PDO::PARAM_STR);
        $stmt->bindValue(':observacion', $rp->observacion, $rp->observacion !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':soporte', $rp->soporteEvidencia, $rp->soporteEvidencia !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':calif_upd', $rp->calificacion, $rp->calificacion !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':dictamen_upd', $rp->dictamen, PDO::PARAM_STR);
        $stmt->bindValue(':obs_upd', $rp->observacion, $rp->observacion !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':soporte_upd', $rp->soporteEvidencia, $rp->soporteEvidencia !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->execute();

        $id = $this->lastInsertId() ?: ($rp->idResultadoPrueba ?? 0);
        return $this->buscarResultadoPruebaPorId($id) ?? $rp;
    }

    /**
     * Busca resultado de prueba por ID.
     */
    public function buscarResultadoPruebaPorId(int $id): ?ResultadoPruebaVO
    {
        $sql = "SELECT id_resultado_prueba, id_prueba, id_postulacion, calificacion, dictamen, observacion, soporte_evidencia, fecha_registro
                FROM resultado_prueba WHERE id_resultado_prueba = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? ResultadoPruebaVO::fromArray($row) : null;
    }

    /**
     * Registra la evaluación de la prueba de aula docente por Desarrollo Profesoral.
     * Satisface: RF-089 (Gestionar pruebas de aula docente).
     */
    public function registrarPruebaAula(PruebaAulaVO $pa): PruebaAulaVO
    {
        $sql = "INSERT INTO prueba_aula (id_postulacion, tema, rubrica, id_evaluador, puntaje, dictamen, observaciones, soporte_acta)
                VALUES (:id_postulacion, :tema, :rubrica, :id_evaluador, :puntaje, :dictamen, :observaciones, :soporte)
                ON DUPLICATE KEY UPDATE puntaje = :puntaje_upd, dictamen = :dictamen_upd, observaciones = :obs_upd";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_postulacion', $pa->idPostulacion, PDO::PARAM_INT);
        $stmt->bindValue(':tema', $pa->tema, PDO::PARAM_STR);
        $stmt->bindValue(':rubrica', $pa->rubrica, PDO::PARAM_STR);
        $stmt->bindValue(':id_evaluador', $pa->idEvaluador, PDO::PARAM_INT);
        $stmt->bindValue(':puntaje', $pa->puntaje, PDO::PARAM_STR);
        $stmt->bindValue(':dictamen', $pa->dictamen, PDO::PARAM_STR);
        $stmt->bindValue(':observaciones', $pa->observaciones, $pa->observaciones !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':soporte', $pa->soporteActa, $pa->soporteActa !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':puntaje_upd', $pa->puntaje, PDO::PARAM_STR);
        $stmt->bindValue(':dictamen_upd', $pa->dictamen, PDO::PARAM_STR);
        $stmt->bindValue(':obs_upd', $pa->observaciones, $pa->observaciones !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->execute();

        $id = $this->lastInsertId() ?: ($pa->idPruebaAula ?? 0);
        return $this->buscarPruebaAulaPorId($id) ?? $pa;
    }

    /**
     * Busca prueba de aula por ID.
     */
    public function buscarPruebaAulaPorId(int $id): ?PruebaAulaVO
    {
        $sql = "SELECT id_prueba_aula, id_postulacion, tema, rubrica, id_evaluador, puntaje, dictamen, observaciones, soporte_acta, fecha_evaluacion
                FROM prueba_aula WHERE id_prueba_aula = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? PruebaAulaVO::fromArray($row) : null;
    }

    /**
     * Registra el dictamen formal obligatorio de cierre de una etapa.
     * Satisface: RF-093 (Resultado obligatorio por etapa y causal de descarte), RN-024.
     */
    public function registrarResultadoEtapa(ResultadoEtapaVO $re): ResultadoEtapaVO
    {
        $sql = "INSERT INTO resultado_etapa (id_postulacion, id_etapa, dictamen, observacion, id_motivo_descarte, id_responsable)
                VALUES (:id_postulacion, :id_etapa, :dictamen, :observacion, :id_motivo, :id_responsable)
                ON DUPLICATE KEY UPDATE dictamen = :dict_upd, observacion = :obs_upd, id_motivo_descarte = :mot_upd";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_postulacion', $re->idPostulacion, PDO::PARAM_INT);
        $stmt->bindValue(':id_etapa', $re->idEtapa, PDO::PARAM_INT);
        $stmt->bindValue(':dictamen', $re->dictamen, PDO::PARAM_STR);
        $stmt->bindValue(':observacion', $re->observacion, PDO::PARAM_STR);
        $stmt->bindValue(':id_motivo', $re->idMotivoDescarte, $re->idMotivoDescarte !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':id_responsable', $re->idResponsable, PDO::PARAM_INT);
        $stmt->bindValue(':dict_upd', $re->dictamen, PDO::PARAM_STR);
        $stmt->bindValue(':obs_upd', $re->observacion, PDO::PARAM_STR);
        $stmt->bindValue(':mot_upd', $re->idMotivoDescarte, $re->idMotivoDescarte !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->execute();

        $id = $this->lastInsertId() ?: ($re->idResultadoEtapa ?? 0);
        return $this->buscarResultadoEtapaPorId($id) ?? $re;
    }

    /**
     * Busca resultado de etapa por ID.
     */
    public function buscarResultadoEtapaPorId(int $id): ?ResultadoEtapaVO
    {
        $sql = "SELECT id_resultado_etapa, id_postulacion, id_etapa, dictamen, observacion, id_motivo_descarte, id_responsable, fecha_registro
                FROM resultado_etapa WHERE id_resultado_etapa = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? ResultadoEtapaVO::fromArray($row) : null;
    }
}
