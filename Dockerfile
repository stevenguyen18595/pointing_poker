FROM php:8.3-fpm

# Install system dependencies and Node.js
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev nginx supervisor \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . /var/www

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies and build frontend
RUN npm ci && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Copy nginx config
COPY docker/nginx/default.conf /etc/nginx/sites-available/default

# Copy supervisor config
RUN echo '[supervisord]\n\
nodaemon=true\n\
\n\
[program:php-fpm]\n\
command=php-fpm\n\
autostart=true\n\
autorestart=true\n\
\n\
[program:nginx]\n\
command=nginx -g "daemon off;"\n\
autostart=true\n\
autorestart=true' > /etc/supervisor/conf.d/supervisord.conf

# Expose port
EXPOSE 8080

# Update nginx to listen on 8080
RUN sed -i 's/listen 80;/listen 8080;/g' /etc/nginx/sites-available/default

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
