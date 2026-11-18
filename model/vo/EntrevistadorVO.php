<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'entrevistador'.
 * Representa de forma inmutable un registro de la tabla 'entrevistador'.
 */
final class EntrevistadorVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idEntrevistador = null,
        public int $idEntrevista = 0,
        public int $idUsuario = 0,
        public string $rolComite = 'MIEMBRO',
        public string $estado = 'ASIGNADO',
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_entrevistador' => $this->idEntrevistador,
            'id_entrevista' => $this->idEntrevista,
            'id_usuario' => $this->idUsuario,
            'rol_comite' => $this->rolComite,
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
            idEntrevistador: isset($data['id_entrevistador']) && $data['id_entrevistador'] !== null && $data['id_entrevistador'] !== '' ? (int)$data['id_entrevistador'] : null,
            idEntrevista: (int)($data['id_entrevista'] ?? 0),
            idUsuario: (int)($data['id_usuario'] ?? 0),
            rolComite: (string)($data['rol_comite'] ?? ''),
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
