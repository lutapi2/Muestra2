<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'resultado_prueba'.
 * Representa de forma inmutable un registro de la tabla 'resultado_prueba'.
 */
final class ResultadoPruebaVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idResultadoPrueba = null,
        public int $idPrueba = 0,
        public int $idPostulacion = 0,
        public ?float $calificacion = null,
        public string $dictamen = 'PENDIENTE',
        public ?string $observacion = null,
        public ?string $soporteEvidencia = null,
        public ?string $fechaRegistro = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_resultado_prueba' => $this->idResultadoPrueba,
            'id_prueba' => $this->idPrueba,
            'id_postulacion' => $this->idPostulacion,
            'calificacion' => $this->calificacion,
            'dictamen' => $this->dictamen,
            'observacion' => $this->observacion,
            'soporte_evidencia' => $this->soporteEvidencia,
            'fecha_registro' => $this->fechaRegistro,
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
            idResultadoPrueba: isset($data['id_resultado_prueba']) && $data['id_resultado_prueba'] !== null && $data['id_resultado_prueba'] !== '' ? (int)$data['id_resultado_prueba'] : null,
            idPrueba: (int)($data['id_prueba'] ?? 0),
            idPostulacion: (int)($data['id_postulacion'] ?? 0),
            calificacion: isset($data['calificacion']) && $data['calificacion'] !== null && $data['calificacion'] !== '' ? (float)$data['calificacion'] : null,
            dictamen: (string)($data['dictamen'] ?? ''),
            observacion: isset($data['observacion']) && $data['observacion'] !== null ? (string)$data['observacion'] : null,
            soporteEvidencia: isset($data['soporte_evidencia']) && $data['soporte_evidencia'] !== null ? (string)$data['soporte_evidencia'] : null,
            fechaRegistro: isset($data['fecha_registro']) && $data['fecha_registro'] !== null ? (string)$data['fecha_registro'] : null,
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
