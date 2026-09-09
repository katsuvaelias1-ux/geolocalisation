# Étape 1 : Installation des dépendances avec Composer
FROM composer:latest as build
WORKDIR /app
COPY . .
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# Étape 2 : Image principale PHP 8.2 avec Apache
FROM php:8.2-apache

# Installation des dépendances système, de libpq-dev (PostgreSQL) et des extensions PHP
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql pgsql gd zip

# Activation de mod_rewrite pour Apache
RUN a2enmod rewrite

# Copie des fichiers du projet
COPY --from=build /app /var/www/html

# Configuration de la racine Apache vers /public pour Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# Permissions système
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# Configuration du port dynamique Render
ENV PORT=8080
EXPOSE 8080
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

CMD ["apache2-foreground"]