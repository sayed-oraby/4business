<?php

namespace Modules\Order\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Modules\Order\Models\Order;

class OrderRepository
{
    public function query(array $filters = []): Builder
    {
        return Order::query()
            ->with(['status', 'payments'])
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->whereHas('status', fn ($s) => $s->where('code', $status)))
            ->when($filters['payment_status'] ?? null, fn ($q, $ps) => $q->where('payment_status', $ps))
            ->when($filters['user_id'] ?? null, fn ($q, $userId) => $q->where('user_id', $userId))
            ->when($filters['guest_uuid'] ?? null, fn ($q, $guest) => $q->where('guest_uuid', $guest))
            ->when($filters['search'] ?? null, function ($q) use ($filters) {
                $term = '%'.$filters['search'].'%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('id', 'like', $term)
                        ->orWhere('guest_uuid', 'like', $term)
                        ->orWhereHas('payments', fn ($p) => $p->where('ref_number', 'like', $term));
                });
            })
            ->when($filters['date_from'] ?? null, fn ($q, $from) => $q->whereDate('created_at', '>=', $from))
            ->when($filters['date_to'] ?? null, fn ($q, $to) => $q->whereDate('created_at', '<=', $to));
    }
}
