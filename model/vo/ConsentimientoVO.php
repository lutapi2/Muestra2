<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'consentimiento'.
 * Representa de forma inmutable un registro de la tabla 'consentimiento'.
 */
final class ConsentimientoVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idConsentimiento = null,
        public int $idCandidato = 0,
        public ?int $idPostulacion = null,
        public string $finalidad = '',
        public string $versionTexto = '',
        public bool $aceptado = false,
        public ?string $fechaHora = null,
        public ?string $ip = null,
        public ?string $revocadoEn = null,
    ) {}

    /**
     * Convierte el Value Object en un arreglo asociativo con los nombres de columna de la BD.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id_consentimiento' => $this->idConsentimiento,
            'id_candidato' => $this->idCandidato,
            'id_postulacion' => $this->idPostulacion,
            'finalidad' => $this->finalidad,
            'version_texto' => $this->versionTexto,
            'aceptado' => $this->aceptado,
            'fecha_hora' => $this->fechaHora,
            'ip' => $this->ip,
            'revocado_en' => $this->revocadoEn,
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
            idConsentimiento: isset($data['id_consentimiento']) && $data['id_consentimiento'] !== null && $data['id_consentimiento'] !== '' ? (int)$data['id_consentimiento'] : null,
            idCandidato: (int)($data['id_candidato'] ?? 0),
            idPostulacion: isset($data['id_postulacion']) && $data['id_postulacion'] !== null && $data['id_postulacion'] !== '' ? (int)$data['id_postulacion'] : null,
            finalidad: (string)($data['finalidad'] ?? ''),
            versionTexto: (string)($data['version_texto'] ?? ''),
            aceptado: isset($data['aceptado']) ? (bool)$data['aceptado'] : false,
            fechaHora: isset($data['fecha_hora']) && $data['fecha_hora'] !== null ? (string)$data['fecha_hora'] : null,
            ip: isset($data['ip']) && $data['ip'] !== null ? (string)$data['ip'] : null,
            revocadoEn: isset($data['revocado_en']) && $data['revocado_en'] !== null ? (string)$data['revocado_en'] : null,
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
