# ==============
# Build Stage
# ==============
FROM php:8.2-fpm AS build

# Install dependencies
RUN apt-get update && apt-get install -y \
    git zip unzip curl libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working dir
WORKDIR /var/www

# Copy all project
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Generate optimized Laravel caches
RUN php artisan config:clear || true
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

# ==============
# Production Stage (FPM + Nginx)
# ==============
FROM nginx:stable-alpine

# Copy Nginx config
COPY ./docker/nginx.conf /etc/nginx/conf.d/default.conf

# Copy app built from stage 1
COPY --from=build /var/www /var/www

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
