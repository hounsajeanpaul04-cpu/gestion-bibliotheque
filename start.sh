$lines = @(
    "#!/bin/sh",
    "php artisan config:clear",
    "php artisan migrate --force",
    "sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf",
    "sed -i 's/<VirtualHost \*:80>/<VirtualHost \*:10000>/' /etc/apache2/sites-enabled/000-default.conf",
    "apache2-foreground"
)
$content = $lines -join "`n"
[System.IO.File]::WriteAllText("D:\bibliotheque\gestion\start.sh", $content, [System.Text.UTF8Encoding]::new($false))