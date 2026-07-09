FROM php:8.2-apache

# ─── PHP-Erweiterungen für MySQL installieren ───
RUN apt-get update && apt-get install -y \
    default-mysql-client \
    libssl-dev \
    && docker-php-ext-install pdo_mysql mysqli

# ─── Apache konfigurieren ───
COPY . /var/www/html/

# ─── 🔥 DocumentRoot auf den website-Ordner setzen ───
RUN sed -i 's!/var/www/html!/var/www/html/website!g' /etc/apache2/sites-available/000-default.conf

RUN chown -R www-data:www-data /var/www/html/
RUN a2enmod rewrite

# ─── Port 80 freigeben ───
EXPOSE 80

# ─── Apache starten ───
CMD ["apache2-foreground"]
