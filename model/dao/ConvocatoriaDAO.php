<?php

declare(strict_types=1);

namespace Model\DAO;

use Model\VO\ConvocatoriaVO;
use Model\VO\PerfilCargoVO;
use Model\VO\RequisitoConvocatoriaVO;
use Model\VO\EtapaProcesoVO;
use Model\VO\CriterioEvaluacionVO;
use PDO;
use RuntimeException;

/**
 * DAO para gestión integral de convocatorias, perfiles, requisitos y etapas.
 * Satisface: RF-006 a RF-013, RF-075, RN-002, RN-003, RN-007, RN-013.
 */
class ConvocatoriaDAO extends BaseDAO
{
    /**
     * Crea una convocatoria en estado BORRADOR junto con su perfil de cargo en una sola transacción.
     * Satisface: RF-006 (Crear convocatoria), RF-007 (Definir perfil y funciones), RN-002.
     */
    public function crearConvocatoriaConPerfil(ConvocatoriaVO $conv, PerfilCargoVO $perfil): ConvocatoriaVO
    {
        $this->beginTransaction();
        try {
            $sqlConv = "INSERT INTO convocatoria (codigo, cargo, id_dependencia, id_facultad, id_programa, id_sede, id_periodo,
                                                 vacantes, id_modalidad_contratacion, descripcion, fecha_apertura, fecha_cierre,
                                                 estado, id_creador)
                        VALUES (:codigo, :cargo, :id_dependencia, :id_facultad, :id_programa, :id_sede, :id_periodo,
                                :vacantes, :id_modalidad, :descripcion, :fecha_apertura, :fecha_cierre,
                                'BORRADOR', :id_creador)";

            $stmtConv = $this->db->prepare($sqlConv);
            $stmtConv->bindValue(':codigo', $conv->codigo, PDO::PARAM_STR);
            $stmtConv->bindValue(':cargo', $conv->cargo, PDO::PARAM_STR);
            $stmtConv->bindValue(':id_dependencia', $conv->idDependencia, PDO::PARAM_INT);
            $stmtConv->bindValue(':id_facultad', $conv->idFacultad, $conv->idFacultad !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmtConv->bindValue(':id_programa', $conv->idPrograma, $conv->idPrograma !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmtConv->bindValue(':id_sede', $conv->idSede, $conv->idSede !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmtConv->bindValue(':id_periodo', $conv->idPeriodo, $conv->idPeriodo !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmtConv->bindValue(':vacantes', $conv->vacantes, PDO::PARAM_INT);
            $stmtConv->bindValue(':id_modalidad', $conv->idModalidadContratacion, PDO::PARAM_INT);
            $stmtConv->bindValue(':descripcion', $conv->descripcion, $conv->descripcion !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmtConv->bindValue(':fecha_apertura', $conv->fechaApertura, PDO::PARAM_STR);
            $stmtConv->bindValue(':fecha_cierre', $conv->fechaCierre, PDO::PARAM_STR);
            $stmtConv->bindValue(':id_creador', $conv->idCreador, PDO::PARAM_INT);
            $stmtConv->execute();

            $idConvocatoria = $this->lastInsertId();

            $sqlPerfil = "INSERT INTO perfil_cargo (id_convocatoria, formacion_requerida, experiencia_requerida, competencias, funciones, condiciones)
                          VALUES (:id_convocatoria, :formacion, :experiencia, :competencias, :funciones, :condiciones)";

            $stmtPerfil = $this->db->prepare($sqlPerfil);
            $stmtPerfil->bindValue(':id_convocatoria', $idConvocatoria, PDO::PARAM_INT);
            $stmtPerfil->bindValue(':formacion', $perfil->formacionRequerida, PDO::PARAM_STR);
            $stmtPerfil->bindValue(':experiencia', $perfil->experienciaRequerida, PDO::PARAM_STR);
            $stmtPerfil->bindValue(':competencias', $perfil->competencias, $perfil->competencias !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmtPerfil->bindValue(':funciones', $perfil->funciones, PDO::PARAM_STR);
            $stmtPerfil->bindValue(':condiciones', $perfil->condiciones, $perfil->condiciones !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmtPerfil->execute();

            $this->commit();
            return $this->buscarPorId($idConvocatoria) ?? $conv;
        } catch (\Throwable $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Publica una convocatoria tras verificar la completitud previa requerida.
     * Satisface: RF-011 (Publicar convocatoria), RN-002 (Configuración antes de publicar).
     */
    public function publicar(int $idConvocatoria): bool
    {
        // Validar RN-002: Debe tener perfil, al menos un requisito documental y etapas obligatorias
        $sqlCheck = "SELECT 
            (SELECT COUNT(*) FROM perfil_cargo WHERE id_convocatoria = :id1) AS tiene_perfil,
            (SELECT COUNT(*) FROM requisito_convocatoria WHERE id_convocatoria = :id2) AS tiene_requisitos,
            (SELECT COUNT(*) FROM etapa_proceso WHERE id_convocatoria = :id3 AND obligatoria = 1) AS tiene_etapas";

        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->bindValue(':id1', $idConvocatoria, PDO::PARAM_INT);
        $stmtCheck->bindValue(':id2', $idConvocatoria, PDO::PARAM_INT);
        $stmtCheck->bindValue(':id3', $idConvocatoria, PDO::PARAM_INT);
        $stmtCheck->execute();
        $check = $stmtCheck->fetch();

        if (empty($check['tiene_perfil']) || empty($check['tiene_requisitos']) || empty($check['tiene_etapas'])) {
            throw new RuntimeException("No se puede publicar: la convocatoria debe tener perfil, requisitos documentales y etapas completas (RN-002).");
        }

        $sql = "UPDATE convocatoria
                SET estado = 'PUBLICADA', fecha_publicacion = NOW()
                WHERE id_convocatoria = :id_convocatoria AND estado = 'BORRADOR'";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_convocatoria', $idConvocatoria, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Retira una convocatoria registrando formalmente el motivo.
     * Satisface: RF-011 (Retirar convocatoria conservando trazabilidad).
     */
    public function retirar(int $idConvocatoria, string $motivoRetiro): bool
    {
        $sql = "UPDATE convocatoria
                SET estado = 'RETIRADA', motivo_retiro = :motivo
                WHERE id_convocatoria = :id_convocatoria AND estado IN ('BORRADOR','PUBLICADA')";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':motivo', $motivoRetiro, PDO::PARAM_STR);
        $stmt->bindValue(':id_convocatoria', $idConvocatoria, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Cierra una convocatoria al finalizar todas las etapas.
     * Satisface: RF-075 (Cerrar convocatoria).
     */
    public function cerrar(int $idConvocatoria): bool
    {
        $sql = "UPDATE convocatoria SET estado = 'CERRADA' WHERE id_convocatoria = :id_convocatoria";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_convocatoria', $idConvocatoria, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Consulta una convocatoria por su identificador.
     * Satisface: RF-006, RF-013.
     */
    public function buscarPorId(int $idConvocatoria): ?ConvocatoriaVO
    {
        $sql = "SELECT id_convocatoria, codigo, cargo, id_dependencia, id_facultad, id_programa, id_sede, id_periodo,
                       vacantes, id_modalidad_contratacion, descripcion, fecha_apertura, fecha_cierre, estado,
                       fecha_publicacion, motivo_retiro, id_creador, creado_en
                FROM convocatoria
                WHERE id_convocatoria = :id_convocatoria
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_convocatoria', $idConvocatoria, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? ConvocatoriaVO::fromArray($row) : null;
    }

    /**
     * Lista convocatorias públicas abiertas para consulta por aspirantes.
     * Satisface: RF-013 (Consultar convocatorias disponibles), RN-003 (Control de periodo de vigencia).
     */
    public function listarPublicadasVigentes(): array
    {
        $sql = "SELECT id_convocatoria, codigo, cargo, id_dependencia, id_facultad, id_programa, id_sede, id_periodo,
                       vacantes, id_modalidad_contratacion, descripcion, fecha_apertura, fecha_cierre, estado,
                       fecha_publicacion, motivo_retiro, id_creador, creado_en
                FROM convocatoria
                WHERE estado = 'PUBLICADA'
                  AND NOW() BETWEEN fecha_apertura AND fecha_cierre
                ORDER BY fecha_apertura DESC";

        $stmt = $this->db->query($sql);
        $lista = [];
        while ($row = $stmt->fetch()) {
            $lista[] = ConvocatoriaVO::fromArray($row);
        }
        return $lista;
    }

    /**
     * Obtiene el perfil de cargo asociado a una convocatoria.
     * Satisface: RF-007, RF-013.
     */
    public function obtenerPerfil(int $idConvocatoria): ?PerfilCargoVO
    {
        $sql = "SELECT id_perfil, id_convocatoria, formacion_requerida, experiencia_requerida, competencias, funciones, condiciones
                FROM perfil_cargo
                WHERE id_convocatoria = :id_convocatoria
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_convocatoria', $idConvocatoria, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? PerfilCargoVO::fromArray($row) : null;
    }

    /**
     * Obtiene los requisitos documentales configurados para la convocatoria.
     * Satisface: RF-008 (Configurar requisitos documentales), RF-033, RN-007.
     */
    public function obtenerRequisitos(int $idConvocatoria): array
    {
        $sql = "SELECT id_requisito, id_convocatoria, id_tipo_item, obligatoriedad, soporte_requerido, regla
                FROM requisito_convocatoria
                WHERE id_convocatoria = :id_convocatoria
                ORDER BY id_requisito ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_convocatoria', $idConvocatoria, PDO::PARAM_INT);
        $stmt->execute();

        $lista = [];
        while ($row = $stmt->fetch()) {
            $lista[] = RequisitoConvocatoriaVO::fromArray($row);
        }
        return $lista;
    }

    /**
     * Obtiene las etapas del proceso configuradas para la convocatoria.
     * Satisface: RF-010 (Configurar etapas del proceso), RF-060.
     */
    public function obtenerEtapas(int $idConvocatoria): array
    {
        $sql = "SELECT id_etapa, id_convocatoria, nombre, tipo, orden, obligatoria, estado
                FROM etapa_proceso
                WHERE id_convocatoria = :id_convocatoria
                ORDER BY orden ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_convocatoria', $idConvocatoria, PDO::PARAM_INT);
        $stmt->execute();

        $lista = [];
        while ($row = $stmt->fetch()) {
            $lista[] = EtapaProcesoVO::fromArray($row);
        }
        return $lista;
    }

    /**
     * Obtiene los criterios y rúbricas de evaluación configurados.
     * Satisface: RF-009 (Definir criterios de evaluación), RF-055, RN-013.
     */
    public function obtenerCriterios(int $idConvocatoria): array
    {
        $sql = "SELECT id_criterio, id_convocatoria, id_etapa, nombre, descripcion, puntaje_maximo, ponderacion, obligatorio, bloqueado
                FROM criterio_evaluacion
                WHERE id_convocatoria = :id_convocatoria
                ORDER BY id_criterio ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_convocatoria', $idConvocatoria, PDO::PARAM_INT);
        $stmt->execute();

        $lista = [];
        while ($row = $stmt->fetch()) {
            $lista[] = CriterioEvaluacionVO::fromArray($row);
        }
        return $lista;
    }
}
