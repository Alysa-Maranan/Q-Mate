# ============================================
# Q-MATE Laravel Dockerfile
# Laravel 12 + PHP 8.2 + Supabase PostgreSQL
# ============================================


# ============================================
# Stage 1: Build Vite Assets
# ============================================
FROM node:22-alpine AS assets

WORKDIR /app

COPY package*.json ./

RUN npm install

COPY . .

RUN npm run build


# ============================================
# Stage 2: Install Composer Dependencies
# ============================================
FROM composer:2.7 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

# Copy the complete Laravel project first
# so artisan is available during Composer scripts
COPY . .

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --optimize-autoloader


# ============================================
# Stage 3: Production PHP + Nginx
# ============================================
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    git \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libzip-dev \
    icu-dev \
    postgresql-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl


# ============================================
# Laravel Application
# ============================================
WORKDIR /var/www/html

COPY --from=vendor /app /var/www/html

# Copy Vite production assets
COPY --from=assets /app/public/build /var/www/html/public/build


# ============================================
# Permissions
# ============================================
RUN chown -R www-data:www-data \
        /var/www/html/storage \
        /var/www/html/bootstrap/cache \
    && chmod -R 775 \
        /var/www/html/storage \
        /var/www/html/bootstrap/cache


# ============================================
# Nginx Configuration
# ============================================
COPY docker/nginx/default.conf \
    /etc/nginx/http.d/default.conf


# ============================================
# Supervisor Configuration
# ============================================
COPY docker/supervisor/supervisord.conf \
    /etc/supervisor/conf.d/supervisord.conf


# ============================================
# Laravel Production Settings
# ============================================
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr


# ============================================
# Render / HTTP
# ============================================
EXPOSE 80


# ============================================
# Startup Script
# ============================================
COPY docker/start.sh /start.sh

RUN chmod +x /start.sh


# ============================================
# Start Q-MATE
# ============================================
CMD ["/start.sh"]