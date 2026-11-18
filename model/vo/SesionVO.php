<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'sesion'.
 * Representa de forma inmutable un registro de la tabla 'sesion'.
 */
final class SesionVO implements \JsonSerializable
{
    public function __construct(
        public string $idSesion = '',
        public int $idUsuario = 0,
        public ?string $creadaEn = null,
        public string $expiraEn = '',
        public ?string $cerradaEn = null,
        public ?string $ip = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_sesion' => $this->idSesion,
            'id_usuario' => $this->idUsuario,
            'creada_en' => $this->creadaEn,
            'expira_en' => $this->expiraEn,
            'cerrada_en' => $this->cerradaEn,
            'ip' => $this->ip,
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
            idSesion: (string)($data['id_sesion'] ?? ''),
            idUsuario: (int)($data['id_usuario'] ?? 0),
            creadaEn: isset($data['creada_en']) && $data['creada_en'] !== null ? (string)$data['creada_en'] : null,
            expiraEn: (string)($data['expira_en'] ?? ''),
            cerradaEn: isset($data['cerrada_en']) && $data['cerrada_en'] !== null ? (string)$data['cerrada_en'] : null,
            ip: isset($data['ip']) && $data['ip'] !== null ? (string)$data['ip'] : null,
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
