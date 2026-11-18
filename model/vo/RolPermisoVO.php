<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'rol_permiso'.
 * Representa de forma inmutable un registro de la tabla 'rol_permiso'.
 */
final class RolPermisoVO implements \JsonSerializable
{
    public function __construct(
        public int $idRol = 0,
        public int $idPermiso = 0,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_rol' => $this->idRol,
            'id_permiso' => $this->idPermiso,
        ];
    }

    /**
     * Construye una instancia del Value Object a partir de un arreglo asociativo de la BD.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            idRol: (int)($data['id_rol'] ?? 0),
            idPermiso: (int)($data['id_permiso'] ?? 0),
        );
    }

    /**
     * Serialización JSON nativa.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
