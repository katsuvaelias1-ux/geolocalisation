# Étape 1 : Compilation des assets frontend avec Node.js / Vite
FROM node:18-alpine as frontend
WORKDIR /app
COPY . .
RUN npm install && npm run build

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

# Installation de Composer directement dans l'image PHP
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Activation de mod_rewrite pour Apache
RUN a2enmod rewrite

WORKDIR /var/www/html

# Copie du code source complet
COPY . .

# Injection du dossier public/build compilé par Vite
COPY --from=frontend /app/public/build /var/www/html/public/build

# Installation des dépendances Composer
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# Configuration de la racine Apache vers /public pour Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# Permissions pour les dossiers de stockage et de cache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# Copie et configuration du script de démarrage
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENV PORT=8080
EXPOSE 8080
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]