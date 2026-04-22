#!/bin/sh
php artisan config:clear
php artisan migrate --force
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/:80>/:${PORT}>/" /etc/apache2/sites-enabled/*.conf
apache2-foreground