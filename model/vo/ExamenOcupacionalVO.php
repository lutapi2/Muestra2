<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'examen_ocupacional'.
 * Representa de forma inmutable un registro de la tabla 'examen_ocupacional'.
 */
final class ExamenOcupacionalVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idExamen = null,
        public int $idPostulacion = 0,
        public string $proveedorSalud = '',
        public string $fechaProgramada = '',
        public ?string $fechaConcepto = null,
        public string $conceptoAptitud = 'PENDIENTE',
        public ?string $observacionesSst = null,
        public int $idResponsableSst = 0,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_examen' => $this->idExamen,
            'id_postulacion' => $this->idPostulacion,
            'proveedor_salud' => $this->proveedorSalud,
            'fecha_programada' => $this->fechaProgramada,
            'fecha_concepto' => $this->fechaConcepto,
            'concepto_aptitud' => $this->conceptoAptitud,
            'observaciones_sst' => $this->observacionesSst,
            'id_responsable_sst' => $this->idResponsableSst,
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
            idExamen: isset($data['id_examen']) && $data['id_examen'] !== null && $data['id_examen'] !== '' ? (int)$data['id_examen'] : null,
            idPostulacion: (int)($data['id_postulacion'] ?? 0),
            proveedorSalud: (string)($data['proveedor_salud'] ?? ''),
            fechaProgramada: (string)($data['fecha_programada'] ?? ''),
            fechaConcepto: isset($data['fecha_concepto']) && $data['fecha_concepto'] !== null ? (string)$data['fecha_concepto'] : null,
            conceptoAptitud: (string)($data['concepto_aptitud'] ?? ''),
            observacionesSst: isset($data['observaciones_sst']) && $data['observaciones_sst'] !== null ? (string)$data['observaciones_sst'] : null,
            idResponsableSst: (int)($data['id_responsable_sst'] ?? 0),
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
