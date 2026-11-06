<?php
namespace App\Core;

class View {
    private static array $sharedData = [];

    public static function share(string $key, $value): void {
        self::$sharedData[$key] = $value;
    }

    public static function render(string $viewPath, array $data = [], string $layout = 'main'): void {
        $fullViewPath = VIEWS_PATH . '/' . str_replace('.', '/', $viewPath) . '.php';

        if (!file_exists($fullViewPath)) {
            die("Vista no encontrada: " . htmlspecialchars($fullViewPath));
        }

        // Combine shared data and specific data
        $mergedData = array_merge(self::$sharedData, $data);
        extract($mergedData);

        // Capture view content
        ob_start();
        require $fullViewPath;
        $content = ob_get_clean();

        if ($layout === 'none' || empty($layout)) {
            echo $content;
            return;
        }

        $layoutPath = VIEWS_PATH . '/layouts/' . $layout . '.php';
        if (file_exists($layoutPath)) {
            require $layoutPath;
        } else {
            echo $content;
        }
    }
}
