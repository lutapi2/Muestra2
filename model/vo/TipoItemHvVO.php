<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'tipo_item_hv'.
 * Representa de forma inmutable un registro de la tabla 'tipo_item_hv'.
 */
final class TipoItemHvVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idTipoItem = null,
        public string $codigo = '',
        public string $seccion = '',
        public string $nombre = '',
        public string $camposMinimos = '',
        public ?string $soporteEsperado = null,
        public string $obligatoriedadBase = 'OBLIGATORIO',
        public bool $repetible = true,
        public string $almacenamiento = 'ITEM_HV',
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
            'id_tipo_item' => $this->idTipoItem,
            'codigo' => $this->codigo,
            'seccion' => $this->seccion,
            'nombre' => $this->nombre,
            'campos_minimos' => $this->camposMinimos,
            'soporte_esperado' => $this->soporteEsperado,
            'obligatoriedad_base' => $this->obligatoriedadBase,
            'repetible' => $this->repetible,
            'almacenamiento' => $this->almacenamiento,
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
            idTipoItem: isset($data['id_tipo_item']) && $data['id_tipo_item'] !== null && $data['id_tipo_item'] !== '' ? (int)$data['id_tipo_item'] : null,
            codigo: (string)($data['codigo'] ?? ''),
            seccion: (string)($data['seccion'] ?? ''),
            nombre: (string)($data['nombre'] ?? ''),
            camposMinimos: (string)($data['campos_minimos'] ?? ''),
            soporteEsperado: isset($data['soporte_esperado']) && $data['soporte_esperado'] !== null ? (string)$data['soporte_esperado'] : null,
            obligatoriedadBase: (string)($data['obligatoriedad_base'] ?? ''),
            repetible: isset($data['repetible']) ? (bool)$data['repetible'] : false,
            almacenamiento: (string)($data['almacenamiento'] ?? ''),
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
