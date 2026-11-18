<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'resultado_etapa'.
 * Representa de forma inmutable un registro de la tabla 'resultado_etapa'.
 */
final class ResultadoEtapaVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idResultadoEtapa = null,
        public int $idPostulacion = 0,
        public int $idEtapa = 0,
        public string $dictamen = '',
        public string $observacion = '',
        public ?int $idMotivoDescarte = null,
        public int $idResponsable = 0,
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
            'id_resultado_etapa' => $this->idResultadoEtapa,
            'id_postulacion' => $this->idPostulacion,
            'id_etapa' => $this->idEtapa,
            'dictamen' => $this->dictamen,
            'observacion' => $this->observacion,
            'id_motivo_descarte' => $this->idMotivoDescarte,
            'id_responsable' => $this->idResponsable,
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
            idResultadoEtapa: isset($data['id_resultado_etapa']) && $data['id_resultado_etapa'] !== null && $data['id_resultado_etapa'] !== '' ? (int)$data['id_resultado_etapa'] : null,
            idPostulacion: (int)($data['id_postulacion'] ?? 0),
            idEtapa: (int)($data['id_etapa'] ?? 0),
            dictamen: (string)($data['dictamen'] ?? ''),
            observacion: (string)($data['observacion'] ?? ''),
            idMotivoDescarte: isset($data['id_motivo_descarte']) && $data['id_motivo_descarte'] !== null && $data['id_motivo_descarte'] !== '' ? (int)$data['id_motivo_descarte'] : null,
            idResponsable: (int)($data['id_responsable'] ?? 0),
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
