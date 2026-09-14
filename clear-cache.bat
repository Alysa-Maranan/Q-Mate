@echo off
echo Clearing Laravel cache and optimizing...

php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan session:flush

echo Cache cleared successfully!
echo.
echo If you're still experiencing refresh issues, try:
echo 1. Restart your web server
echo 2. Clear browser cache (Ctrl+Shift+Delete)
echo 3. Check browser console for JavaScript errors (F12)
echo.
pause