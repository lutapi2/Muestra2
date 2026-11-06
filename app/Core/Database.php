<?php
namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $config = require ROOT_PATH . '/config/database.php';
            $driver = $config['driver'] ?? 'sqlite';

            if ($driver === 'sqlite') {
                $dbPath = $config['sqlite']['database'];
                $dir = dirname($dbPath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }

                $isNew = !file_exists($dbPath) || filesize($dbPath) === 0;

                try {
                    self::$instance = new PDO("sqlite:" . $dbPath);
                    self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                    self::$instance->exec("PRAGMA foreign_keys = ON;");

                    if ($isNew) {
                        self::initializeDatabase(self::$instance);
                    }
                } catch (PDOException $e) {
                    die("Error de conexión a base de datos SQLite: " . htmlspecialchars($e->getMessage()));
                }
            } else {
                // MySQL driver
                $mysql = $config['mysql'];
                $dsn = "mysql:host={$mysql['host']};port={$mysql['port']};dbname={$mysql['database']};charset={$mysql['charset']}";
                try {
                    self::$instance = new PDO($dsn, $mysql['username'], $mysql['password']);
                    self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    die("Error de conexión a base de datos MySQL: " . htmlspecialchars($e->getMessage()));
                }
            }
        }

        return self::$instance;
    }

    private static function initializeDatabase(PDO $pdo): void {
        $schemaPath = ROOT_PATH . '/database/schema.sql';
        $seedPath = ROOT_PATH . '/database/seed.sql';

        if (file_exists($schemaPath)) {
            $schemaSql = file_get_contents($schemaPath);
            $pdo->exec($schemaSql);
        }

        if (file_exists($seedPath)) {
            $seedSql = file_get_contents($seedPath);
            $pdo->exec($seedSql);
        }
    }
}
