<?php

declare(strict_types=1);

namespace Model\DAO;

use Model\VO\CandidatoVO;
use PDO;

/**
 * DAO para la gestión de datos de filiación y contacto del candidato.
 * Satisface: RF-001, RF-014, RF-016, RF-017, HV-002, HV-003, HV-004.
 */
class CandidatoDAO extends BaseDAO
{
    /**
     * Registra los datos personales de un candidato vinculado a su cuenta de usuario.
     * Satisface: RF-001, RF-016 (Identificación personal), RF-017 (Datos de contacto).
     */
    public function crear(CandidatoVO $candidato): CandidatoVO
    {
        $sql = "INSERT INTO candidato (id_usuario, nombres, apellidos, id_tipo_documento, numero_documento, telefono, ciudad, direccion)
                VALUES (:id_usuario, :nombres, :apellidos, :id_tipo_documento, :numero_documento, :telefono, :ciudad, :direccion)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_usuario', $candidato->idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':nombres', $candidato->nombres, PDO::PARAM_STR);
        $stmt->bindValue(':apellidos', $candidato->apellidos, PDO::PARAM_STR);
        $stmt->bindValue(':id_tipo_documento', $candidato->idTipoDocumento, PDO::PARAM_INT);
        $stmt->bindValue(':numero_documento', $candidato->numeroDocumento, PDO::PARAM_STR);
        $stmt->bindValue(':telefono', $candidato->telefono, PDO::PARAM_STR);
        $stmt->bindValue(':ciudad', $candidato->ciudad, PDO::PARAM_STR);
        $stmt->bindValue(':direccion', $candidato->direccion, $candidato->direccion !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->execute();

        $id = $this->lastInsertId();
        return $this->buscarPorId($id) ?? $candidato;
    }

    /**
     * Obtiene el candidato a partir de su ID de usuario de autenticación.
     * Satisface: RF-002, RF-014 (Inicio de postulación por candidato autenticado).
     */
    public function buscarPorUsuarioId(int $idUsuario): ?CandidatoVO
    {
        $sql = "SELECT id_candidato, id_usuario, nombres, apellidos, id_tipo_documento, numero_documento, telefono, ciudad, direccion, creado_en
                FROM candidato
                WHERE id_usuario = :id_usuario
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? CandidatoVO::fromArray($row) : null;
    }

    /**
     * Busca candidato por su identificador primario.
     * Satisface: RF-014, RF-043.
     */
    public function buscarPorId(int $idCandidato): ?CandidatoVO
    {
        $sql = "SELECT id_candidato, id_usuario, nombres, apellidos, id_tipo_documento, numero_documento, telefono, ciudad, direccion, creado_en
                FROM candidato
                WHERE id_candidato = :id_candidato
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_candidato', $idCandidato, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? CandidatoVO::fromArray($row) : null;
    }

    /**
     * Verifica si existe otro candidato registrado con el mismo documento.
     * Satisface: RF-001, RF-016 (Unicidad y validación de identificación).
     */
    public function existeDocumento(int $idTipoDoc, string $numeroDoc, ?int $excluirCandidatoId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM candidato 
                WHERE id_tipo_documento = :id_tipo AND numero_documento = :numero"
                . ($excluirCandidatoId !== null ? " AND id_candidato <> :excluir" : "");

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_tipo', $idTipoDoc, PDO::PARAM_INT);
        $stmt->bindValue(':numero', $numeroDoc, PDO::PARAM_STR);
        if ($excluirCandidatoId !== null) {
            $stmt->bindValue(':excluir', $excluirCandidatoId, PDO::PARAM_INT);
        }
        $stmt->execute();

        return ((int)$stmt->fetchColumn()) > 0;
    }

    /**
     * Actualiza la información de contacto del candidato.
     * Satisface: RF-017 (Actualizar teléfono, ciudad y dirección).
     */
    public function actualizarContacto(int $idCandidato, string $telefono, string $ciudad, ?string $direccion): bool
    {
        $sql = "UPDATE candidato
                SET telefono = :telefono, ciudad = :ciudad, direccion = :direccion
                WHERE id_candidato = :id_candidato";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':telefono', $telefono, PDO::PARAM_STR);
        $stmt->bindValue(':ciudad', $ciudad, PDO::PARAM_STR);
        $stmt->bindValue(':direccion', $direccion, $direccion !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':id_candidato', $idCandidato, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
