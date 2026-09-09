#!/bin/sh

# Exécution des migrations en production (--force est obligatoire sur Render)
php artisan migrate --force

# Lancement d'Apache
exec apache2-foreground