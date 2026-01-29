#!/bin/bash
cd ~/Sites/4jobs

echo "Starting Horizon..."
nohup php artisan horizon > storage/logs/horizon.log 2>&1 &
echo "Horizon PID: $!"

echo "Starting Reverb..."
nohup php artisan reverb:start > storage/logs/reverb.log 2>&1 &
echo "Reverb PID: $!"

echo "Starting Scheduler..."
nohup php artisan schedule:work > storage/logs/scheduler.log 2>&1 &
echo "Scheduler PID: $!"

echo "All services started!"
