<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'item_hv'.
 * Representa de forma inmutable un registro de la tabla 'item_hv'.
 */
final class ItemHvVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idItemHv = null,
        public int $idPostulacion = 0,
        public int $idTipoItem = 0,
        public string $estadoRevision = 'PENDIENTE',
        public ?string $fechaRegistro = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_item_hv' => $this->idItemHv,
            'id_postulacion' => $this->idPostulacion,
            'id_tipo_item' => $this->idTipoItem,
            'estado_revision' => $this->estadoRevision,
            'fecha_registro' => $this->fechaRegistro,
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
            idItemHv: isset($data['id_item_hv']) && $data['id_item_hv'] !== null && $data['id_item_hv'] !== '' ? (int)$data['id_item_hv'] : null,
            idPostulacion: (int)($data['id_postulacion'] ?? 0),
            idTipoItem: (int)($data['id_tipo_item'] ?? 0),
            estadoRevision: (string)($data['estado_revision'] ?? ''),
            fechaRegistro: isset($data['fecha_registro']) && $data['fecha_registro'] !== null ? (string)$data['fecha_registro'] : null,
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
