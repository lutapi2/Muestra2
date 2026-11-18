<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'sede'.
 * Representa de forma inmutable un registro de la tabla 'sede'.
 */
final class SedeVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idSede = null,
        public string $nombre = '',
        public string $ciudad = '',
        public bool $activo = true,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_sede' => $this->idSede,
            'nombre' => $this->nombre,
            'ciudad' => $this->ciudad,
            'activo' => $this->activo,
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
            idSede: isset($data['id_sede']) && $data['id_sede'] !== null && $data['id_sede'] !== '' ? (int)$data['id_sede'] : null,
            nombre: (string)($data['nombre'] ?? ''),
            ciudad: (string)($data['ciudad'] ?? ''),
            activo: isset($data['activo']) ? (bool)$data['activo'] : false,
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
