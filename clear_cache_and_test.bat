@echo off
echo Clearing Laravel cache...
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo.
echo Cache cleared successfully!
echo.
echo Now refresh your browser and test the Inventory page.
pause
