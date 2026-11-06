<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Enums\EstadoPostulacion;

class FormatHelper {
    public static function fecha(?string $datetime, string $format = 'd/m/Y H:i'): string {
        if (empty($datetime)) return '—';
        $time = strtotime($datetime);
        return date($format, $time);
    }

    public static function fechaCorta(?string $datetime): string {
        return self::fecha($datetime, 'd/m/Y');
    }

    public static function moneda($monto): string {
        return '$ ' . number_format((float)$monto, 0, ',', '.');
    }

    public static function badgeEstado(string $estado): string {
        $key = strtolower(trim($estado));

        $enum = EstadoPostulacion::tryFrom($key);
        if ($enum) {
            $label = $enum->label();
            $colors = $enum->badgeColors();
        } else {
            $map = [
                'aprobado' => ['bg' => '#f0fdf4', 'text' => '#15803d', 'border' => '#bbf7d0', 'label' => 'Aprobado'],
                'en_proceso' => ['bg' => '#fffbeb', 'text' => '#b45309', 'border' => '#fde68a', 'label' => 'En proceso'],
                'firmado' => ['bg' => '#ecfdf5', 'text' => '#047857', 'border' => '#a7f3d0', 'label' => 'Firmado'],
                'pendiente' => ['bg' => '#fffbeb', 'text' => '#b45309', 'border' => '#fde68a', 'label' => 'Pendiente'],
                'observado' => ['bg' => '#fff7ed', 'text' => '#c2410c', 'border' => '#ffedd5', 'label' => 'Observado'],
                'rechazado' => ['bg' => '#fef2f2', 'text' => '#b91c1c', 'border' => '#fecaca', 'label' => 'Rechazado'],
                'conforme' => ['bg' => '#f0fdf4', 'text' => '#15803d', 'border' => '#bbf7d0', 'label' => 'Conforme'],
                'no_conforme' => ['bg' => '#fef2f2', 'text' => '#b91c1c', 'border' => '#fecaca', 'label' => 'No Conforme']
            ];
            $colors = $map[$key] ?? ['bg' => '#f8fafc', 'text' => '#475569', 'border' => '#e2e8f0', 'label' => ucfirst($estado)];
            $label = $colors['label'];
        }

        return sprintf(
            '<span class="badge" style="background-color: %s; color: %s; border: 1px solid %s; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.78rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;">%s</span>',
            $colors['bg'],
            $colors['text'],
            $colors['border'],
            htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
        );
    }
}
