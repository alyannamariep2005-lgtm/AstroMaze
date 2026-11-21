#!/bin/sh
set -e

# Railway tells us which port to listen on
PORT="${PORT:-8080}"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# A mounted Railway volume is owned by root; Apache runs as www-data
mkdir -p /var/www/html/uploads/profile_pictures
chown -R www-data:www-data /var/www/html/uploads

exec apache2-foreground
