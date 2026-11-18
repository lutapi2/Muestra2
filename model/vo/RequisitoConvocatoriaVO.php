<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'requisito_convocatoria'.
 * Representa de forma inmutable un registro de la tabla 'requisito_convocatoria'.
 */
final class RequisitoConvocatoriaVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idRequisito = null,
        public int $idConvocatoria = 0,
        public int $idTipoItem = 0,
        public string $obligatoriedad = 'OBLIGATORIO',
        public string $soporteRequerido = 'OBLIGATORIO',
        public ?string $regla = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_requisito' => $this->idRequisito,
            'id_convocatoria' => $this->idConvocatoria,
            'id_tipo_item' => $this->idTipoItem,
            'obligatoriedad' => $this->obligatoriedad,
            'soporte_requerido' => $this->soporteRequerido,
            'regla' => $this->regla,
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
            idRequisito: isset($data['id_requisito']) && $data['id_requisito'] !== null && $data['id_requisito'] !== '' ? (int)$data['id_requisito'] : null,
            idConvocatoria: (int)($data['id_convocatoria'] ?? 0),
            idTipoItem: (int)($data['id_tipo_item'] ?? 0),
            obligatoriedad: (string)($data['obligatoriedad'] ?? ''),
            soporteRequerido: (string)($data['soporte_requerido'] ?? ''),
            regla: isset($data['regla']) && $data['regla'] !== null ? (string)$data['regla'] : null,
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
