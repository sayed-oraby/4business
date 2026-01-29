<?php

namespace Modules\Order\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Order\Models\OrderStatus;

class OrderDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [
                'code' => 'pending',
                'title' => [
                    'en' => 'Pending',
                    'ar' => 'قيد الانتظار',
                ],
                'color' => '#ffc107',
                'is_default' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'completed',
                'title' => [
                    'en' => 'Completed',
                    'ar' => 'مكتمل',
                ],
                'color' => '#198754',
                'is_final' => true,
                'sort_order' => 2,
            ],
            [
                'code' => 'processing',
                'title' => [
                    'en' => 'Processing',
                    'ar' => 'قيد المعالجة',
                ],
                'color' => '#0d6efd',
                'sort_order' => 3,
            ],
            [
                'code' => 'shipped',
                'title' => [
                    'en' => 'Shipped',
                    'ar' => 'تم الشحن',
                ],
                'color' => '#6610f2',
                'sort_order' => 4,
            ],
            [
                'code' => 'delivered',
                'title' => [
                    'en' => 'Delivered',
                    'ar' => 'تم التسليم',
                ],
                'color' => '#198754',
                'is_final' => true,
                'sort_order' => 5,
            ],
            [
                'code' => 'canceled',
                'title' => [
                    'en' => 'Canceled',
                    'ar' => 'ملغي',
                ],
                'color' => '#dc3545',
                'is_cancel' => true,
                'sort_order' => 6,
            ],
            [
                'code' => 'refunded',
                'title' => [
                    'en' => 'Refunded',
                    'ar' => 'مسترد',
                ],
                'color' => '#6c757d',
                'is_refund' => true,
                'sort_order' => 7,
            ],
        ];

        foreach ($statuses as $status) {
            OrderStatus::updateOrCreate(
                ['code' => $status['code']],
                $status
            );
        }
    }
}
