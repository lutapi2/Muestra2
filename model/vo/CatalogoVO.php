<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'catalogo'.
 * Representa de forma inmutable un registro de la tabla 'catalogo'.
 */
final class CatalogoVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idCatalogo = null,
        public string $tipo = '',
        public string $codigo = '',
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
            'id_catalogo' => $this->idCatalogo,
            'tipo' => $this->tipo,
            'codigo' => $this->codigo,
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
            idCatalogo: isset($data['id_catalogo']) && $data['id_catalogo'] !== null && $data['id_catalogo'] !== '' ? (int)$data['id_catalogo'] : null,
            tipo: (string)($data['tipo'] ?? ''),
            codigo: (string)($data['codigo'] ?? ''),
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
