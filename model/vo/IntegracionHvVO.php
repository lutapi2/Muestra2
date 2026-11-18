<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'integracion_hv'.
 * Representa de forma inmutable un registro de la tabla 'integracion_hv'.
 */
final class IntegracionHvVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idIntegracion = null,
        public int $idPostulacion = 0,
        public string $sistemaDestino = 'SISTEMA_TALENTO_CECAR',
        public ?string $idExternoCreado = null,
        public string $estado = 'PENDIENTE',
        public ?string $fechaTransferencia = null,
        public int $reintentos = 0,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_integracion' => $this->idIntegracion,
            'id_postulacion' => $this->idPostulacion,
            'sistema_destino' => $this->sistemaDestino,
            'id_externo_creado' => $this->idExternoCreado,
            'estado' => $this->estado,
            'fecha_transferencia' => $this->fechaTransferencia,
            'reintentos' => $this->reintentos,
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
            idIntegracion: isset($data['id_integracion']) && $data['id_integracion'] !== null && $data['id_integracion'] !== '' ? (int)$data['id_integracion'] : null,
            idPostulacion: (int)($data['id_postulacion'] ?? 0),
            sistemaDestino: (string)($data['sistema_destino'] ?? ''),
            idExternoCreado: isset($data['id_externo_creado']) && $data['id_externo_creado'] !== null ? (string)$data['id_externo_creado'] : null,
            estado: (string)($data['estado'] ?? ''),
            fechaTransferencia: isset($data['fecha_transferencia']) && $data['fecha_transferencia'] !== null ? (string)$data['fecha_transferencia'] : null,
            reintentos: (int)($data['reintentos'] ?? 0),
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
