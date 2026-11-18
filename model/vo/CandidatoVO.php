<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'candidato'.
 * Representa de forma inmutable un registro de la tabla 'candidato'.
 */
final class CandidatoVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idCandidato = null,
        public int $idUsuario = 0,
        public string $nombres = '',
        public string $apellidos = '',
        public int $idTipoDocumento = 0,
        public string $numeroDocumento = '',
        public string $telefono = '',
        public string $ciudad = '',
        public ?string $direccion = null,
        public ?string $creadoEn = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_candidato' => $this->idCandidato,
            'id_usuario' => $this->idUsuario,
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'id_tipo_documento' => $this->idTipoDocumento,
            'numero_documento' => $this->numeroDocumento,
            'telefono' => $this->telefono,
            'ciudad' => $this->ciudad,
            'direccion' => $this->direccion,
            'creado_en' => $this->creadoEn,
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
            idCandidato: isset($data['id_candidato']) && $data['id_candidato'] !== null && $data['id_candidato'] !== '' ? (int)$data['id_candidato'] : null,
            idUsuario: (int)($data['id_usuario'] ?? 0),
            nombres: (string)($data['nombres'] ?? ''),
            apellidos: (string)($data['apellidos'] ?? ''),
            idTipoDocumento: (int)($data['id_tipo_documento'] ?? 0),
            numeroDocumento: (string)($data['numero_documento'] ?? ''),
            telefono: (string)($data['telefono'] ?? ''),
            ciudad: (string)($data['ciudad'] ?? ''),
            direccion: isset($data['direccion']) && $data['direccion'] !== null ? (string)$data['direccion'] : null,
            creadoEn: isset($data['creado_en']) && $data['creado_en'] !== null ? (string)$data['creado_en'] : null,
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
