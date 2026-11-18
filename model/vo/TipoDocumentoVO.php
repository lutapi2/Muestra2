<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'tipo_documento'.
 * Representa de forma inmutable un registro de la tabla 'tipo_documento'.
 */
final class TipoDocumentoVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idTipoDocumento = null,
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
            'id_tipo_documento' => $this->idTipoDocumento,
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
            idTipoDocumento: isset($data['id_tipo_documento']) && $data['id_tipo_documento'] !== null && $data['id_tipo_documento'] !== '' ? (int)$data['id_tipo_documento'] : null,
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
