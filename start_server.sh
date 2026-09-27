#!/bin/bash
# Clean Laravel Development Server Startup Script

cd "$(dirname "$0")"

# Ensure environment is loaded
export APP_ENV=local
export DB_CONNECTION=sqlite
export DB_DATABASE=./database/app.db
export APP_URL=http://127.0.0.1:8000

# Clear caches for fresh start
php artisan config:clear > /dev/null 2>&1
php artisan cache:clear > /dev/null 2>&1
php artisan route:clear > /dev/null 2>&1
php artisan view:clear > /dev/null 2>&1

# Start Laravel serve with proper stderr handling
exec php artisan serve --host=127.0.0.1 --port=8000
