#!/bin/bash
echo "Stopping services..."
pkill -f "php artisan horizon"
pkill -f "php artisan reverb"
pkill -f "php artisan schedule:work"
echo "Services stopped!"
