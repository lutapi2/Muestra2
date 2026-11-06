<?php
declare(strict_types=1);

namespace App\Enums;

enum EstadoDocumental: string {
    case PENDIENTE = 'pendiente';
    case CONFORME = 'conforme';
    case OBSERVADO = 'observado';
    case NO_CONFORME = 'no_conforme';

    public function label(): string {
        return match($this) {
            self::PENDIENTE => 'Pendiente',
            self::CONFORME => 'Conforme',
            self::OBSERVADO => 'Observado',
            self::NO_CONFORME => 'No Conforme',
        };
    }
}
