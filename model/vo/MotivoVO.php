<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'motivo'.
 * Representa de forma inmutable un registro de la tabla 'motivo'.
 */
final class MotivoVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idMotivo = null,
        public string $tipo = '',
        public string $codigo = '',
        public string $nombre = '',
        public ?string $mensajeCandidato = null,
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
            'id_motivo' => $this->idMotivo,
            'tipo' => $this->tipo,
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'mensaje_candidato' => $this->mensajeCandidato,
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
            idMotivo: isset($data['id_motivo']) && $data['id_motivo'] !== null && $data['id_motivo'] !== '' ? (int)$data['id_motivo'] : null,
            tipo: (string)($data['tipo'] ?? ''),
            codigo: (string)($data['codigo'] ?? ''),
            nombre: (string)($data['nombre'] ?? ''),
            mensajeCandidato: isset($data['mensaje_candidato']) && $data['mensaje_candidato'] !== null ? (string)$data['mensaje_candidato'] : null,
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
