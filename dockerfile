FROM php:8.2-fpm


RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


WORKDIR /var/www


COPY . .

RUN composer install --no-dev --optimize-autoloader


RUN chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache

CMD ["php-fpm"]
