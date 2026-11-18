#!/bin/bash
set -e

# Render expone la variable $PORT (usualmente 10000 o 80)
PORT=${PORT:-80}

echo "Configurando Apache para escuchar en el puerto: $PORT"
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf

# Asegurar permisos del almacenamiento
chown -R www-data:www-data /var/www/html/storage
chmod -R 777 /var/www/html/storage

# Inicializar base de datos y archivos semilla ejecutando un ciclo silencioso de arranque
php -r "
require '/var/www/html/config/app.php';
require '/var/www/html/app/cargar_clases.php';
try {
    Database::getConnection();
    FileHelper::asegurarArchivosSemilla();
    echo 'Base de datos y soportes inicializados correctamente.\n';
} catch (Throwable \$e) {
    echo 'Aviso de inicializacion: ' . \$e->getMessage() . '\n';
}
" || true

echo "Iniciando Apache en primer plano..."
exec apache2-foreground
