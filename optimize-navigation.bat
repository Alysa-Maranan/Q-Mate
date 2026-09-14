@echo off
echo Optimizing Laravel for faster navigation...

echo [1/5] Clearing all caches...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo [2/5] Optimizing configuration...
php artisan config:cache

echo [3/5] Caching routes...
php artisan route:cache

echo [4/5] Optimizing autoloader...
composer dump-autoload --optimize

echo [5/5] Caching views...
php artisan view:cache

echo.
echo ✅ Optimization complete!
echo.
echo Navigation should now load much faster.
echo If still slow, check:
echo - Browser cache (Ctrl+Shift+Delete)
echo - Network tab in browser dev tools (F12)
echo - Database connection speed
echo.
pause