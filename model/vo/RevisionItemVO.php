<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'revision_item'.
 * Representa de forma inmutable un registro de la tabla 'revision_item'.
 */
final class RevisionItemVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idRevision = null,
        public int $idVersionItem = 0,
        public int $idRevisor = 0,
        public string $resultado = '',
        public ?int $idMotivo = null,
        public ?string $observacion = null,
        public ?string $fechaRevision = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_revision' => $this->idRevision,
            'id_version_item' => $this->idVersionItem,
            'id_revisor' => $this->idRevisor,
            'resultado' => $this->resultado,
            'id_motivo' => $this->idMotivo,
            'observacion' => $this->observacion,
            'fecha_revision' => $this->fechaRevision,
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
            idRevision: isset($data['id_revision']) && $data['id_revision'] !== null && $data['id_revision'] !== '' ? (int)$data['id_revision'] : null,
            idVersionItem: (int)($data['id_version_item'] ?? 0),
            idRevisor: (int)($data['id_revisor'] ?? 0),
            resultado: (string)($data['resultado'] ?? ''),
            idMotivo: isset($data['id_motivo']) && $data['id_motivo'] !== null && $data['id_motivo'] !== '' ? (int)$data['id_motivo'] : null,
            observacion: isset($data['observacion']) && $data['observacion'] !== null ? (string)$data['observacion'] : null,
            fechaRevision: isset($data['fecha_revision']) && $data['fecha_revision'] !== null ? (string)$data['fecha_revision'] : null,
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
