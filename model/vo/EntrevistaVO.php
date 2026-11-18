<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'entrevista'.
 * Representa de forma inmutable un registro de la tabla 'entrevista'.
 */
final class EntrevistaVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idEntrevista = null,
        public int $idPostulacion = 0,
        public int $idEtapa = 0,
        public ?int $idFranja = null,
        public string $inicio = '',
        public string $fin = '',
        public string $modalidad = 'PRESENCIAL',
        public ?string $ubicacion = null,
        public ?string $enlaceVirtual = null,
        public string $estado = 'PROGRAMADA',
        public string $confirmacionCandidato = 'PENDIENTE',
        public int $reprogramaciones = 0,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_entrevista' => $this->idEntrevista,
            'id_postulacion' => $this->idPostulacion,
            'id_etapa' => $this->idEtapa,
            'id_franja' => $this->idFranja,
            'inicio' => $this->inicio,
            'fin' => $this->fin,
            'modalidad' => $this->modalidad,
            'ubicacion' => $this->ubicacion,
            'enlace_virtual' => $this->enlaceVirtual,
            'estado' => $this->estado,
            'confirmacion_candidato' => $this->confirmacionCandidato,
            'reprogramaciones' => $this->reprogramaciones,
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
            idEntrevista: isset($data['id_entrevista']) && $data['id_entrevista'] !== null && $data['id_entrevista'] !== '' ? (int)$data['id_entrevista'] : null,
            idPostulacion: (int)($data['id_postulacion'] ?? 0),
            idEtapa: (int)($data['id_etapa'] ?? 0),
            idFranja: isset($data['id_franja']) && $data['id_franja'] !== null && $data['id_franja'] !== '' ? (int)$data['id_franja'] : null,
            inicio: (string)($data['inicio'] ?? ''),
            fin: (string)($data['fin'] ?? ''),
            modalidad: (string)($data['modalidad'] ?? ''),
            ubicacion: isset($data['ubicacion']) && $data['ubicacion'] !== null ? (string)$data['ubicacion'] : null,
            enlaceVirtual: isset($data['enlace_virtual']) && $data['enlace_virtual'] !== null ? (string)$data['enlace_virtual'] : null,
            estado: (string)($data['estado'] ?? ''),
            confirmacionCandidato: (string)($data['confirmacion_candidato'] ?? ''),
            reprogramaciones: (int)($data['reprogramaciones'] ?? 0),
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
