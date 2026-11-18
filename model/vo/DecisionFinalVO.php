<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'decision_final'.
 * Representa de forma inmutable un registro de la tabla 'decision_final'.
 */
final class DecisionFinalVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idDecision = null,
        public int $idPostulacion = 0,
        public string $resultado = '',
        public string $fundamento = '',
        public int $idUsuario = 0,
        public ?string $fechaDecision = null,
        public ?string $fechaPublicacion = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_decision' => $this->idDecision,
            'id_postulacion' => $this->idPostulacion,
            'resultado' => $this->resultado,
            'fundamento' => $this->fundamento,
            'id_usuario' => $this->idUsuario,
            'fecha_decision' => $this->fechaDecision,
            'fecha_publicacion' => $this->fechaPublicacion,
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
            idDecision: isset($data['id_decision']) && $data['id_decision'] !== null && $data['id_decision'] !== '' ? (int)$data['id_decision'] : null,
            idPostulacion: (int)($data['id_postulacion'] ?? 0),
            resultado: (string)($data['resultado'] ?? ''),
            fundamento: (string)($data['fundamento'] ?? ''),
            idUsuario: (int)($data['id_usuario'] ?? 0),
            fechaDecision: isset($data['fecha_decision']) && $data['fecha_decision'] !== null ? (string)$data['fecha_decision'] : null,
            fechaPublicacion: isset($data['fecha_publicacion']) && $data['fecha_publicacion'] !== null ? (string)$data['fecha_publicacion'] : null,
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
