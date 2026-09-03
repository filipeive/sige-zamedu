#!/bin/bash
# scripts/audit_rc1.sh
# Perform RC-1 audit: run tests, lint, and static analysis
set -e

echo "Running Composer install..."
composer install --no-interaction --prefer-dist

echo "Running npm install..."
npm ci

echo "Running PHP unit tests..."
php artisan test

echo "Running PHP lint..."
./vendor/bin/phpcs -p .

echo "Running PHPStan static analysis..."
./vendor/bin/phpstan analyse -c phpstan.neon

echo "Audit completed successfully."
