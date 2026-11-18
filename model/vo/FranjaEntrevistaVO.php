<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'franja_entrevista'.
 * Representa de forma inmutable un registro de la tabla 'franja_entrevista'.
 */
final class FranjaEntrevistaVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idFranja = null,
        public int $idConvocatoria = 0,
        public int $idEtapa = 0,
        public string $inicio = '',
        public string $fin = '',
        public int $cupos = 1,
        public string $modalidad = 'PRESENCIAL',
        public ?string $recurso = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_franja' => $this->idFranja,
            'id_convocatoria' => $this->idConvocatoria,
            'id_etapa' => $this->idEtapa,
            'inicio' => $this->inicio,
            'fin' => $this->fin,
            'cupos' => $this->cupos,
            'modalidad' => $this->modalidad,
            'recurso' => $this->recurso,
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
            idFranja: isset($data['id_franja']) && $data['id_franja'] !== null && $data['id_franja'] !== '' ? (int)$data['id_franja'] : null,
            idConvocatoria: (int)($data['id_convocatoria'] ?? 0),
            idEtapa: (int)($data['id_etapa'] ?? 0),
            inicio: (string)($data['inicio'] ?? ''),
            fin: (string)($data['fin'] ?? ''),
            cupos: (int)($data['cupos'] ?? 0),
            modalidad: (string)($data['modalidad'] ?? ''),
            recurso: isset($data['recurso']) && $data['recurso'] !== null ? (string)$data['recurso'] : null,
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
