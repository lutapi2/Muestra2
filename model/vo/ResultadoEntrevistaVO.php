<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'resultado_entrevista'.
 * Representa de forma inmutable un registro de la tabla 'resultado_entrevista'.
 */
final class ResultadoEntrevistaVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idResultado = null,
        public int $idEntrevista = 0,
        public ?int $idEvaluador = null,
        public string $tipo = '',
        public ?float $puntaje = null,
        public string $concepto = '',
        public ?string $observacion = null,
        public ?string $fecha = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_resultado' => $this->idResultado,
            'id_entrevista' => $this->idEntrevista,
            'id_evaluador' => $this->idEvaluador,
            'tipo' => $this->tipo,
            'puntaje' => $this->puntaje,
            'concepto' => $this->concepto,
            'observacion' => $this->observacion,
            'fecha' => $this->fecha,
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
            idResultado: isset($data['id_resultado']) && $data['id_resultado'] !== null && $data['id_resultado'] !== '' ? (int)$data['id_resultado'] : null,
            idEntrevista: (int)($data['id_entrevista'] ?? 0),
            idEvaluador: isset($data['id_evaluador']) && $data['id_evaluador'] !== null && $data['id_evaluador'] !== '' ? (int)$data['id_evaluador'] : null,
            tipo: (string)($data['tipo'] ?? ''),
            puntaje: isset($data['puntaje']) && $data['puntaje'] !== null && $data['puntaje'] !== '' ? (float)$data['puntaje'] : null,
            concepto: (string)($data['concepto'] ?? ''),
            observacion: isset($data['observacion']) && $data['observacion'] !== null ? (string)$data['observacion'] : null,
            fecha: isset($data['fecha']) && $data['fecha'] !== null ? (string)$data['fecha'] : null,
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
