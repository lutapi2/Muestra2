<?php
namespace App\Core;

class Request {
    private array $get;
    private array $post;
    private array $files;
    private array $server;

    public function __construct() {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->server = $_SERVER;
    }

    public function get(string $key, $default = null) {
        return $this->get[$key] ?? $default;
    }

    public function post(string $key, $default = null) {
        return $this->post[$key] ?? $default;
    }

    public function input(string $key, $default = null) {
        return $this->post[$key] ?? $this->get[$key] ?? $default;
    }

    public function all(): array {
        return array_merge($this->get, $this->post);
    }

    public function file(string $key) {
        return $this->files[$key] ?? null;
    }

    public function isPost(): bool {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    public function isAjax(): bool {
        return (!empty($this->server['HTTP_X_REQUESTED_WITH']) && 
                strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
               (isset($this->server['HTTP_ACCEPT']) && strpos($this->server['HTTP_ACCEPT'], 'application/json') !== false);
    }

    public function ip(): string {
        return $this->server['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function userAgent(): string {
        return $this->server['HTTP_USER_AGENT'] ?? 'Unknown';
    }

    public function getCsrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function validateCsrf(): bool {
        $token = $this->post('csrf_token') ?? $this->server['HTTP_X_CSRF_TOKEN'] ?? '';
        return !empty($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }
}
