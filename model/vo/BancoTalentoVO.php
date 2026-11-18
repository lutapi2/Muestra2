<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'banco_talento'.
 * Representa de forma inmutable un registro de la tabla 'banco_talento'.
 */
final class BancoTalentoVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idBanco = null,
        public int $idCandidato = 0,
        public int $idConsentimiento = 0,
        public string $fechaIngreso = '',
        public string $vigenciaHasta = '',
        public string $estado = 'VIGENTE',
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_banco' => $this->idBanco,
            'id_candidato' => $this->idCandidato,
            'id_consentimiento' => $this->idConsentimiento,
            'fecha_ingreso' => $this->fechaIngreso,
            'vigencia_hasta' => $this->vigenciaHasta,
            'estado' => $this->estado,
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
            idBanco: isset($data['id_banco']) && $data['id_banco'] !== null && $data['id_banco'] !== '' ? (int)$data['id_banco'] : null,
            idCandidato: (int)($data['id_candidato'] ?? 0),
            idConsentimiento: (int)($data['id_consentimiento'] ?? 0),
            fechaIngreso: (string)($data['fecha_ingreso'] ?? ''),
            vigenciaHasta: (string)($data['vigencia_hasta'] ?? ''),
            estado: (string)($data['estado'] ?? ''),
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
