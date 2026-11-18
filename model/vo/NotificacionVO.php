<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'notificacion'.
 * Representa de forma inmutable un registro de la tabla 'notificacion'.
 */
final class NotificacionVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idNotificacion = null,
        public int $idDestinatario = 0,
        public ?int $idPostulacion = null,
        public string $canal = 'PORTAL',
        public string $asunto = '',
        public string $mensaje = '',
        public ?string $fechaCreacion = null,
        public ?string $leidaEn = null,
        public string $estadoEnvio = 'ENVIADA',
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_notificacion' => $this->idNotificacion,
            'id_destinatario' => $this->idDestinatario,
            'id_postulacion' => $this->idPostulacion,
            'canal' => $this->canal,
            'asunto' => $this->asunto,
            'mensaje' => $this->mensaje,
            'fecha_creacion' => $this->fechaCreacion,
            'leida_en' => $this->leidaEn,
            'estado_envio' => $this->estadoEnvio,
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
            idNotificacion: isset($data['id_notificacion']) && $data['id_notificacion'] !== null && $data['id_notificacion'] !== '' ? (int)$data['id_notificacion'] : null,
            idDestinatario: (int)($data['id_destinatario'] ?? 0),
            idPostulacion: isset($data['id_postulacion']) && $data['id_postulacion'] !== null && $data['id_postulacion'] !== '' ? (int)$data['id_postulacion'] : null,
            canal: (string)($data['canal'] ?? ''),
            asunto: (string)($data['asunto'] ?? ''),
            mensaje: (string)($data['mensaje'] ?? ''),
            fechaCreacion: isset($data['fecha_creacion']) && $data['fecha_creacion'] !== null ? (string)$data['fecha_creacion'] : null,
            leidaEn: isset($data['leida_en']) && $data['leida_en'] !== null ? (string)$data['leida_en'] : null,
            estadoEnvio: (string)($data['estado_envio'] ?? ''),
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
