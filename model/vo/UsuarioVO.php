<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'usuario'.
 * Representa de forma inmutable un registro de la tabla 'usuario'.
 */
final class UsuarioVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idUsuario = null,
        public string $correo = '',
        public string $contrasenaHash = '',
        public string $tipo = '',
        public string $nombreCompleto = '',
        public ?int $idDependencia = null,
        public string $estado = 'ACTIVO',
        public ?string $ultimoAcceso = null,
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
            'id_usuario' => $this->idUsuario,
            'correo' => $this->correo,
            'contrasena_hash' => $this->contrasenaHash,
            'tipo' => $this->tipo,
            'nombre_completo' => $this->nombreCompleto,
            'id_dependencia' => $this->idDependencia,
            'estado' => $this->estado,
            'ultimo_acceso' => $this->ultimoAcceso,
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
            idUsuario: isset($data['id_usuario']) && $data['id_usuario'] !== null && $data['id_usuario'] !== '' ? (int)$data['id_usuario'] : null,
            correo: (string)($data['correo'] ?? ''),
            contrasenaHash: (string)($data['contrasena_hash'] ?? ''),
            tipo: (string)($data['tipo'] ?? ''),
            nombreCompleto: (string)($data['nombre_completo'] ?? ''),
            idDependencia: isset($data['id_dependencia']) && $data['id_dependencia'] !== null && $data['id_dependencia'] !== '' ? (int)$data['id_dependencia'] : null,
            estado: (string)($data['estado'] ?? ''),
            ultimoAcceso: isset($data['ultimo_acceso']) && $data['ultimo_acceso'] !== null ? (string)$data['ultimo_acceso'] : null,
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
