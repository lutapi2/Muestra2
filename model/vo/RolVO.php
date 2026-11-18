<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'rol'.
 * Representa de forma inmutable un registro de la tabla 'rol'.
 */
final class RolVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idRol = null,
        public string $nombre = '',
        public ?string $descripcion = null,
        public string $estado = 'ACTIVO',
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
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
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
            idRol: isset($data['id_rol']) && $data['id_rol'] !== null && $data['id_rol'] !== '' ? (int)$data['id_rol'] : null,
            nombre: (string)($data['nombre'] ?? ''),
            descripcion: isset($data['descripcion']) && $data['descripcion'] !== null ? (string)$data['descripcion'] : null,
            estado: (string)($data['estado'] ?? ''),
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
