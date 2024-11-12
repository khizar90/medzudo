#!/bin/bash

# Enter medzudo directory
cd /var/www/medzudo/

sudo rm -r storage/logs/laravel.log

sudo npm install
sudo npm run build

# Install dependencies
export COMPOSER_ALLOW_SUPERUSER=1
sudo composer install --prefer-dist --no-dev -o --ignore-platform-reqs -d /var/www/medzudo/


# Migrate all tables
sudo php artisan migrate

# Clear any previous cached views
sudo php artisan cache:clear
sudo php artisan view:clear

# Optimize the application
sudo php artisan optimize

sudo chgrp -R www-data public storage bootstrap/cache
sudo chmod -R ug+rwx public storage bootstrap/cache
sudo chgrp -R 777 public storage bootstrap/cache
sudo chmod -R 777 public storage bootstrap/cache
sudo chmod -R 777 public/*
