<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Sistema de Selección de Personal CECAR v3.0 - Corporación Universitaria del Caribe">
  <title><?= htmlspecialchars($pageTitle ?? 'Sistema de Selección de Personal - CECAR') ?></title>
  
  <!-- Hojas de Estilo Institucionales -->
  <?php
    $baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
    if ($baseUrl === '/' || $baseUrl === '\\') $baseUrl = '';
  ?>
  <meta name="base-url" content="<?= $baseUrl ?>">
  <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/variables.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/main.css">
</head>
<body>
  <!-- Enlace de accesibilidad para lectores de pantalla -->
  <a href="#main-content" class="skip-link">Saltar al contenido principal</a>
