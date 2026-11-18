<?php

declare(strict_types=1);

namespace Config;

use PDO;
use PDOException;

/**
 * Gestor de conexión centralizada a la base de datos relacional MySQL 8.0+
 * Aplica los requisitos RNF-011, RNF-022, RNF-024 y RNF-025 (Zona horaria America/Bogota).
 */
final class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}

    /**
     * Obtiene una instancia única (Singleton) de PDO configurada de forma segura.
     *
     * @return PDO
     * @throws PDOException si ocurre un error de conexión
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $host = getenv('DB_HOST') !== false ? getenv('DB_HOST') : '127.0.0.1';
            $port = getenv('DB_PORT') !== false ? getenv('DB_PORT') : '3306';
            $dbName = getenv('DB_NAME') !== false ? getenv('DB_NAME') : 'seleccion_personal_cecar';
            $user = getenv('DB_USER') !== false ? getenv('DB_USER') : 'root';
            $pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

            $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, // Desactiva emulación para usar consultas preparadas nativas de MySQL
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci, time_zone = '-05:00'",
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                // Registrar error sin filtrar credenciales sensibles al usuario final (RNF-024)
                throw new PDOException("Error de conexión con la base de datos institucional: " . $e->getMessage(), (int)$e->getCode());
            }
        }

        return self::$instance;
    }

    /**
     * Permite reiniciar la conexión (útil en pruebas unitarias y de integración).
     */
    public static function resetConnection(): void
    {
        self::$instance = null;
    }
}
