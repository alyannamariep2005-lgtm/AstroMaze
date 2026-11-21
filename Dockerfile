FROM php:8.2-apache

# Postgres driver (Supabase)
RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Production php.ini (keeps output buffering on, errors hidden) + app overrides
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php.ini $PHP_INI_DIR/conf.d/zz-astromaze.ini

COPY docker/apache.conf /etc/apache2/conf-available/astromaze.conf
RUN a2enconf astromaze

COPY . /var/www/html/

COPY docker/start.sh /usr/local/bin/start.sh
# Strip Windows line endings if the repo was committed from Windows
RUN sed -i 's/\r$//' /usr/local/bin/start.sh && chmod +x /usr/local/bin/start.sh

RUN mkdir -p /var/www/html/uploads/profile_pictures \
    && chown -R www-data:www-data /var/www/html/uploads

CMD ["start.sh"]
