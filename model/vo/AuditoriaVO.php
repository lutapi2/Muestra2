<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'auditoria'.
 * Representa de forma inmutable un registro de la tabla 'auditoria'.
 */
final class AuditoriaVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idAuditoria = null,
        public ?int $idUsuario = null,
        public string $accion = '',
        public string $entidad = '',
        public ?int $idEntidad = null,
        public ?array $datosAntes = null,
        public ?array $datosDespues = null,
        public ?string $ip = null,
        public ?string $fechaHora = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_auditoria' => $this->idAuditoria,
            'id_usuario' => $this->idUsuario,
            'accion' => $this->accion,
            'entidad' => $this->entidad,
            'id_entidad' => $this->idEntidad,
            'datos_antes' => $this->datosAntes,
            'datos_despues' => $this->datosDespues,
            'ip' => $this->ip,
            'fecha_hora' => $this->fechaHora,
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
            idAuditoria: isset($data['id_auditoria']) && $data['id_auditoria'] !== null && $data['id_auditoria'] !== '' ? (int)$data['id_auditoria'] : null,
            idUsuario: isset($data['id_usuario']) && $data['id_usuario'] !== null && $data['id_usuario'] !== '' ? (int)$data['id_usuario'] : null,
            accion: (string)($data['accion'] ?? ''),
            entidad: (string)($data['entidad'] ?? ''),
            idEntidad: isset($data['id_entidad']) && $data['id_entidad'] !== null && $data['id_entidad'] !== '' ? (int)$data['id_entidad'] : null,
            datosAntes: isset($data['datos_antes']) ? (is_array($data['datos_antes']) ? $data['datos_antes'] : json_decode((string)$data['datos_antes'], true)) : null,
            datosDespues: isset($data['datos_despues']) ? (is_array($data['datos_despues']) ? $data['datos_despues'] : json_decode((string)$data['datos_despues'], true)) : null,
            ip: isset($data['ip']) && $data['ip'] !== null ? (string)$data['ip'] : null,
            fechaHora: isset($data['fecha_hora']) && $data['fecha_hora'] !== null ? (string)$data['fecha_hora'] : null,
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
