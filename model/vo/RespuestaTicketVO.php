<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'respuesta_ticket'.
 * Representa de forma inmutable un registro de la tabla 'respuesta_ticket'.
 */
final class RespuestaTicketVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idRespuesta = null,
        public int $idTicket = 0,
        public int $idUsuario = 0,
        public string $mensaje = '',
        public ?string $fecha = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_respuesta' => $this->idRespuesta,
            'id_ticket' => $this->idTicket,
            'id_usuario' => $this->idUsuario,
            'mensaje' => $this->mensaje,
            'fecha' => $this->fecha,
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
            idRespuesta: isset($data['id_respuesta']) && $data['id_respuesta'] !== null && $data['id_respuesta'] !== '' ? (int)$data['id_respuesta'] : null,
            idTicket: (int)($data['id_ticket'] ?? 0),
            idUsuario: (int)($data['id_usuario'] ?? 0),
            mensaje: (string)($data['mensaje'] ?? ''),
            fecha: isset($data['fecha']) && $data['fecha'] !== null ? (string)$data['fecha'] : null,
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
