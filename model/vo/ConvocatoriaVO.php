<?php

declare(strict_types=1);

namespace Model\VO;

/**
 * Value Object para la entidad 'convocatoria'.
 * Representa de forma inmutable un registro de la tabla 'convocatoria'.
 */
final class ConvocatoriaVO implements \JsonSerializable
{
    public function __construct(
        public ?int $idConvocatoria = null,
        public string $codigo = '',
        public string $cargo = '',
        public int $idDependencia = 0,
        public ?int $idFacultad = null,
        public ?int $idPrograma = null,
        public ?int $idSede = null,
        public ?int $idPeriodo = null,
        public int $vacantes = 1,
        public int $idModalidadContratacion = 0,
        public ?string $descripcion = null,
        public string $fechaApertura = '',
        public string $fechaCierre = '',
        public string $estado = 'BORRADOR',
        public ?string $fechaPublicacion = null,
        public ?string $motivoRetiro = null,
        public int $idCreador = 0,
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
            'id_convocatoria' => $this->idConvocatoria,
            'codigo' => $this->codigo,
            'cargo' => $this->cargo,
            'id_dependencia' => $this->idDependencia,
            'id_facultad' => $this->idFacultad,
            'id_programa' => $this->idPrograma,
            'id_sede' => $this->idSede,
            'id_periodo' => $this->idPeriodo,
            'vacantes' => $this->vacantes,
            'id_modalidad_contratacion' => $this->idModalidadContratacion,
            'descripcion' => $this->descripcion,
            'fecha_apertura' => $this->fechaApertura,
            'fecha_cierre' => $this->fechaCierre,
            'estado' => $this->estado,
            'fecha_publicacion' => $this->fechaPublicacion,
            'motivo_retiro' => $this->motivoRetiro,
            'id_creador' => $this->idCreador,
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
            idConvocatoria: isset($data['id_convocatoria']) && $data['id_convocatoria'] !== null && $data['id_convocatoria'] !== '' ? (int)$data['id_convocatoria'] : null,
            codigo: (string)($data['codigo'] ?? ''),
            cargo: (string)($data['cargo'] ?? ''),
            idDependencia: (int)($data['id_dependencia'] ?? 0),
            idFacultad: isset($data['id_facultad']) && $data['id_facultad'] !== null && $data['id_facultad'] !== '' ? (int)$data['id_facultad'] : null,
            idPrograma: isset($data['id_programa']) && $data['id_programa'] !== null && $data['id_programa'] !== '' ? (int)$data['id_programa'] : null,
            idSede: isset($data['id_sede']) && $data['id_sede'] !== null && $data['id_sede'] !== '' ? (int)$data['id_sede'] : null,
            idPeriodo: isset($data['id_periodo']) && $data['id_periodo'] !== null && $data['id_periodo'] !== '' ? (int)$data['id_periodo'] : null,
            vacantes: (int)($data['vacantes'] ?? 0),
            idModalidadContratacion: (int)($data['id_modalidad_contratacion'] ?? 0),
            descripcion: isset($data['descripcion']) && $data['descripcion'] !== null ? (string)$data['descripcion'] : null,
            fechaApertura: (string)($data['fecha_apertura'] ?? ''),
            fechaCierre: (string)($data['fecha_cierre'] ?? ''),
            estado: (string)($data['estado'] ?? ''),
            fechaPublicacion: isset($data['fecha_publicacion']) && $data['fecha_publicacion'] !== null ? (string)$data['fecha_publicacion'] : null,
            motivoRetiro: isset($data['motivo_retiro']) && $data['motivo_retiro'] !== null ? (string)$data['motivo_retiro'] : null,
            idCreador: (int)($data['id_creador'] ?? 0),
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
