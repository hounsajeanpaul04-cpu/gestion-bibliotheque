#!/bin/sh
php artisan config:clear
php artisan config:cache
php artisan migrate --force
sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf
sed -i 's/*:80>/*:10000>/' /etc/apache2/sites-enabled/000-default.conf
apache2-foreground