# Image ini menggunakan PHP 8.2 FPM dengan Alpine Linux (lightweight)

FROM php:8.2-fpm-alpine

# Set working directory di dalam container
WORKDIR /var/www/html

# Install package yang dibutuhkan oleh Laravel dan PHP extensions
RUN apk add --no-cache \
    # Tools dasar
    git \
    curl \
    zip \
    unzip \
    # Library untuk PHP extensions
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    # Database client
    mysql-client \
    # Node.js untuk build frontend
    nodejs \
    npm

# Extensions yang dibutuhkan Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl

# Install Redis extension (untuk caching)
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Composer adalah dependency manager untuk PHP
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy file composer terlebih dahulu (untuk Docker layer caching)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --prefer-dist

# Copy file package.json untuk install Node dependencies
COPY package.json package-lock.json ./
RUN npm ci

# Copy semua file aplikasi ke dalam container
COPY . .

# Generate autoloader yang sudah dioptimasi
RUN composer dump-autoload --optimize

# Build frontend assets (CSS, JS)
RUN npm run build

# Laravel perlu write permission untuk storage dan cache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Script yang akan dijalankan saat container start
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port 9000 untuk PHP-FPM
EXPOSE 9000

# Jalankan entrypoint script dan PHP-FPM
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
