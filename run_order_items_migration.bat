@echo off
echo Running migration for order_items table...
php artisan migrate --path=database/migrations/2026_04_20_000000_create_order_items_table.php
echo.
echo Migration completed!
pause
