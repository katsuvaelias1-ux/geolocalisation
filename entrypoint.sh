#!/bin/sh

# Régénération des optimisations Laravel au démarrage du conteneur
php artisan package:discover --ansi
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Exécution des migrations PostgreSQL
php artisan migrate --force

# Démarrage d'Apache en premier plan
exec apache2-foreground