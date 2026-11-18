<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'guia_entrevista'.
 * Representa de forma inmutable un registro de la tabla 'guia_entrevista'.
 */
final class GuiaEntrevistaVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idGuia = null,
        public int $idEtapa = 0,
        public int $orden = 1,
        public string $pregunta = '',
        public ?int $idCriterio = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_guia' => $this->idGuia,
            'id_etapa' => $this->idEtapa,
            'orden' => $this->orden,
            'pregunta' => $this->pregunta,
            'id_criterio' => $this->idCriterio,
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
            idGuia: isset($data['id_guia']) && $data['id_guia'] !== null && $data['id_guia'] !== '' ? (int)$data['id_guia'] : null,
            idEtapa: (int)($data['id_etapa'] ?? 0),
            orden: (int)($data['orden'] ?? 0),
            pregunta: (string)($data['pregunta'] ?? ''),
            idCriterio: isset($data['id_criterio']) && $data['id_criterio'] !== null && $data['id_criterio'] !== '' ? (int)$data['id_criterio'] : null,
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
