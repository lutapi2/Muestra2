<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'parametro_sistema'.
 * Representa de forma inmutable un registro de la tabla 'parametro_sistema'.
 */
final class ParametroSistemaVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idParametro = null,
        public string $clave = '',
        public string $valor = '',
        public string $tipo = 'TEXTO',
        public ?string $descripcion = null,
        public string $fechaVigencia = '',
        public ?int $idActualizadoPor = null,
        public ?string $actualizadoEn = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_parametro' => $this->idParametro,
            'clave' => $this->clave,
            'valor' => $this->valor,
            'tipo' => $this->tipo,
            'descripcion' => $this->descripcion,
            'fecha_vigencia' => $this->fechaVigencia,
            'id_actualizado_por' => $this->idActualizadoPor,
            'actualizado_en' => $this->actualizadoEn,
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
            idParametro: isset($data['id_parametro']) && $data['id_parametro'] !== null && $data['id_parametro'] !== '' ? (int)$data['id_parametro'] : null,
            clave: (string)($data['clave'] ?? ''),
            valor: (string)($data['valor'] ?? ''),
            tipo: (string)($data['tipo'] ?? ''),
            descripcion: isset($data['descripcion']) && $data['descripcion'] !== null ? (string)$data['descripcion'] : null,
            fechaVigencia: (string)($data['fecha_vigencia'] ?? ''),
            idActualizadoPor: isset($data['id_actualizado_por']) && $data['id_actualizado_por'] !== null && $data['id_actualizado_por'] !== '' ? (int)$data['id_actualizado_por'] : null,
            actualizadoEn: isset($data['actualizado_en']) && $data['actualizado_en'] !== null ? (string)$data['actualizado_en'] : null,
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
