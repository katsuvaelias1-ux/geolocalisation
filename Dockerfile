# Image principale PHP 8.2 avec Apache
FROM php:8.2-apache

# Installation des dépendances système et des extensions PHP nécessaires (PostgreSQL + MySQL + GD)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql pgsql gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copie de l'exécutable Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Activation du module d'écriture d'URL Apache
RUN a2enmod rewrite

# Le formulaire artisan peut contenir deux images (4 Mo + 6 Mo).
RUN { \
        echo 'upload_max_filesize=6M'; \
        echo 'post_max_size=16M'; \
        echo 'max_file_uploads=5'; \
        echo 'max_execution_time=120'; \
        echo 'max_input_time=120'; \
    } > /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /var/www/html

# Copie complète du projet (incluant le dossier public/build)
COPY . .

# Installation des dépendances PHP en désactivant les scripts automatiques pour éviter les erreurs de découverte de paquets
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-scripts

# Configuration du VHost Apache pour pointer vers /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# Fixation des permissions pour le serveur web
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# Configuration du script d'entrée
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Configuration du port dynamique sur Render
ENV PORT=8080
EXPOSE 8080
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
