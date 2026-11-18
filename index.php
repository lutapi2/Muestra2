<?php

declare(strict_types=1);

/**
 * Redirección automática al Front Controller público para despliegues locales (ej. Apache / XAMPP).
 */
header('Location: public/');
exit;
