# Étape 1 : Dépendances PHP via Composer
FROM composer:latest as vendor
WORKDIR /app
COPY database/ database/
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# Étape 2 : Compilation des assets frontend via Node.js / Vite
FROM node:18-alpine as frontend
WORKDIR /app
COPY package*.json vite.config.js ./
COPY resources/ resources/
RUN npm ci && npm run build

# Étape 3 : Image d'exécution finale PHP 8.2 + Apache
FROM php:8.2-apache

# Installation des extensions système et PHP
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql pgsql gd zip

# Activation de mod_rewrite d'Apache
RUN a2enmod rewrite

# Copie du code source du projet
COPY . /var/www/html

# Injection du dossier vendor et du dossier public/build compilé par Vite
COPY --from=vendor /app/vendor /var/www/html/vendor
COPY --from=frontend /app/public/build /var/www/html/public/build

# Point d'entrée Apache vers le dossier /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# Permissions système pour Apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# Configuration du script d'entrée
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENV PORT=8080
EXPOSE 8080
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]