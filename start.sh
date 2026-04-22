@"
#!/bin/sh
cp /etc/secrets/.env /var/www/html/.env
php artisan config:clear
php artisan migrate --force
php artisan tinker --execute="\App\Models\User::firstOrCreate(['email' => 'admin@bibliotheque.com'], ['name' => 'Admin', 'password' => bcrypt('password123'), 'is_admin' => true]);"
apache2-foreground
"@ | Set-Content -NoNewline -Encoding UTF8 start.sh