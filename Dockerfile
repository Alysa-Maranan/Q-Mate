# ============================================
# Q-MATE Laravel Dockerfile
# Laravel 12 + PHP 8.2 + Supabase PostgreSQL
# Python + TensorFlow Quail Breed Classifier
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

COPY . .

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --optimize-autoloader


# ============================================
# Stage 3: Production PHP + Nginx + Python
# ============================================
FROM php:8.2-fpm

# ============================================
# System Dependencies
# ============================================
RUN apt-get update \
    && apt-get install -y \
        nginx \
        supervisor \
        curl \
        git \
        unzip \
        python3 \
        python3-venv \
        python3-pip \
        python3-dev \
        build-essential \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libonig-dev \
        libzip-dev \
        libicu-dev \
        libpq-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
    && rm -rf /var/lib/apt/lists/*


# ============================================
# Remove Default Nginx Website
# ============================================
RUN rm -f /etc/nginx/sites-enabled/default


# ============================================
# Python Virtual Environment
# ============================================
RUN python3 -m venv /opt/qmate-venv

ENV PATH="/opt/qmate-venv/bin:$PATH"


# ============================================
# Upgrade Python Packaging Tools
# ============================================
RUN pip install \
        --no-cache-dir \
        --upgrade \
        pip \
        setuptools \
        wheel


# ============================================
# Laravel Application
# ============================================
WORKDIR /var/www/html

COPY --from=vendor /app /var/www/html


# ============================================
# Python Classifier Dependencies
# ============================================
COPY requirements.txt /tmp/requirements.txt

RUN pip install \
        --no-cache-dir \
        -r /tmp/requirements.txt


# ============================================
# Verify Python + TensorFlow Installation
# ============================================
RUN python --version \
    && python -c "import tensorflow as tf; print('TensorFlow:', tf.__version__)"


# ============================================
# Remove Local Laravel Cache
# ============================================
RUN rm -f /var/www/html/bootstrap/cache/*.php \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions


# ============================================
# Copy Vite Production Assets
# ============================================
COPY --from=assets /app/public/build \
    /var/www/html/public/build


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
    /etc/nginx/conf.d/default.conf


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