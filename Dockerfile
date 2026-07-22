FROM php:8.2-fpm

RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev zip git redis && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo pdo_mysql

WORKDIR /var/www/html

COPY . .

RUN chown -R www-data:www-data /var/www/html

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

RUN composer install --no-interaction --optimize-autoloader --prefer-dist

EXPOSE 9050

CMD ["php-fpm"]
