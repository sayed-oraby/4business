<?php

namespace Modules\Order\Policies;

use Modules\Order\Models\OrderStatus;
use Modules\User\Models\User;

class OrderStatusPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('order_statuses.view');
    }

    public function view(User $user, OrderStatus $status): bool
    {
        return $user->can('order_statuses.view');
    }

    public function create(User $user): bool
    {
        return $user->can('order_statuses.create');
    }

    public function update(User $user, OrderStatus $status): bool
    {
        return $user->can('order_statuses.update');
    }

    public function delete(User $user, OrderStatus $status): bool
    {
        return $user->can('order_statuses.delete');
    }
}
