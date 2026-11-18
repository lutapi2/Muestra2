<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'actividad_plan'.
 * Representa de forma inmutable un registro de la tabla 'actividad_plan'.
 */
final class ActividadPlanVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idActividad = null,
        public int $idPlan = 0,
        public string $nombre = '',
        public ?string $descripcion = null,
        public int $idResponsable = 0,
        public string $fechaLimite = '',
        public bool $completada = false,
        public ?string $fechaCompletada = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_actividad' => $this->idActividad,
            'id_plan' => $this->idPlan,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'id_responsable' => $this->idResponsable,
            'fecha_limite' => $this->fechaLimite,
            'completada' => $this->completada,
            'fecha_completada' => $this->fechaCompletada,
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
            idActividad: isset($data['id_actividad']) && $data['id_actividad'] !== null && $data['id_actividad'] !== '' ? (int)$data['id_actividad'] : null,
            idPlan: (int)($data['id_plan'] ?? 0),
            nombre: (string)($data['nombre'] ?? ''),
            descripcion: isset($data['descripcion']) && $data['descripcion'] !== null ? (string)$data['descripcion'] : null,
            idResponsable: (int)($data['id_responsable'] ?? 0),
            fechaLimite: (string)($data['fecha_limite'] ?? ''),
            completada: isset($data['completada']) ? (bool)$data['completada'] : false,
            fechaCompletada: isset($data['fecha_completada']) && $data['fecha_completada'] !== null ? (string)$data['fecha_completada'] : null,
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
