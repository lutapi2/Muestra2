<?php
declare(strict_types=1);

namespace App\Enums;

enum EstadoPostulacion: string {
    case BORRADOR = 'borrador';
    case ENVIADA = 'enviada';
    case EN_REVISION = 'en_revision';
    case SUBSANACION_REQUERIDA = 'subsanacion_requerida';
    case HABILITADA = 'habilitada';
    case NO_HABILITADA = 'no_habilitada';
    case PRESELECCIONADA = 'preseleccionada';
    case DESCARTADA = 'descartada';
    case SELECCIONADA = 'seleccionada';
    case NO_SELECCIONADA = 'no_seleccionada';
    case EN_FORMALIZACION = 'en_formalizacion';
    case CONTRATADO = 'contratado';
    case VINCULADO = 'vinculado';
    case RETIRADA = 'retirada';

    public function label(): string {
        return match($this) {
            self::BORRADOR => 'Borrador',
            self::ENVIADA => 'Enviada',
            self::EN_REVISION => 'En Revisión',
            self::SUBSANACION_REQUERIDA => 'Subsanación Requerida',
            self::HABILITADA => 'Habilitada',
            self::NO_HABILITADA => 'No Habilitada',
            self::PRESELECCIONADA => 'Preseleccionada',
            self::DESCARTADA => 'Descartada',
            self::SELECCIONADA => 'Seleccionada',
            self::NO_SELECCIONADA => 'No Seleccionada',
            self::EN_FORMALIZACION => 'En Formalización',
            self::CONTRATADO => 'Contratado',
            self::VINCULADO => 'Vinculado',
            self::RETIRADA => 'Retirada',
        };
    }

    public function badgeColors(): array {
        return match($this) {
            self::BORRADOR, self::RETIRADA => ['bg' => '#f1f5f9', 'text' => '#475569', 'border' => '#cbd5e1'],
            self::ENVIADA, self::HABILITADA => ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'border' => '#bfdbfe'],
            self::EN_REVISION, self::EN_FORMALIZACION => ['bg' => '#fffbeb', 'text' => '#b45309', 'border' => '#fde68a'],
            self::SUBSANACION_REQUERIDA, self::DESCARTADA, self::NO_HABILITADA => ['bg' => '#fef2f2', 'text' => '#b91c1c', 'border' => '#fecaca'],
            self::PRESELECCIONADA => ['bg' => '#f5f3ff', 'text' => '#6d28d9', 'border' => '#ddd6fe'],
            self::SELECCIONADA, self::CONTRATADO, self::VINCULADO => ['bg' => '#f0fdf4', 'text' => '#15803d', 'border' => '#bbf7d0'],
            self::NO_SELECCIONADA => ['bg' => '#f8fafc', 'text' => '#64748b', 'border' => '#e2e8f0'],
        };
    }
}
