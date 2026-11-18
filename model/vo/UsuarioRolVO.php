<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'usuario_rol'.
 * Representa de forma inmutable un registro de la tabla 'usuario_rol'.
 */
final class UsuarioRolVO implements \JsonSerializable
{
    public function __construct(
        public int $idUsuario = 0,
        public int $idRol = 0,
        public ?string $asignadoEn = null,
        public ?int $asignadoPor = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_usuario' => $this->idUsuario,
            'id_rol' => $this->idRol,
            'asignado_en' => $this->asignadoEn,
            'asignado_por' => $this->asignadoPor,
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
            idUsuario: (int)($data['id_usuario'] ?? 0),
            idRol: (int)($data['id_rol'] ?? 0),
            asignadoEn: isset($data['asignado_en']) && $data['asignado_en'] !== null ? (string)$data['asignado_en'] : null,
            asignadoPor: isset($data['asignado_por']) && $data['asignado_por'] !== null && $data['asignado_por'] !== '' ? (int)$data['asignado_por'] : null,
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
