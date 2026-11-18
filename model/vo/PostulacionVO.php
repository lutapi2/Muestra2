<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'postulacion'.
 * Representa de forma inmutable un registro de la tabla 'postulacion'.
 */
final class PostulacionVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idPostulacion = null,
        public int $idCandidato = 0,
        public int $idConvocatoria = 0,
        public ?int $idEtapaActual = null,
        public string $estado = 'BORRADOR',
        public string $resultadoDocumental = 'PENDIENTE',
        public int $version = 1,
        public ?string $fechaInicio = null,
        public ?string $fechaEnvio = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_postulacion' => $this->idPostulacion,
            'id_candidato' => $this->idCandidato,
            'id_convocatoria' => $this->idConvocatoria,
            'id_etapa_actual' => $this->idEtapaActual,
            'estado' => $this->estado,
            'resultado_documental' => $this->resultadoDocumental,
            'version' => $this->version,
            'fecha_inicio' => $this->fechaInicio,
            'fecha_envio' => $this->fechaEnvio,
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
            idPostulacion: isset($data['id_postulacion']) && $data['id_postulacion'] !== null && $data['id_postulacion'] !== '' ? (int)$data['id_postulacion'] : null,
            idCandidato: (int)($data['id_candidato'] ?? 0),
            idConvocatoria: (int)($data['id_convocatoria'] ?? 0),
            idEtapaActual: isset($data['id_etapa_actual']) && $data['id_etapa_actual'] !== null && $data['id_etapa_actual'] !== '' ? (int)$data['id_etapa_actual'] : null,
            estado: (string)($data['estado'] ?? ''),
            resultadoDocumental: (string)($data['resultado_documental'] ?? ''),
            version: (int)($data['version'] ?? 0),
            fechaInicio: isset($data['fecha_inicio']) && $data['fecha_inicio'] !== null ? (string)$data['fecha_inicio'] : null,
            fechaEnvio: isset($data['fecha_envio']) && $data['fecha_envio'] !== null ? (string)$data['fecha_envio'] : null,
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
