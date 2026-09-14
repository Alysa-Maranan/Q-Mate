#!/bin/bash

echo "Starting Feeder Check Service..."
echo "Press Ctrl+C to stop"
echo ""

while true; do
    echo "[$(date)] Running feeder check..."
    php artisan feeder:check
    echo "[$(date)] Next check in 60 seconds..."
    sleep 60
done
