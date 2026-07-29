FROM php:8.2-fpm


# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install \
        gd \
        pdo \
        pdo_mysql \
        mbstring \
        zip \
        opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*


# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


WORKDIR /var/www/html


# Fix git ownership issue
RUN git config --global --add safe.directory /var/www/html


# Copy composer files first
COPY composer.json composer.lock ./


# Install Laravel dependencies
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts


# Copy application files
COPY . .


# Run Laravel package discovery
RUN composer dump-autoload --optimize


# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

RUN chown -R www-data:www-data /var/www/html/storage \
    /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage \
    /var/www/html/bootstrap/cache


# Laravel environment
ENV APP_ENV=production


# PHP-FPM port
EXPOSE 9000


CMD ["php-fpm"]