<?php
namespace App\Helpers;

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
        $map = [
            'borrador' => ['bg' => '#6B7280', 'text' => '#FFFFFF', 'label' => 'Borrador'],
            'enviada' => ['bg' => '#3B82F6', 'text' => '#FFFFFF', 'label' => 'Enviada'],
            'en_revision' => ['bg' => '#F59E0B', 'text' => '#1F2937', 'label' => 'En Revisión'],
            'subsanacion_requerida' => ['bg' => '#EF4444', 'text' => '#FFFFFF', 'label' => 'Subsanación Requerida'],
            'habilitada' => ['bg' => '#10B981', 'text' => '#FFFFFF', 'label' => 'Habilitada'],
            'no_habilitada' => ['bg' => '#DC2626', 'text' => '#FFFFFF', 'label' => 'No Habilitada'],
            'preseleccionada' => ['bg' => '#8B5CF6', 'text' => '#FFFFFF', 'label' => 'Preseleccionada'],
            'descartada' => ['bg' => '#991B1B', 'text' => '#FFFFFF', 'label' => 'Descartada'],
            'seleccionada' => ['bg' => '#059669', 'text' => '#FFFFFF', 'label' => 'Seleccionada'],
            'no_seleccionada' => ['bg' => '#6B7280', 'text' => '#FFFFFF', 'label' => 'No Seleccionada'],
            'en_formalizacion' => ['bg' => '#F59E0B', 'text' => '#FFFFFF', 'label' => 'En Formalización'],
            'contratado' => ['bg' => '#10B981', 'text' => '#FFFFFF', 'label' => 'Contratado'],
            'vinculado' => ['bg' => '#047857', 'text' => '#FFFFFF', 'label' => 'Vinculado'],
            'retirada' => ['bg' => '#4B5563', 'text' => '#FFFFFF', 'label' => 'Retirada'],

            // Generic workflow states matching screenshots
            'aprobado' => ['bg' => '#10B981', 'text' => '#FFFFFF', 'label' => 'Aprobado'],
            'en_proceso' => ['bg' => '#F59E0B', 'text' => '#FFFFFF', 'label' => 'En proceso'],
            'firmado' => ['bg' => '#059669', 'text' => '#FFFFFF', 'label' => 'Firmado'],
            'pendiente' => ['bg' => '#F59E0B', 'text' => '#FFFFFF', 'label' => 'Pendiente'],
            'observado' => ['bg' => '#F97316', 'text' => '#FFFFFF', 'label' => 'Observado'],
            'rechazado' => ['bg' => '#EF4444', 'text' => '#FFFFFF', 'label' => 'Rechazado']
        ];

        $key = strtolower(trim($estado));
        $item = $map[$key] ?? ['bg' => '#6B7280', 'text' => '#FFFFFF', 'label' => ucfirst($estado)];

        return sprintf(
            '<span class="badge" style="background-color: %s; color: %s; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; display: inline-block;">%s</span>',
            $item['bg'],
            $item['text'],
            htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8')
        );
    }
}
