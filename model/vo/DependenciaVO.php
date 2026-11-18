<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'dependencia'.
 * Representa de forma inmutable un registro de la tabla 'dependencia'.
 */
final class DependenciaVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idDependencia = null,
        public string $nombre = '',
        public ?string $sigla = null,
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
            'id_dependencia' => $this->idDependencia,
            'nombre' => $this->nombre,
            'sigla' => $this->sigla,
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
            idDependencia: isset($data['id_dependencia']) && $data['id_dependencia'] !== null && $data['id_dependencia'] !== '' ? (int)$data['id_dependencia'] : null,
            nombre: (string)($data['nombre'] ?? ''),
            sigla: isset($data['sigla']) && $data['sigla'] !== null ? (string)$data['sigla'] : null,
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
