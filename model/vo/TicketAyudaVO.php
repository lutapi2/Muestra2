<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'ticket_ayuda'.
 * Representa de forma inmutable un registro de la tabla 'ticket_ayuda'.
 */
final class TicketAyudaVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idTicket = null,
        public int $idUsuario = 0,
        public string $categoria = '',
        public string $asunto = '',
        public string $mensaje = '',
        public string $estado = 'ABIERTO',
        public ?string $fechaCreacion = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_ticket' => $this->idTicket,
            'id_usuario' => $this->idUsuario,
            'categoria' => $this->categoria,
            'asunto' => $this->asunto,
            'mensaje' => $this->mensaje,
            'estado' => $this->estado,
            'fecha_creacion' => $this->fechaCreacion,
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
            idTicket: isset($data['id_ticket']) && $data['id_ticket'] !== null && $data['id_ticket'] !== '' ? (int)$data['id_ticket'] : null,
            idUsuario: (int)($data['id_usuario'] ?? 0),
            categoria: (string)($data['categoria'] ?? ''),
            asunto: (string)($data['asunto'] ?? ''),
            mensaje: (string)($data['mensaje'] ?? ''),
            estado: (string)($data['estado'] ?? ''),
            fechaCreacion: isset($data['fecha_creacion']) && $data['fecha_creacion'] !== null ? (string)$data['fecha_creacion'] : null,
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
