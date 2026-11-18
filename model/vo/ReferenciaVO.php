<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'referencia'.
 * Representa de forma inmutable un registro de la tabla 'referencia'.
 */
final class ReferenciaVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idReferencia = null,
        public int $idCandidato = 0,
        public string $nombre = '',
        public string $relacion = '',
        public ?string $entidad = null,
        public string $telefono = '',
        public ?string $correo = null,
        public bool $autorizaContacto = false,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_referencia' => $this->idReferencia,
            'id_candidato' => $this->idCandidato,
            'nombre' => $this->nombre,
            'relacion' => $this->relacion,
            'entidad' => $this->entidad,
            'telefono' => $this->telefono,
            'correo' => $this->correo,
            'autoriza_contacto' => $this->autorizaContacto,
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
            idReferencia: isset($data['id_referencia']) && $data['id_referencia'] !== null && $data['id_referencia'] !== '' ? (int)$data['id_referencia'] : null,
            idCandidato: (int)($data['id_candidato'] ?? 0),
            nombre: (string)($data['nombre'] ?? ''),
            relacion: (string)($data['relacion'] ?? ''),
            entidad: isset($data['entidad']) && $data['entidad'] !== null ? (string)$data['entidad'] : null,
            telefono: (string)($data['telefono'] ?? ''),
            correo: isset($data['correo']) && $data['correo'] !== null ? (string)$data['correo'] : null,
            autorizaContacto: isset($data['autoriza_contacto']) ? (bool)$data['autoriza_contacto'] : false,
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
