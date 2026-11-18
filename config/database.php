<?php
/**
 * Configuración y Conexión de Base de Datos
 * Soporte PDO SQLite nativo y auto-migración
 */

return [
    'driver' => 'sqlite',
    'sqlite' => [
        'database' => STORAGE_PATH . '/database.sqlite',
    ],
    'mysql' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'database' => 'cecar_contratacion',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ]
];
