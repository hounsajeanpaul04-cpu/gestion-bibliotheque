#!/bin/sh
cp /etc/secrets/.env /var/www/html/.env
php artisan config:clear
php artisan migrate --force
apache2-foreground