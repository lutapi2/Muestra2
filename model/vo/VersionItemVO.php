<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'version_item'.
 * Representa de forma inmutable un registro de la tabla 'version_item'.
 */
final class VersionItemVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idVersionItem = null,
        public int $idItemHv = 0,
        public int $numeroVersion = 1,
        public array $datos = [],
        public ?string $creadoEn = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_version_item' => $this->idVersionItem,
            'id_item_hv' => $this->idItemHv,
            'numero_version' => $this->numeroVersion,
            'datos' => $this->datos,
            'creado_en' => $this->creadoEn,
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
            idVersionItem: isset($data['id_version_item']) && $data['id_version_item'] !== null && $data['id_version_item'] !== '' ? (int)$data['id_version_item'] : null,
            idItemHv: (int)($data['id_item_hv'] ?? 0),
            numeroVersion: (int)($data['numero_version'] ?? 0),
            datos: isset($data['datos']) ? (is_array($data['datos']) ? $data['datos'] : (json_decode((string)$data['datos'], true) ?? [])) : [],
            creadoEn: isset($data['creado_en']) && $data['creado_en'] !== null ? (string)$data['creado_en'] : null,
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
