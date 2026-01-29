<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\DB;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderStatus;
use Modules\Order\Services\InventoryService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('orders:expire-pending', function () {
    $threshold = now()->subMinutes(15);
    $failedStatus = OrderStatus::where('code', 'failed')->first()
        ?? OrderStatus::where('is_cancel', true)->first();

    $orders = Order::query()
        ->where('payment_status', 'pending')
        ->whereNull('paid_at')
        ->where('created_at', '<=', $threshold)
        ->get();

    $inventory = app(InventoryService::class);
    $count = 0;

    DB::transaction(function () use ($orders, $failedStatus, $inventory, &$count) {
        foreach ($orders as $order) {
            $order->update([
                'payment_status' => 'failed',
                'order_status_id' => $failedStatus?->id ?? $order->order_status_id,
            ]);

            $inventory->release($order);
            $count++;
        }
    });

    $this->info("Expired {$count} pending orders.");
})->purpose('Mark stale pending orders as failed and release inventory');

// Schedule: run every 5 minutes to expire stale pending orders (15m threshold inside command)
Schedule::command('orders:expire-pending')->everyFiveMinutes();
