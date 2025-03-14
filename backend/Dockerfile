FROM php:8.2-fpm

# อัปเดต package list และติดตั้ง dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ติดตั้ง Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ตั้ง working directory และ copy ไฟล์
WORKDIR /var/www
COPY . /var/www

# ติดตั้ง dependencies ด้วย Composer
RUN composer install --optimize-autoloader --no-dev

# ตั้ง permission
RUN chown -R www-data:www-data /var/www \
    && chmod 755 /var/www

# รัน PHP-FPM
CMD ["php-fpm"]