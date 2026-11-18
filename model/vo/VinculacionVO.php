<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'vinculacion'.
 * Representa de forma inmutable un registro de la tabla 'vinculacion'.
 */
final class VinculacionVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idVinculacion = null,
        public int $idContratacion = 0,
        public string $codigoColaborador = '',
        public string $fechaVinculacion = '',
        public string $estado = 'ACTIVO',
        public int $idAutorizadoPor = 0,
        public ?string $fechaRegistro = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_vinculacion' => $this->idVinculacion,
            'id_contratacion' => $this->idContratacion,
            'codigo_colaborador' => $this->codigoColaborador,
            'fecha_vinculacion' => $this->fechaVinculacion,
            'estado' => $this->estado,
            'id_autorizado_por' => $this->idAutorizadoPor,
            'fecha_registro' => $this->fechaRegistro,
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
            idVinculacion: isset($data['id_vinculacion']) && $data['id_vinculacion'] !== null && $data['id_vinculacion'] !== '' ? (int)$data['id_vinculacion'] : null,
            idContratacion: (int)($data['id_contratacion'] ?? 0),
            codigoColaborador: (string)($data['codigo_colaborador'] ?? ''),
            fechaVinculacion: (string)($data['fecha_vinculacion'] ?? ''),
            estado: (string)($data['estado'] ?? ''),
            idAutorizadoPor: (int)($data['id_autorizado_por'] ?? 0),
            fechaRegistro: isset($data['fecha_registro']) && $data['fecha_registro'] !== null ? (string)$data['fecha_registro'] : null,
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
