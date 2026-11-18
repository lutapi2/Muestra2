<?php

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

        // Colores y nombre que se muestran para cada estado
        $map = [
            // Estados de la postulacion
            'borrador' => ['bg' => '#f1f5f9', 'text' => '#475569', 'border' => '#cbd5e1', 'label' => 'Borrador'],
            'enviada' => ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'border' => '#bfdbfe', 'label' => 'Enviada'],
            'en_revision' => ['bg' => '#fffbeb', 'text' => '#b45309', 'border' => '#fde68a', 'label' => 'En Revisión'],
            'subsanacion_requerida' => ['bg' => '#fef2f2', 'text' => '#b91c1c', 'border' => '#fecaca', 'label' => 'Subsanación Requerida'],
            'habilitada' => ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'border' => '#bfdbfe', 'label' => 'Habilitada'],
            'no_habilitada' => ['bg' => '#fef2f2', 'text' => '#b91c1c', 'border' => '#fecaca', 'label' => 'No Habilitada'],
            'preseleccionada' => ['bg' => '#f5f3ff', 'text' => '#6d28d9', 'border' => '#ddd6fe', 'label' => 'Preseleccionada'],
            'descartada' => ['bg' => '#fef2f2', 'text' => '#b91c1c', 'border' => '#fecaca', 'label' => 'Descartada'],
            'seleccionada' => ['bg' => '#f0fdf4', 'text' => '#15803d', 'border' => '#bbf7d0', 'label' => 'Seleccionada'],
            'no_seleccionada' => ['bg' => '#f8fafc', 'text' => '#64748b', 'border' => '#e2e8f0', 'label' => 'No Seleccionada'],
            'en_formalizacion' => ['bg' => '#fffbeb', 'text' => '#b45309', 'border' => '#fde68a', 'label' => 'En Formalización'],
            'contratado' => ['bg' => '#f0fdf4', 'text' => '#15803d', 'border' => '#bbf7d0', 'label' => 'Contratado'],
            'vinculado' => ['bg' => '#f0fdf4', 'text' => '#15803d', 'border' => '#bbf7d0', 'label' => 'Vinculado'],
            'retirada' => ['bg' => '#f1f5f9', 'text' => '#475569', 'border' => '#cbd5e1', 'label' => 'Retirada'],
            'publicada' => ['bg' => '#f0fdf4', 'text' => '#15803d', 'border' => '#bbf7d0', 'label' => 'Publicada'],
            'cerrada' => ['bg' => '#fef2f2', 'text' => '#991b1b', 'border' => '#fecaca', 'label' => 'Cerrada'],
            'activa' => ['bg' => '#f0fdf4', 'text' => '#15803d', 'border' => '#bbf7d0', 'label' => 'Activa'],
            // Otros estados (documentos, formalizacion)
            'aprobado' => ['bg' => '#f0fdf4', 'text' => '#15803d', 'border' => '#bbf7d0', 'label' => 'Aprobado'],
            'en_proceso' => ['bg' => '#fffbeb', 'text' => '#b45309', 'border' => '#fde68a', 'label' => 'En proceso'],
            'firmado' => ['bg' => '#ecfdf5', 'text' => '#047857', 'border' => '#a7f3d0', 'label' => 'Firmado'],
            'pendiente' => ['bg' => '#fffbeb', 'text' => '#b45309', 'border' => '#fde68a', 'label' => 'Pendiente'],
            'observado' => ['bg' => '#fff7ed', 'text' => '#c2410c', 'border' => '#ffedd5', 'label' => 'Observado'],
            'rechazado' => ['bg' => '#fef2f2', 'text' => '#b91c1c', 'border' => '#fecaca', 'label' => 'Rechazado'],
            'conforme' => ['bg' => '#f0fdf4', 'text' => '#15803d', 'border' => '#bbf7d0', 'label' => 'Conforme'],
            'no_conforme' => ['bg' => '#fef2f2', 'text' => '#b91c1c', 'border' => '#fecaca', 'label' => 'No Conforme']
        ];

        if (isset($map[$key])) {
            $colors = $map[$key];
        } else {
            $colors = ['bg' => '#f8fafc', 'text' => '#475569', 'border' => '#e2e8f0', 'label' => ucfirst($estado)];
        }
        $label = $colors['label'];

        return '<span class="badge" style="background-color: ' . $colors['bg'] . '; color: ' . $colors['text']
            . '; border: 1px solid ' . $colors['border'] . '; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.78rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;">'
            . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>';
    }
}
