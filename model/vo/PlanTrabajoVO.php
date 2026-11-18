<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'plan_trabajo'.
 * Representa de forma inmutable un registro de la tabla 'plan_trabajo'.
 */
final class PlanTrabajoVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idPlan = null,
        public int $idConvocatoria = 0,
        public int $idResponsable = 0,
        public string $fechaInicio = '',
        public string $fechaEstimadaFin = '',
        public string $estado = 'EN_CURSO',
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_plan' => $this->idPlan,
            'id_convocatoria' => $this->idConvocatoria,
            'id_responsable' => $this->idResponsable,
            'fecha_inicio' => $this->fechaInicio,
            'fecha_estimada_fin' => $this->fechaEstimadaFin,
            'estado' => $this->estado,
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
            idPlan: isset($data['id_plan']) && $data['id_plan'] !== null && $data['id_plan'] !== '' ? (int)$data['id_plan'] : null,
            idConvocatoria: (int)($data['id_convocatoria'] ?? 0),
            idResponsable: (int)($data['id_responsable'] ?? 0),
            fechaInicio: (string)($data['fecha_inicio'] ?? ''),
            fechaEstimadaFin: (string)($data['fecha_estimada_fin'] ?? ''),
            estado: (string)($data['estado'] ?? ''),
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
