<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'perfil_cargo'.
 * Representa de forma inmutable un registro de la tabla 'perfil_cargo'.
 */
final class PerfilCargoVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idPerfil = null,
        public int $idConvocatoria = 0,
        public string $formacionRequerida = '',
        public string $experienciaRequerida = '',
        public ?string $competencias = null,
        public string $funciones = '',
        public ?string $condiciones = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_perfil' => $this->idPerfil,
            'id_convocatoria' => $this->idConvocatoria,
            'formacion_requerida' => $this->formacionRequerida,
            'experiencia_requerida' => $this->experienciaRequerida,
            'competencias' => $this->competencias,
            'funciones' => $this->funciones,
            'condiciones' => $this->condiciones,
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
            idPerfil: isset($data['id_perfil']) && $data['id_perfil'] !== null && $data['id_perfil'] !== '' ? (int)$data['id_perfil'] : null,
            idConvocatoria: (int)($data['id_convocatoria'] ?? 0),
            formacionRequerida: (string)($data['formacion_requerida'] ?? ''),
            experienciaRequerida: (string)($data['experiencia_requerida'] ?? ''),
            competencias: isset($data['competencias']) && $data['competencias'] !== null ? (string)$data['competencias'] : null,
            funciones: (string)($data['funciones'] ?? ''),
            condiciones: isset($data['condiciones']) && $data['condiciones'] !== null ? (string)$data['condiciones'] : null,
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
