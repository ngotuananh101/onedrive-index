# Docker file to deploy the application (Laravel) on a container

# Base image
FROM php:8.3-fpm-alpine

# installing system dependencies and php extensions
RUN apk add --no-cache \
    zip \
    libzip-dev \
    freetype \
    libjpeg-turbo \
    libpng \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    nodejs \
    npm \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip pdo pdo_mysql \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-enable gd

# Installing composer
COPY --from=composer:2.8.3 /usr/bin/composer /usr/bin/composer

# Setting working directory
WORKDIR /var/www/html

# Copying the application files
COPY . .

# install php and node.js dependencies
RUN composer install --no-dev --no-interaction --no-progress --no-suggest \
    && npm install \
    && npm run build

# Change Permissions
RUN chown -R www-data:www-data /var/www/html/vendor \
    && chmod -R 775 /var/www/html/vendor


# Running laravel on port 80
EXPOSE 80

# Running the application
CMD php artisan serve --host=0.0.0.0 --port=80
