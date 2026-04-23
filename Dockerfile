FROM php:8.4-apache

RUN apt-get update && apt-get install -y git curl libpng-dev libjpeg-dev libfreetype6-dev libpq-dev libzip-dev zip unzip nodejs npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j4 gd pdo pdo_pgsql pgsql zip bcmath opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

RUN npm install && npm run build

RUN chown -R www-data:www-data /var/www/html && chmod -R 775 storage bootstrap/cache

RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

COPY start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 10000

CMD ["/start.sh"]