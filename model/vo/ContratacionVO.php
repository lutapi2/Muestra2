<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'contratacion'.
 * Representa de forma inmutable un registro de la tabla 'contratacion'.
 */
final class ContratacionVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idContratacion = null,
        public int $idPostulacion = 0,
        public string $estadoFormalizacion = 'PENDIENTE',
        public ?string $fechaInicioLabores = null,
        public int $idResponsableGh = 0,
        public ?string $observaciones = null,
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
            'id_contratacion' => $this->idContratacion,
            'id_postulacion' => $this->idPostulacion,
            'estado_formalizacion' => $this->estadoFormalizacion,
            'fecha_inicio_labores' => $this->fechaInicioLabores,
            'id_responsable_gh' => $this->idResponsableGh,
            'observaciones' => $this->observaciones,
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
            idContratacion: isset($data['id_contratacion']) && $data['id_contratacion'] !== null && $data['id_contratacion'] !== '' ? (int)$data['id_contratacion'] : null,
            idPostulacion: (int)($data['id_postulacion'] ?? 0),
            estadoFormalizacion: (string)($data['estado_formalizacion'] ?? ''),
            fechaInicioLabores: isset($data['fecha_inicio_labores']) && $data['fecha_inicio_labores'] !== null ? (string)$data['fecha_inicio_labores'] : null,
            idResponsableGh: (int)($data['id_responsable_gh'] ?? 0),
            observaciones: isset($data['observaciones']) && $data['observaciones'] !== null ? (string)$data['observaciones'] : null,
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
