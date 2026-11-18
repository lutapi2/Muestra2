<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'periodo_academico'.
 * Representa de forma inmutable un registro de la tabla 'periodo_academico'.
 */
final class PeriodoAcademicoVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idPeriodo = null,
        public string $codigo = '',
        public string $nombre = '',
        public string $fechaInicio = '',
        public string $fechaFin = '',
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
            'id_periodo' => $this->idPeriodo,
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'fecha_inicio' => $this->fechaInicio,
            'fecha_fin' => $this->fechaFin,
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
            idPeriodo: isset($data['id_periodo']) && $data['id_periodo'] !== null && $data['id_periodo'] !== '' ? (int)$data['id_periodo'] : null,
            codigo: (string)($data['codigo'] ?? ''),
            nombre: (string)($data['nombre'] ?? ''),
            fechaInicio: (string)($data['fecha_inicio'] ?? ''),
            fechaFin: (string)($data['fecha_fin'] ?? ''),
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
