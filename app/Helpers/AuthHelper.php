<?php
namespace App\Helpers;

use App\Core\Response;

class AuthHelper {
    public static function initSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check session expiration (RNF-012)
        if (isset($_SESSION['ultimo_acceso'])) {
            $inactivo = time() - $_SESSION['ultimo_acceso'];
            if ($inactivo > SESSION_TIMEOUT_SECONDS) {
                self::logout();
                self::setFlash('warning', 'Su sesión ha expirado por inactividad. Por favor inicie sesión nuevamente.');
                Response::redirect('c=Auth&a=login');
            }
        }
        $_SESSION['ultimo_acceso'] = time();
    }

    public static function estaAutenticado(): bool {
        self::initSession();
        return !empty($_SESSION['usuario_id']);
    }

    public static function getUsuarioActual(): ?array {
        self::initSession();
        return $_SESSION['usuario'] ?? null;
    }

    public static function getUsuarioId(): ?int {
        self::initSession();
        return $_SESSION['usuario_id'] ?? null;
    }

    public static function getRol(): ?string {
        self::initSession();
        return $_SESSION['usuario']['rol'] ?? null;
    }

    public static function getRolNombre(): string {
        $roles = [
            'admin' => 'Administrador',
            'talento_humano' => 'Talento Humano',
            'revisor' => 'Revisor Documental',
            'entrevistador' => 'Evaluador / Comité',
            'desarrollo_profesoral' => 'Desarrollo Profesoral',
            'candidato' => 'Aspirante / Candidato',
            'auditor' => 'Auditor'
        ];
        $rol = self::getRol();
        return $roles[$rol] ?? ucfirst($rol ?? 'Invitado');
    }

    public static function getNombreCompleto(): string {
        self::initSession();
        if (empty($_SESSION['usuario'])) {
            return 'Invitado';
        }
        return $_SESSION['usuario']['nombres'] . ' ' . $_SESSION['usuario']['apellidos'];
    }

    public static function getCargo(): string {
        self::initSession();
        return $_SESSION['usuario']['cargo_interno'] ?? 'Candidato';
    }

    public static function getDependenciaNombre(): string {
        self::initSession();
        return $_SESSION['usuario']['dependencia_nombre'] ?? 'CECAR - Sincelejo';
    }

    public static function tieneRol(array $roles): bool {
        $rol = self::getRol();
        if (!$rol) return false;
        if ($rol === 'admin') return true; // Superadmin has access
        return in_array($rol, $roles);
    }

    public static function esAdmin(): bool {
        return self::getRol() === 'admin';
    }

    public static function esTalentoHumano(): bool {
        return in_array(self::getRol(), ['admin', 'talento_humano']);
    }

    public static function esRevisor(): bool {
        return in_array(self::getRol(), ['admin', 'talento_humano', 'revisor']);
    }

    public static function esCandidato(): bool {
        return self::getRol() === 'candidato';
    }

    public static function exigirAutenticacion(): void {
        if (!self::estaAutenticado()) {
            self::setFlash('info', 'Debe iniciar sesión para acceder al módulo solicitado.');
            Response::redirect('c=Auth&a=login');
        }
    }

    public static function exigirRol(array $roles): void {
        self::exigirAutenticacion();
        if (!self::tieneRol($roles)) {
            self::setFlash('danger', 'Acceso denegado: No cuenta con permisos suficientes para esta sección (RNF-010).');
            Response::redirect('c=Dashboard&a=index');
        }
    }

    public static function login(array $usuario): void {
        self::initSession();
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = (int)$usuario['id'];
        $_SESSION['usuario'] = $usuario;
        $_SESSION['ultimo_acceso'] = time();
    }

    public static function logout(): void {
        self::initSession();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    public static function setFlash(string $tipo, string $mensaje): void {
        self::initSession();
        if (!isset($_SESSION['flashes'])) {
            $_SESSION['flashes'] = [];
        }
        $_SESSION['flashes'][] = [
            'tipo' => $tipo, // success, info, warning, danger
            'mensaje' => $mensaje
        ];
    }

    public static function getFlashes(): array {
        self::initSession();
        $flashes = $_SESSION['flashes'] ?? [];
        $_SESSION['flashes'] = [];
        return $flashes;
    }
}
