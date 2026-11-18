<?php

declare(strict_types=1);

namespace Model\DAO;

use Model\VO\UsuarioVO;
use PDO;

/**
 * DAO para la gestión de usuarios, autenticación y roles de seguridad RBAC.
 * Satisface: RF-001, RF-002, RF-003, RF-004, RF-005, RNF-009, RNF-012.
 */
class UsuarioDAO extends BaseDAO
{
    /**
     * Registra un nuevo usuario en el sistema.
     * Satisface: RF-001 (Registrar candidato), RF-005 (Gestionar usuarios internos), RNF-009 (Hash seguro).
     */
    public function crear(UsuarioVO $usuario): UsuarioVO
    {
        $sql = "INSERT INTO usuario (correo, contrasena_hash, tipo, nombre_completo, id_dependencia, estado)
                VALUES (:correo, :contrasena_hash, :tipo, :nombre_completo, :id_dependencia, :estado)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':correo', $usuario->correo, PDO::PARAM_STR);
        $stmt->bindValue(':contrasena_hash', $usuario->contrasenaHash, PDO::PARAM_STR);
        $stmt->bindValue(':tipo', $usuario->tipo, PDO::PARAM_STR);
        $stmt->bindValue(':nombre_completo', $usuario->nombreCompleto, PDO::PARAM_STR);
        $stmt->bindValue(':id_dependencia', $usuario->idDependencia, $usuario->idDependencia !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':estado', $usuario->estado, PDO::PARAM_STR);
        $stmt->execute();

        $id = $this->lastInsertId();
        return $this->buscarPorId($id) ?? $usuario;
    }

    /**
     * Busca un usuario por su correo electrónico único.
     * Satisface: RF-001 (Validación de unicidad), RF-002 (Inicio de sesión candidato), RF-003 (Autenticación interna).
     */
    public function buscarPorCorreo(string $correo): ?UsuarioVO
    {
        $sql = "SELECT id_usuario, correo, contrasena_hash, tipo, nombre_completo, id_dependencia, estado, ultimo_acceso, creado_en
                FROM usuario
                WHERE correo = :correo
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? UsuarioVO::fromArray($row) : null;
    }

    /**
     * Busca un usuario por su identificador primario.
     * Satisface: RF-003 (Autenticación), RF-005 (Gestión de usuarios).
     */
    public function buscarPorId(int $idUsuario): ?UsuarioVO
    {
        $sql = "SELECT id_usuario, correo, contrasena_hash, tipo, nombre_completo, id_dependencia, estado, ultimo_acceso, creado_en
                FROM usuario
                WHERE id_usuario = :id_usuario
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ? UsuarioVO::fromArray($row) : null;
    }

    /**
     * Actualiza la fecha y hora de último acceso tras autenticación exitosa.
     * Satisface: RF-002, RF-003, RNF-012 (Control de sesiones).
     */
    public function actualizarUltimoAcceso(int $idUsuario): bool
    {
        $sql = "UPDATE usuario SET ultimo_acceso = NOW() WHERE id_usuario = :id_usuario";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Cambia el estado de una cuenta (ACTIVO, INACTIVO, BLOQUEADO).
     * Satisface: RF-005 (Desactivación de cuentas).
     */
    public function cambiarEstado(int $idUsuario, string $nuevoEstado): bool
    {
        $sql = "UPDATE usuario SET estado = :estado WHERE id_usuario = :id_usuario";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':estado', $nuevoEstado, PDO::PARAM_STR);
        $stmt->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Asigna un rol a un usuario determinado.
     * Satisface: RF-004, RF-005 (Asignación de roles y permisos).
     */
    public function asignarRol(int $idUsuario, int $idRol, ?int $asignadoPor = null): bool
    {
        $sql = "INSERT INTO usuario_rol (id_usuario, id_rol, asignado_por)
                VALUES (:id_usuario, :id_rol, :asignado_por)
                ON DUPLICATE KEY UPDATE asignado_en = CURRENT_TIMESTAMP";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':id_rol', $idRol, PDO::PARAM_INT);
        $stmt->bindValue(':asignado_por', $asignadoPor, $asignadoPor !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        return $stmt->execute();
    }

    /**
     * Verifica en el servidor si un usuario posee un permiso atómico específico.
     * Satisface: RF-004 (Control de permisos por rol), RNF-010 (Autorización en servidor).
     */
    public function tienePermiso(int $idUsuario, string $codigoPermiso): bool
    {
        $sql = "SELECT COUNT(*)
                FROM usuario_rol ur
                JOIN rol_permiso rp ON rp.id_rol = ur.id_rol
                JOIN permiso p ON p.id_permiso = rp.id_permiso
                JOIN rol r ON r.id_rol = ur.id_rol
                JOIN usuario u ON u.id_usuario = ur.id_usuario
                WHERE ur.id_usuario = :id_usuario
                  AND p.codigo = :codigo_permiso
                  AND r.estado = 'ACTIVO'
                  AND u.estado = 'ACTIVO'";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':codigo_permiso', $codigoPermiso, PDO::PARAM_STR);
        $stmt->execute();

        return ((int)$stmt->fetchColumn()) > 0;
    }
}
