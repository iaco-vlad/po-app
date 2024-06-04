#!/bin/bash

if [ ! -f /application/.env ]; then
    cp /application/.env.example /application/.env
fi

echo "Installing Composer dependencies..."
composer install

echo "Running migrations..."
php artisan migrate

echo "Application is now up and running."

php-fpm -D
tail -f /dev/null
