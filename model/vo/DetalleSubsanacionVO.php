<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'detalle_subsanacion'.
 * Representa de forma inmutable un registro de la tabla 'detalle_subsanacion'.
 */
final class DetalleSubsanacionVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idDetalle = null,
        public int $idSolicitud = 0,
        public int $idItemHv = 0,
        public ?int $idRevision = null,
        public string $motivo = '',
        public string $estado = 'PENDIENTE',
        public ?int $idVersionCorregida = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_detalle' => $this->idDetalle,
            'id_solicitud' => $this->idSolicitud,
            'id_item_hv' => $this->idItemHv,
            'id_revision' => $this->idRevision,
            'motivo' => $this->motivo,
            'estado' => $this->estado,
            'id_version_corregida' => $this->idVersionCorregida,
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
            idDetalle: isset($data['id_detalle']) && $data['id_detalle'] !== null && $data['id_detalle'] !== '' ? (int)$data['id_detalle'] : null,
            idSolicitud: (int)($data['id_solicitud'] ?? 0),
            idItemHv: (int)($data['id_item_hv'] ?? 0),
            idRevision: isset($data['id_revision']) && $data['id_revision'] !== null && $data['id_revision'] !== '' ? (int)$data['id_revision'] : null,
            motivo: (string)($data['motivo'] ?? ''),
            estado: (string)($data['estado'] ?? ''),
            idVersionCorregida: isset($data['id_version_corregida']) && $data['id_version_corregida'] !== null && $data['id_version_corregida'] !== '' ? (int)$data['id_version_corregida'] : null,
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
