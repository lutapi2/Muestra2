<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'bitacora_integracion'.
 * Representa de forma inmutable un registro de la tabla 'bitacora_integracion'.
 */
final class BitacoraIntegracionVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idBitacora = null,
        public int $idIntegracion = 0,
        public ?array $payloadEnviado = null,
        public ?string $respuestaRecibida = null,
        public ?int $codigoHttp = null,
        public ?string $errorDetalle = null,
        public ?string $fechaIntento = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_bitacora' => $this->idBitacora,
            'id_integracion' => $this->idIntegracion,
            'payload_enviado' => $this->payloadEnviado,
            'respuesta_recibida' => $this->respuestaRecibida,
            'codigo_http' => $this->codigoHttp,
            'error_detalle' => $this->errorDetalle,
            'fecha_intento' => $this->fechaIntento,
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
            idBitacora: isset($data['id_bitacora']) && $data['id_bitacora'] !== null && $data['id_bitacora'] !== '' ? (int)$data['id_bitacora'] : null,
            idIntegracion: (int)($data['id_integracion'] ?? 0),
            payloadEnviado: isset($data['payload_enviado']) ? (is_array($data['payload_enviado']) ? $data['payload_enviado'] : json_decode((string)$data['payload_enviado'], true)) : null,
            respuestaRecibida: isset($data['respuesta_recibida']) && $data['respuesta_recibida'] !== null ? (string)$data['respuesta_recibida'] : null,
            codigoHttp: isset($data['codigo_http']) && $data['codigo_http'] !== null && $data['codigo_http'] !== '' ? (int)$data['codigo_http'] : null,
            errorDetalle: isset($data['error_detalle']) && $data['error_detalle'] !== null ? (string)$data['error_detalle'] : null,
            fechaIntento: isset($data['fecha_intento']) && $data['fecha_intento'] !== null ? (string)$data['fecha_intento'] : null,
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
