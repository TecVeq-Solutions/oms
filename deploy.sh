#!/bin/bash
set -e

cd /home/u587253009/domains/tecveq.com/public_html/oms_app

echo "Pulling latest code..."
git pull origin main

echo "Syncing public files..."
cp -r public/* ../oms/
cp public/.htaccess ../oms/ 2>/dev/null || true
cp -r public/build ../oms/ 2>/dev/null || true

echo "Fixing Laravel public index paths..."
sed -i "s#require __DIR__.'/../vendor/autoload.php';#require __DIR__.'/../oms_app/vendor/autoload.php';#" ../oms/index.php || true
sed -i "s#\$app = require_once __DIR__.'/../bootstrap/app.php';#\$app = require_once __DIR__.'/../oms_app/bootstrap/app.php';#" ../oms/index.php || true

echo "Clearing and caching..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Setting permissions..."
chmod -R 775 storage bootstrap/cache

echo "Deployment completed."