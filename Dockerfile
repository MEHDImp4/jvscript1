# Production-ready PHP-Apache image for the WA4E profile app
FROM php:8.2-apache

# Install PDO MySQL and enable Apache rewrite
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends libzip-dev; \
    docker-php-ext-install pdo_mysql; \
    a2enmod rewrite; \
    apt-get purge -y --auto-remove libzip-dev; \
    rm -rf /var/lib/apt/lists/*

# Copy source code
WORKDIR /var/www/html
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html

# Apache listens on 0.0.0.0:80 by default
EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD wget --no-verbose --tries=1 --spider http://127.0.0.1/ || exit 1
