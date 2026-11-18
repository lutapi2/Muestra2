<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'evaluacion'.
 * Representa de forma inmutable un registro de la tabla 'evaluacion'.
 */
final class EvaluacionVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idEvaluacion = null,
        public int $idPostulacion = 0,
        public int $idCriterio = 0,
        public int $idEvaluador = 0,
        public string $resultado = '',
        public ?float $puntaje = null,
        public ?string $evidencia = null,
        public ?string $observacion = null,
        public ?string $fechaEvaluacion = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_evaluacion' => $this->idEvaluacion,
            'id_postulacion' => $this->idPostulacion,
            'id_criterio' => $this->idCriterio,
            'id_evaluador' => $this->idEvaluador,
            'resultado' => $this->resultado,
            'puntaje' => $this->puntaje,
            'evidencia' => $this->evidencia,
            'observacion' => $this->observacion,
            'fecha_evaluacion' => $this->fechaEvaluacion,
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
            idEvaluacion: isset($data['id_evaluacion']) && $data['id_evaluacion'] !== null && $data['id_evaluacion'] !== '' ? (int)$data['id_evaluacion'] : null,
            idPostulacion: (int)($data['id_postulacion'] ?? 0),
            idCriterio: (int)($data['id_criterio'] ?? 0),
            idEvaluador: (int)($data['id_evaluador'] ?? 0),
            resultado: (string)($data['resultado'] ?? ''),
            puntaje: isset($data['puntaje']) && $data['puntaje'] !== null && $data['puntaje'] !== '' ? (float)$data['puntaje'] : null,
            evidencia: isset($data['evidencia']) && $data['evidencia'] !== null ? (string)$data['evidencia'] : null,
            observacion: isset($data['observacion']) && $data['observacion'] !== null ? (string)$data['observacion'] : null,
            fechaEvaluacion: isset($data['fecha_evaluacion']) && $data['fecha_evaluacion'] !== null ? (string)$data['fecha_evaluacion'] : null,
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
