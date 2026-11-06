<?php
declare(strict_types=1);

namespace App\Enums;

enum EstadoFormalizacion: string {
    case PENDIENTE = 'pendiente';
    case EN_TRAMITE = 'en_tramite';
    case FIRMADO = 'firmado';
    case RECHAZADO = 'rechazado';
    case FORMALIZADO = 'formalizado';

    public function label(): string {
        return match($this) {
            self::PENDIENTE => 'Pendiente',
            self::EN_TRAMITE => 'En Trámite',
            self::FIRMADO => 'Firmado',
            self::RECHAZADO => 'Rechazado',
            self::FORMALIZADO => 'Formalizado',
        };
    }
}
