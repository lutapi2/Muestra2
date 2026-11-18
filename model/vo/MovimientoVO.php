<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'movimiento'.
 * Representa de forma inmutable un registro de la tabla 'movimiento'.
 */
final class MovimientoVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idMovimiento = null,
        public int $idPostulacion = 0,
        public int $idUsuario = 0,
        public string $accion = '',
        public ?string $estadoAnterior = null,
        public ?string $estadoNuevo = null,
        public ?int $idMotivo = null,
        public ?string $detalle = null,
        public bool $visibleCandidato = false,
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
            'id_movimiento' => $this->idMovimiento,
            'id_postulacion' => $this->idPostulacion,
            'id_usuario' => $this->idUsuario,
            'accion' => $this->accion,
            'estado_anterior' => $this->estadoAnterior,
            'estado_nuevo' => $this->estadoNuevo,
            'id_motivo' => $this->idMotivo,
            'detalle' => $this->detalle,
            'visible_candidato' => $this->visibleCandidato,
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
            idMovimiento: isset($data['id_movimiento']) && $data['id_movimiento'] !== null && $data['id_movimiento'] !== '' ? (int)$data['id_movimiento'] : null,
            idPostulacion: (int)($data['id_postulacion'] ?? 0),
            idUsuario: (int)($data['id_usuario'] ?? 0),
            accion: (string)($data['accion'] ?? ''),
            estadoAnterior: isset($data['estado_anterior']) && $data['estado_anterior'] !== null ? (string)$data['estado_anterior'] : null,
            estadoNuevo: isset($data['estado_nuevo']) && $data['estado_nuevo'] !== null ? (string)$data['estado_nuevo'] : null,
            idMotivo: isset($data['id_motivo']) && $data['id_motivo'] !== null && $data['id_motivo'] !== '' ? (int)$data['id_motivo'] : null,
            detalle: isset($data['detalle']) && $data['detalle'] !== null ? (string)$data['detalle'] : null,
            visibleCandidato: isset($data['visible_candidato']) ? (bool)$data['visible_candidato'] : false,
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
