<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'solicitud_subsanacion'.
 * Representa de forma inmutable un registro de la tabla 'solicitud_subsanacion'.
 */
final class SolicitudSubsanacionVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idSolicitud = null,
        public int $idPostulacion = 0,
        public int $idSolicitante = 0,
        public ?string $fechaSolicitud = null,
        public string $fechaLimite = '',
        public ?string $fechaExtension = null,
        public string $estado = 'VIGENTE',
        public ?string $fechaRespuesta = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_solicitud' => $this->idSolicitud,
            'id_postulacion' => $this->idPostulacion,
            'id_solicitante' => $this->idSolicitante,
            'fecha_solicitud' => $this->fechaSolicitud,
            'fecha_limite' => $this->fechaLimite,
            'fecha_extension' => $this->fechaExtension,
            'estado' => $this->estado,
            'fecha_respuesta' => $this->fechaRespuesta,
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
            idSolicitud: isset($data['id_solicitud']) && $data['id_solicitud'] !== null && $data['id_solicitud'] !== '' ? (int)$data['id_solicitud'] : null,
            idPostulacion: (int)($data['id_postulacion'] ?? 0),
            idSolicitante: (int)($data['id_solicitante'] ?? 0),
            fechaSolicitud: isset($data['fecha_solicitud']) && $data['fecha_solicitud'] !== null ? (string)$data['fecha_solicitud'] : null,
            fechaLimite: (string)($data['fecha_limite'] ?? ''),
            fechaExtension: isset($data['fecha_extension']) && $data['fecha_extension'] !== null ? (string)$data['fecha_extension'] : null,
            estado: (string)($data['estado'] ?? ''),
            fechaRespuesta: isset($data['fecha_respuesta']) && $data['fecha_respuesta'] !== null ? (string)$data['fecha_respuesta'] : null,
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
