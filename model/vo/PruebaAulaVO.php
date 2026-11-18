<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'prueba_aula'.
 * Representa de forma inmutable un registro de la tabla 'prueba_aula'.
 */
final class PruebaAulaVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idPruebaAula = null,
        public int $idPostulacion = 0,
        public string $tema = '',
        public string $rubrica = '',
        public int $idEvaluador = 0,
        public float $puntaje = 0.0,
        public string $dictamen = '',
        public ?string $observaciones = null,
        public ?string $soporteActa = null,
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
            'id_prueba_aula' => $this->idPruebaAula,
            'id_postulacion' => $this->idPostulacion,
            'tema' => $this->tema,
            'rubrica' => $this->rubrica,
            'id_evaluador' => $this->idEvaluador,
            'puntaje' => $this->puntaje,
            'dictamen' => $this->dictamen,
            'observaciones' => $this->observaciones,
            'soporte_acta' => $this->soporteActa,
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
            idPruebaAula: isset($data['id_prueba_aula']) && $data['id_prueba_aula'] !== null && $data['id_prueba_aula'] !== '' ? (int)$data['id_prueba_aula'] : null,
            idPostulacion: (int)($data['id_postulacion'] ?? 0),
            tema: (string)($data['tema'] ?? ''),
            rubrica: (string)($data['rubrica'] ?? ''),
            idEvaluador: (int)($data['id_evaluador'] ?? 0),
            puntaje: (float)($data['puntaje'] ?? 0.0),
            dictamen: (string)($data['dictamen'] ?? ''),
            observaciones: isset($data['observaciones']) && $data['observaciones'] !== null ? (string)$data['observaciones'] : null,
            soporteActa: isset($data['soporte_acta']) && $data['soporte_acta'] !== null ? (string)$data['soporte_acta'] : null,
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
