<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'soporte'.
 * Representa de forma inmutable un registro de la tabla 'soporte'.
 */
final class SoporteVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idSoporte = null,
        public int $idVersionItem = 0,
        public string $nombreOriginal = '',
        public string $nombreAlmacenado = '',
        public string $mime = '',
        public int $tamanoBytes = 0,
        public string $hashSha256 = '',
        public string $rutaSegura = '',
        public string $estadoArchivo = 'VALIDO',
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
            'id_soporte' => $this->idSoporte,
            'id_version_item' => $this->idVersionItem,
            'nombre_original' => $this->nombreOriginal,
            'nombre_almacenado' => $this->nombreAlmacenado,
            'mime' => $this->mime,
            'tamano_bytes' => $this->tamanoBytes,
            'hash_sha256' => $this->hashSha256,
            'ruta_segura' => $this->rutaSegura,
            'estado_archivo' => $this->estadoArchivo,
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
            idSoporte: isset($data['id_soporte']) && $data['id_soporte'] !== null && $data['id_soporte'] !== '' ? (int)$data['id_soporte'] : null,
            idVersionItem: (int)($data['id_version_item'] ?? 0),
            nombreOriginal: (string)($data['nombre_original'] ?? ''),
            nombreAlmacenado: (string)($data['nombre_almacenado'] ?? ''),
            mime: (string)($data['mime'] ?? ''),
            tamanoBytes: (int)($data['tamano_bytes'] ?? 0),
            hashSha256: (string)($data['hash_sha256'] ?? ''),
            rutaSegura: (string)($data['ruta_segura'] ?? ''),
            estadoArchivo: (string)($data['estado_archivo'] ?? ''),
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
