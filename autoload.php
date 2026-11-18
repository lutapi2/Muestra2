<?php

declare(strict_types=1);

/**
 * Autoloader PSR-4 para el proyecto Sistema de Selección CECAR.
 * Mapea:
 *   Config\      => config/
 *   Model\VO\    => model/vo/
 *   Model\DAO\   => model/dao/
 *   Controller\  => controller/
 */
spl_autoload_register(function (string $class): void {
    $prefixes = [
        'Config\\'     => __DIR__ . '/config/',
        'Model\\VO\\'   => __DIR__ . '/model/vo/',
        'Model\\DAO\\'  => __DIR__ . '/model/dao/',
        'Controller\\' => __DIR__ . '/controller/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
