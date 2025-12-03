FROM php:8.3-fpm

# Install system dependencies and Node.js
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev libpq-dev nginx supervisor \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy all application files first
COPY . /var/www

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Install Node dependencies
RUN npm ci --verbose

# Build frontend assets
RUN npm run build

# Run composer scripts after everything is in place
RUN composer run-script post-autoload-dump

# Verify build was successful
RUN if [ ! -f "/var/www/public/build/manifest.json" ]; then \
        echo "ERROR: Build failed - manifest.json not found" && \
        exit 1; \
    fi && \
    echo "Build successful - manifest.json found:" && \
    ls -la /var/www/public/build/ && \
    head -5 /var/www/public/build/manifest.json

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/public

# Copy nginx config for production (nginx + PHP-FPM in same container)
COPY docker/nginx/default.prod.conf /etc/nginx/sites-available/default

# Create PHP-FPM pool directory and configure TCP listening
RUN mkdir -p /etc/php/8.3/fpm/pool.d && \
    echo '[www]\n\
user = www-data\n\
group = www-data\n\
listen = 127.0.0.1:9000\n\
listen.owner = www-data\n\
listen.group = www-data\n\
pm = dynamic\n\
pm.max_children = 5\n\
pm.start_servers = 2\n\
pm.min_spare_servers = 1\n\
pm.max_spare_servers = 3' > /etc/php/8.3/fpm/pool.d/www.conf

# Copy supervisor config
RUN echo '[supervisord]\n\
nodaemon=true\n\
user=root\n\
\n\
[program:php-fpm]\n\
command=php-fpm -F -R\n\
autostart=true\n\
autorestart=true\n\
stdout_logfile=/var/log/supervisor/php-fpm.log\n\
stderr_logfile=/var/log/supervisor/php-fpm.log\n\
\n\
[program:nginx]\n\
command=nginx -g "daemon off;"\n\
autostart=true\n\
autorestart=true\n\
stdout_logfile=/var/log/supervisor/nginx.log\n\
stderr_logfile=/var/log/supervisor/nginx.log' > /etc/supervisor/conf.d/supervisord.conf

# Create log directory for supervisor
RUN mkdir -p /var/log/supervisor

# Expose port
EXPOSE 8080

# Create entrypoint script to run migrations before starting services
RUN echo '#!/bin/sh\n\
echo "Running database migrations..."\n\
php artisan migrate --force\n\
echo "Seeding database..."\n\
php artisan db:seed --force\n\
echo "Starting services..."\n\
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf' > /entrypoint.sh && \
    chmod +x /entrypoint.sh

# Start with entrypoint
CMD ["/entrypoint.sh"]
