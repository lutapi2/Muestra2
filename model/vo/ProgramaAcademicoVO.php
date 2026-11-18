<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'programa_academico'.
 * Representa de forma inmutable un registro de la tabla 'programa_academico'.
 */
final class ProgramaAcademicoVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idPrograma = null,
        public int $idFacultad = 0,
        public string $nombre = '',
        public ?string $codigo = null,
        public bool $activo = true,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_programa' => $this->idPrograma,
            'id_facultad' => $this->idFacultad,
            'nombre' => $this->nombre,
            'codigo' => $this->codigo,
            'activo' => $this->activo,
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
            idPrograma: isset($data['id_programa']) && $data['id_programa'] !== null && $data['id_programa'] !== '' ? (int)$data['id_programa'] : null,
            idFacultad: (int)($data['id_facultad'] ?? 0),
            nombre: (string)($data['nombre'] ?? ''),
            codigo: isset($data['codigo']) && $data['codigo'] !== null ? (string)$data['codigo'] : null,
            activo: isset($data['activo']) ? (bool)$data['activo'] : false,
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
