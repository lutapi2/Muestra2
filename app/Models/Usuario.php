<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Usuario {
    public static function findByEmail(string $correo): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT u.*, d.nombre as dependencia_nombre, d.facultad, d.sede, r.nombre as rol_nombre
            FROM usuarios u
            LEFT JOIN dependencias d ON u.dependencia_id = d.id
            LEFT JOIN roles r ON u.rol = r.id
            WHERE LOWER(u.correo) = LOWER(:correo) AND u.activo = 1
            LIMIT 1
        ");
        $stmt->execute([':correo' => trim($correo)]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function findById(int $id): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT u.*, d.nombre as dependencia_nombre, d.facultad, d.sede, r.nombre as rol_nombre
            FROM usuarios u
            LEFT JOIN dependencias d ON u.dependencia_id = d.id
            LEFT JOIN roles r ON u.rol = r.id
            WHERE u.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function registrarCandidato(array $datos): int {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO usuarios (nombres, apellidos, tipo_documento, numero_documento, correo, telefono, ciudad, direccion, password_hash, rol, activo, fecha_creacion)
            VALUES (:nombres, :apellidos, :tipo_documento, :numero_documento, :correo, :telefono, :ciudad, :direccion, :password_hash, 'candidato', 1, CURRENT_TIMESTAMP)
        ");
        $stmt->execute([
            ':nombres' => trim($datos['nombres']),
            ':apellidos' => trim($datos['apellidos']),
            ':tipo_documento' => $datos['tipo_documento'] ?? 'CC',
            ':numero_documento' => trim($datos['numero_documento']),
            ':correo' => strtolower(trim($datos['correo'])),
            ':telefono' => trim($datos['telefono'] ?? ''),
            ':ciudad' => trim($datos['ciudad'] ?? 'Sincelejo'),
            ':direccion' => trim($datos['direccion'] ?? ''),
            ':password_hash' => password_hash($datos['password'], PASSWORD_BCRYPT)
        ]);

        $userId = (int)$pdo->lastInsertId();

        // Registrar consentimiento inicial de tratamiento de datos (RF-015, HV-001)
        $consentStmt = $pdo->prepare("
            INSERT INTO consentimientos (usuario_id, finalidad, version_texto, ip_origen, fecha_hora)
            VALUES (:usuario_id, 'Tratamiento de Datos Personales Selección y Contratación CECAR', 'v3.0-2026', :ip, CURRENT_TIMESTAMP)
        ");
        $consentStmt->execute([
            ':usuario_id' => $userId,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);

        return $userId;
    }

    public static function getAll(string $rolFiltro = ''): array {
        $pdo = Database::getConnection();
        $sql = "
            SELECT u.*, d.nombre as dependencia_nombre, r.nombre as rol_nombre
            FROM usuarios u
            LEFT JOIN dependencias d ON u.dependencia_id = d.id
            LEFT JOIN roles r ON u.rol = r.id
        ";
        $params = [];
        if (!empty($rolFiltro)) {
            $sql .= " WHERE u.rol = :rol";
            $params[':rol'] = $rolFiltro;
        }
        $sql .= " ORDER BY u.fecha_creacion DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function actualizarUltimoAcceso(int $id): void {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE usuarios SET ultimo_acceso = CURRENT_TIMESTAMP WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
}
