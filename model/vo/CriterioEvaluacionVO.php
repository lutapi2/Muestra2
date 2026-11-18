<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'criterio_evaluacion'.
 * Representa de forma inmutable un registro de la tabla 'criterio_evaluacion'.
 */
final class CriterioEvaluacionVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idCriterio = null,
        public int $idConvocatoria = 0,
        public ?int $idEtapa = null,
        public string $nombre = '',
        public ?string $descripcion = null,
        public float $puntajeMaximo = 0.0,
        public float $ponderacion = 0.0,
        public bool $obligatorio = true,
        public bool $bloqueado = false,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_criterio' => $this->idCriterio,
            'id_convocatoria' => $this->idConvocatoria,
            'id_etapa' => $this->idEtapa,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'puntaje_maximo' => $this->puntajeMaximo,
            'ponderacion' => $this->ponderacion,
            'obligatorio' => $this->obligatorio,
            'bloqueado' => $this->bloqueado,
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
            idCriterio: isset($data['id_criterio']) && $data['id_criterio'] !== null && $data['id_criterio'] !== '' ? (int)$data['id_criterio'] : null,
            idConvocatoria: (int)($data['id_convocatoria'] ?? 0),
            idEtapa: isset($data['id_etapa']) && $data['id_etapa'] !== null && $data['id_etapa'] !== '' ? (int)$data['id_etapa'] : null,
            nombre: (string)($data['nombre'] ?? ''),
            descripcion: isset($data['descripcion']) && $data['descripcion'] !== null ? (string)$data['descripcion'] : null,
            puntajeMaximo: (float)($data['puntaje_maximo'] ?? 0.0),
            ponderacion: (float)($data['ponderacion'] ?? 0.0),
            obligatorio: isset($data['obligatorio']) ? (bool)$data['obligatorio'] : false,
            bloqueado: isset($data['bloqueado']) ? (bool)$data['bloqueado'] : false,
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
