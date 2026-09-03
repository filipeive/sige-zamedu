#!/bin/bash
# scripts/smoke_test_rc1.sh
# Simple smoke test for RC-1 to verify basic application health
set -e

# Ensure dependencies are installed
composer install --no-interaction --prefer-dist
npm ci

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Run migrations (in testing environment)
php artisan migrate --env=testing --force

# Run a subset of quick tests
php artisan test --testsuite=Feature --filter=Smoke

# Check that the application can start without errors
php artisan serve --host=127.0.0.1 --port=8000 &
SERVER_PID=$!
sleep 5
# Perform a simple HTTP request
curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:8000 || { echo "Server not responding"; kill $SERVER_PID; exit 1; }
kill $SERVER_PID

echo "Smoke test completed successfully."
