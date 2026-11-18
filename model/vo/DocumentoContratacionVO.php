<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'documento_contratacion'.
 * Representa de forma inmutable un registro de la tabla 'documento_contratacion'.
 */
final class DocumentoContratacionVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idDocContratacion = null,
        public int $idContratacion = 0,
        public string $tipoDocumento = '',
        public string $rutaArchivo = '',
        public string $estadoVerificacion = 'PENDIENTE',
        public ?string $fechaCarga = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_doc_contratacion' => $this->idDocContratacion,
            'id_contratacion' => $this->idContratacion,
            'tipo_documento' => $this->tipoDocumento,
            'ruta_archivo' => $this->rutaArchivo,
            'estado_verificacion' => $this->estadoVerificacion,
            'fecha_carga' => $this->fechaCarga,
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
            idDocContratacion: isset($data['id_doc_contratacion']) && $data['id_doc_contratacion'] !== null && $data['id_doc_contratacion'] !== '' ? (int)$data['id_doc_contratacion'] : null,
            idContratacion: (int)($data['id_contratacion'] ?? 0),
            tipoDocumento: (string)($data['tipo_documento'] ?? ''),
            rutaArchivo: (string)($data['ruta_archivo'] ?? ''),
            estadoVerificacion: (string)($data['estado_verificacion'] ?? ''),
            fechaCarga: isset($data['fecha_carga']) && $data['fecha_carga'] !== null ? (string)$data['fecha_carga'] : null,
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
