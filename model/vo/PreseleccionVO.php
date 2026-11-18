<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'preseleccion'.
 * Representa de forma inmutable un registro de la tabla 'preseleccion'.
 */
final class PreseleccionVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idPreseleccion = null,
        public int $idPostulacion = 0,
        public int $idUsuario = 0,
        public ?string $fecha = null,
        public string $fundamento = '',
        public bool $esExcepcion = false,
        public string $estado = 'VIGENTE',
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_preseleccion' => $this->idPreseleccion,
            'id_postulacion' => $this->idPostulacion,
            'id_usuario' => $this->idUsuario,
            'fecha' => $this->fecha,
            'fundamento' => $this->fundamento,
            'es_excepcion' => $this->esExcepcion,
            'estado' => $this->estado,
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
            idPreseleccion: isset($data['id_preseleccion']) && $data['id_preseleccion'] !== null && $data['id_preseleccion'] !== '' ? (int)$data['id_preseleccion'] : null,
            idPostulacion: (int)($data['id_postulacion'] ?? 0),
            idUsuario: (int)($data['id_usuario'] ?? 0),
            fecha: isset($data['fecha']) && $data['fecha'] !== null ? (string)$data['fecha'] : null,
            fundamento: (string)($data['fundamento'] ?? ''),
            esExcepcion: isset($data['es_excepcion']) ? (bool)$data['es_excepcion'] : false,
            estado: (string)($data['estado'] ?? ''),
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
