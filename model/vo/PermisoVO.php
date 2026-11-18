<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'permiso'.
 * Representa de forma inmutable un registro de la tabla 'permiso'.
 */
final class PermisoVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idPermiso = null,
        public string $codigo = '',
        public string $modulo = '',
        public ?string $descripcion = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_permiso' => $this->idPermiso,
            'codigo' => $this->codigo,
            'modulo' => $this->modulo,
            'descripcion' => $this->descripcion,
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
            idPermiso: isset($data['id_permiso']) && $data['id_permiso'] !== null && $data['id_permiso'] !== '' ? (int)$data['id_permiso'] : null,
            codigo: (string)($data['codigo'] ?? ''),
            modulo: (string)($data['modulo'] ?? ''),
            descripcion: isset($data['descripcion']) && $data['descripcion'] !== null ? (string)$data['descripcion'] : null,
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
