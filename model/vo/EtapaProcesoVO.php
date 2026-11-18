<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'etapa_proceso'.
 * Representa de forma inmutable un registro de la tabla 'etapa_proceso'.
 */
final class EtapaProcesoVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idEtapa = null,
        public int $idConvocatoria = 0,
        public string $nombre = '',
        public string $tipo = '',
        public int $orden = 1,
        public bool $obligatoria = true,
        public string $estado = 'ACTIVA',
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_etapa' => $this->idEtapa,
            'id_convocatoria' => $this->idConvocatoria,
            'nombre' => $this->nombre,
            'tipo' => $this->tipo,
            'orden' => $this->orden,
            'obligatoria' => $this->obligatoria,
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
            idEtapa: isset($data['id_etapa']) && $data['id_etapa'] !== null && $data['id_etapa'] !== '' ? (int)$data['id_etapa'] : null,
            idConvocatoria: (int)($data['id_convocatoria'] ?? 0),
            nombre: (string)($data['nombre'] ?? ''),
            tipo: (string)($data['tipo'] ?? ''),
            orden: (int)($data['orden'] ?? 0),
            obligatoria: isset($data['obligatoria']) ? (bool)$data['obligatoria'] : false,
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
