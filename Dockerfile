FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends ca-certificates default-mysql-client \
    && docker-php-ext-install pdo_mysql mysqli \
    && a2enmod headers rewrite expires \
    && rm -rf /var/lib/apt/lists/*

COPY . /var/www/html/
COPY docker/entrypoint.sh /usr/local/bin/dartsystem-entrypoint

RUN chmod +x /usr/local/bin/dartsystem-entrypoint \
    && chown -R www-data:www-data /var/www/html \
    && printf 'ServerName dartsystem.alessiohennecke.de\n' > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername

ENV PORT=10000
EXPOSE 10000

ENTRYPOINT ["/usr/local/bin/dartsystem-entrypoint"]
