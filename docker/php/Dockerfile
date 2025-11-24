FROM php:8.3-fpm

# System deps
RUN apt-get update && apt-get install -y \
    #It installs packages required by Laravel, PHP extensions, Composer, GD, etc.
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy Xdebug configuration
COPY ./docker/php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /var/www


# These are the PHP extensions Laravel commonly needs:
# | Extension | Purpose                                 |
# | --------- | --------------------------------------- |
# | pdo_mysql | MySQL database                          |
# | mbstring  | Multibyte strings (required by Laravel) |
# | exif      | Image metadata                          |
# | pcntl     | Async processes                         |
# | bcmath    | Large number operations                 |f
# | gd        | Image processing                        |
# | zip       | Used by Composer & storage              |

