<?php

namespace Modules\Cart\Repositories;

use Illuminate\Support\Carbon;
use Modules\Cart\Models\Cart;
use Modules\User\Models\User;

class CartRepository
{
    public const TTL_DAYS = 10;

    public function getOrCreate(?User $user, ?string $guestUuid): Cart
    {
        $query = Cart::query()->active();

        if ($user) {
            $cart = $query->where('user_id', $user->id)->first();
        } elseif ($guestUuid) {
            $cart = $query->where('guest_uuid', $guestUuid)->first();
        } else {
            throw new \InvalidArgumentException('Either user or guest UUID is required to resolve the cart.');
        }

        if ($cart && $cart->expires_at && $cart->expires_at->isPast()) {
            $cart->update(['status' => Cart::STATUS_EXPIRED]);
            $cart = null;
        }

        if (! $cart) {
            $cart = Cart::create([
                'user_id' => $user?->id,
                'guest_uuid' => $user ? null : $guestUuid,
                'currency' => config('app.currency', 'KWD'),
                'last_activity_at' => now(),
                'expires_at' => $this->expirationDate(),
            ]);
        } else {
            $cart->fill([
                'user_id' => $user?->id ?? $cart->user_id,
                'guest_uuid' => $user ? null : ($guestUuid ?? $cart->guest_uuid),
                'last_activity_at' => now(),
                'expires_at' => $this->expirationDate(),
            ])->save();
        }

        return $cart;
    }

    public function expireOldCarts(): int
    {
        return Cart::query()
            ->active()
            ->where('expires_at', '<', now())
            ->update(['status' => Cart::STATUS_EXPIRED]);
    }

    protected function expirationDate(): Carbon
    {
        return now()->addDays(self::TTL_DAYS);
    }
}
