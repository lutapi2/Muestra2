<?php
namespace App\Core;

class Response {
    public static function json($data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    public static function redirect(string $url): void {
        if (strpos($url, 'http') !== 0 && strpos($url, '/') !== 0) {
            $url = BASE_URL . '/index.php?' . ltrim($url, '?');
        }
        header("Location: " . $url);
        exit;
    }

    public static function download(string $filePath, string $filename, string $mimeType = 'application/octet-stream'): void {
        if (!file_exists($filePath)) {
            http_response_code(404);
            die("Archivo no encontrado.");
        }

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: inline; filename="' . basename($filename) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }
}
