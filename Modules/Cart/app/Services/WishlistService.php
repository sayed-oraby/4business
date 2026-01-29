<?php

namespace Modules\Cart\Services;

use Illuminate\Support\Str;
use Modules\Cart\Models\Wishlist;
use Modules\Cart\Models\WishlistItem;
use Modules\Product\Models\Product;
use Modules\User\Models\User;

class WishlistService
{
    /**
     * @return array{wishlist: Wishlist, guest_uuid: ?string}
     */
    public function resolveWishlist(?User $user, ?string $guestUuid): array
    {
        if (! $user && ! $guestUuid) {
            $guestUuid = (string) Str::uuid();
        }

        if ($user) {
            $wishlist = Wishlist::firstOrCreate(['user_id' => $user->id], [
                'guest_uuid' => null,
            ]);
        } else {
            $wishlist = Wishlist::firstOrCreate(['guest_uuid' => $guestUuid]);
        }

        return [
            'wishlist' => $wishlist->load('items.product'),
            'guest_uuid' => $user ? null : $wishlist->guest_uuid,
        ];
    }

    public function toggleItem(Wishlist $wishlist, int $productId): Wishlist
    {
        $product = Product::query()->where('id', $productId)->firstOrFail();

        $existing = $wishlist->items()->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->delete();
        } else {
            $wishlist->items()->create(['product_id' => $product->id]);
        }

        return $wishlist->fresh('items.product');
    }

    public function removeItem(Wishlist $wishlist, int $productId): Wishlist
    {
        $wishlist->items()->where('product_id', $productId)->delete();

        return $wishlist->fresh('items.product');
    }

    public function merge(Wishlist $from, Wishlist $to): void
    {
        $productIds = $from->items()->pluck('product_id');

        foreach ($productIds as $productId) {
            WishlistItem::firstOrCreate([
                'wishlist_id' => $to->id,
                'product_id' => $productId,
            ]);
        }

        $from->items()->delete();
        $from->delete();
    }
}
