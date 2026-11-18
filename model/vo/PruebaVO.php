<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'prueba'.
 * Representa de forma inmutable un registro de la tabla 'prueba'.
 */
final class PruebaVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idPrueba = null,
        public int $idConvocatoria = 0,
        public string $tipo = '',
        public string $nombre = '',
        public ?string $descripcion = null,
        public ?string $fechaAplicacion = null,
        public int $idResponsable = 0,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_prueba' => $this->idPrueba,
            'id_convocatoria' => $this->idConvocatoria,
            'tipo' => $this->tipo,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'fecha_aplicacion' => $this->fechaAplicacion,
            'id_responsable' => $this->idResponsable,
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
            idPrueba: isset($data['id_prueba']) && $data['id_prueba'] !== null && $data['id_prueba'] !== '' ? (int)$data['id_prueba'] : null,
            idConvocatoria: (int)($data['id_convocatoria'] ?? 0),
            tipo: (string)($data['tipo'] ?? ''),
            nombre: (string)($data['nombre'] ?? ''),
            descripcion: isset($data['descripcion']) && $data['descripcion'] !== null ? (string)$data['descripcion'] : null,
            fechaAplicacion: isset($data['fecha_aplicacion']) && $data['fecha_aplicacion'] !== null ? (string)$data['fecha_aplicacion'] : null,
            idResponsable: (int)($data['id_responsable'] ?? 0),
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
