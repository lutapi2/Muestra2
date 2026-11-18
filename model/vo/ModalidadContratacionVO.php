<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'modalidad_contratacion'.
 * Representa de forma inmutable un registro de la tabla 'modalidad_contratacion'.
 */
final class ModalidadContratacionVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idModalidadContratacion = null,
        public string $nombre = '',
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
            'id_modalidad_contratacion' => $this->idModalidadContratacion,
            'nombre' => $this->nombre,
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
            idModalidadContratacion: isset($data['id_modalidad_contratacion']) && $data['id_modalidad_contratacion'] !== null && $data['id_modalidad_contratacion'] !== '' ? (int)$data['id_modalidad_contratacion'] : null,
            nombre: (string)($data['nombre'] ?? ''),
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
