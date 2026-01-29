<?php

namespace Modules\Cart\Services;

use DomainException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Cart\Models\Cart;
use Modules\Cart\Models\CartItem;
use Modules\Cart\Repositories\CartRepository;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use Modules\Shipping\Models\UserAddress;
use Modules\Shipping\Services\ShippingCalculator;

class CartService
{
    public function __construct(
        protected CartRepository $repository,
        protected ShippingCalculator $shippingCalculator
    ) {
    }

    /**
     * @return array{cart: Cart, guest_uuid: ?string}
     */
    public function getCart(?User $user, ?string $guestUuid, bool $createIfMissing = true): array
    {
        if (! $user && ! $guestUuid && $createIfMissing) {
            $guestUuid = (string) Str::uuid();
        }

        if (! $user && ! $guestUuid) {
            if ($createIfMissing) {
                throw new DomainException(__('cart::cart.errors.missing_owner'));
            }

            return [
                'cart' => null,
                'guest_uuid' => null,
            ];
        }

        $cart = $createIfMissing
            ? $this->repository->getOrCreate($user, $guestUuid)
            : $this->findActiveCart($user, $guestUuid);

        if ($cart) {
            $cart->load('items.product');
        }

        return [
            'cart' => $cart,
            'guest_uuid' => $user ? null : ($cart?->guest_uuid ?? $guestUuid),
        ];
    }

    /**
     * @return array{cart: Cart, guest_uuid: ?string}
     */
    public function addItem(?User $user, ?string $guestUuid, int $productId, int $quantity, array $options = []): array
    {
        $quantity = max(1, $quantity);

        return DB::transaction(function () use ($user, $guestUuid, $productId, $quantity, $options) {
            $context = $this->getCart($user, $guestUuid);
            /** @var Cart $cart */
            $cart = $context['cart'];
            $product = Product::query()->findOrFail($productId);

            $this->assertProductIsPurchasable($product);

            $available = $this->availableStock($product);
            if ($available <= 0) {
                throw new DomainException(__('cart::cart.errors.out_of_stock'));
            }

            $quantity = min($quantity, $available);

            $item = $cart->items()->where('product_id', $product->id)->first();

            if ($item) {
                $newQty = min($item->quantity + $quantity, $available);
                $item->quantity = $newQty;
                $item->unit_price = $product->price;
                $item->line_total = $this->calculateLineTotal($newQty, $product->price);
                $item->options = $this->mergeOptions($item->options ?? [], $options);
                $item->save();
            } else {
                $cart->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'line_total' => $this->calculateLineTotal($quantity, $product->price),
                    'options' => $this->sanitizeOptions($options),
                ]);
            }

            $this->recalculateTotals($cart->fresh('items'));

            return [
                'cart' => $cart->fresh('items.product'),
                'guest_uuid' => $context['guest_uuid'],
            ];
        });
    }

    /**
     * @return array{cart: Cart, guest_uuid: ?string}
     */
    public function updateItem(?User $user, ?string $guestUuid, CartItem $item, int $quantity, array $options = []): array
    {
        $quantity = max(1, $quantity);

        return DB::transaction(function () use ($user, $guestUuid, $item, $quantity, $options) {
            $this->assertItemOwnership($item, $user, $guestUuid);

            $product = Product::query()->findOrFail($item->product_id);
            $this->assertProductIsPurchasable($product);

            $available = $this->availableStock($product);
            if ($available <= 0) {
                $item->delete();
                throw new DomainException(__('cart::cart.errors.out_of_stock'));
            }

            $quantity = min($quantity, $available);

            $item->quantity = $quantity;
            $item->unit_price = $product->price;
            $item->line_total = $this->calculateLineTotal($quantity, $product->price);
            if (! empty($options)) {
                $item->options = $this->sanitizeOptions($options);
            }
            $item->save();

            $cart = $item->cart()->with('items.product')->first();
            $this->recalculateTotals($cart);

            return [
                'cart' => $cart->fresh('items.product'),
                'guest_uuid' => $user ? null : $cart->guest_uuid,
            ];
        });
    }

    /**
     * @return array{cart: Cart, guest_uuid: ?string}
     */
    public function removeItem(?User $user, ?string $guestUuid, CartItem $item): array
    {
        return DB::transaction(function () use ($user, $guestUuid, $item) {
            $this->assertItemOwnership($item, $user, $guestUuid);
            $cart = $item->cart;
            $item->delete();
            $this->recalculateTotals($cart->fresh('items'));

            return [
                'cart' => $cart->fresh('items.product'),
                'guest_uuid' => $user ? null : $cart->guest_uuid,
            ];
        });
    }

    /**
     * @return array{cart: Cart, guest_uuid: ?string, changes: array<int, array<string, mixed>>}
     */
    public function refresh(?User $user, ?string $guestUuid): array
    {
        $context = $this->getCart($user, $guestUuid, false);

        if (! $context['cart']) {
            return [
                'cart' => null,
                'guest_uuid' => $user ? null : ($guestUuid ?: null),
                'changes' => [],
            ];
        }

        $cart = $context['cart'];

        $changes = DB::transaction(fn () => $this->refreshCart($cart));

        return [
            'cart' => $cart->fresh('items.product'),
            'guest_uuid' => $context['guest_uuid'],
            'changes' => $changes,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function refreshCart(Cart $cart): array
    {
        $changes = [];
        $cart->load('items');

        foreach ($cart->items as $item) {
            $product = Product::query()->find($item->product_id);

            if (! $product || ! $this->isProductActive($product)) {
                $changes[] = [
                    'type' => 'removed',
                    'item_id' => $item->id,
                    'reason' => 'product_unavailable',
                ];
                $item->delete();
                continue;
            }

            $available = $this->availableStock($product);

            if ($available <= 0) {
                $changes[] = [
                    'type' => 'removed',
                    'item_id' => $item->id,
                    'reason' => 'out_of_stock',
                ];
                $item->delete();
                continue;
            }

            if ($item->quantity > $available) {
                $changes[] = [
                    'type' => 'quantity_adjusted',
                    'item_id' => $item->id,
                    'from' => $item->quantity,
                    'to' => $available,
                ];
                $item->quantity = $available;
            }

            if ((float) $item->unit_price !== (float) $product->price) {
                $changes[] = [
                    'type' => 'price_updated',
                    'item_id' => $item->id,
                    'from' => $item->unit_price,
                    'to' => $product->price,
                ];
                $item->unit_price = $product->price;
            }

            $item->line_total = $this->calculateLineTotal($item->quantity, $item->unit_price);
            $item->save();
        }

        $cart->refresh();
        $cart->load('items');
        $this->recalculateTotals($cart);

        return $changes;
    }

    /**
     * @return array{cart: Cart, guest_uuid: ?string, changes: array<int, array<string, mixed>>}
     */
    public function validateForCheckout(?User $user, ?string $guestUuid, ?int $userAddressId = null, ?int $cartId = null): array
    {
        $cart = $this->resolveCart($user, $guestUuid, $cartId);

        if (! $cart) {
            throw new DomainException(__('cart::cart.errors.empty'));
        }

        if ($cart->status !== Cart::STATUS_ACTIVE || ($cart->expires_at && $cart->expires_at->isPast())) {
            throw new DomainException(__('cart::cart.errors.expired'));
        }

        $changes = $this->refreshCart($cart);

        if ($cart->items()->count() === 0) {
            throw new DomainException(__('cart::cart.errors.empty_after_refresh'));
        }

        $shipping = null;

        if ($userAddressId && $user) {
            $address = UserAddress::query()
                ->where('user_id', $user->id)
                ->with('country')
                ->findOrFail($userAddressId);

            $estimate = $this->shippingCalculator->calculate($cart->load('items.product'), $address);

            $shipping = [
                'amount' => $estimate['amount'],
                'currency' => $estimate['currency'],
                'estimate_en' => $estimate['estimate_en'],
                'estimate_ar' => $estimate['estimate_ar'],
                'meta' => $estimate['meta'],
            ];
        }

        return [
            'cart' => $cart->fresh('items.product'),
            'guest_uuid' => $cart->guest_uuid ?? $guestUuid,
            'changes' => $changes,
            'shipping' => $shipping,
        ];
    }

    public function removeProductFromCarts(int $productId): void
    {
        $items = CartItem::query()->where('product_id', $productId)->get();
        $cartIds = $items->pluck('cart_id')->unique()->all();

        CartItem::query()->where('product_id', $productId)->delete();

        Cart::query()->whereIn('id', $cartIds)->get()->each(function (Cart $cart) {
            $this->recalculateTotals($cart->load('items'));
        });
    }

    public function resolveItemForOwner(?User $user, ?string $guestUuid, int $identifier): CartItem
    {
        $cart = $this->findActiveCart($user, $guestUuid);

        if (! $cart) {
            throw new DomainException(__('cart::cart.errors.item_not_found'));
        }

        $cart->loadMissing('items');
        $item = $cart->items->firstWhere('id', $identifier)
            ?? $cart->items->firstWhere('product_id', $identifier);

        if (! $item) {
            throw new DomainException(__('cart::cart.errors.item_not_found'));
        }

        return $item;
    }

    public function syncProductAcrossCarts(Product $product): void
    {
        $items = CartItem::query()
            ->where('product_id', $product->id)
            ->get()
            ->groupBy('cart_id');

        /** @var Collection<int, CartItem> $itemGroup */
        foreach ($items as $cartId => $itemGroup) {
            $cart = Cart::find($cartId);
            if (! $cart) {
                continue;
            }

            foreach ($itemGroup as $item) {
                if (! $this->isProductActive($product) || $this->availableStock($product) <= 0) {
                    $item->delete();
                    continue;
                }

                $maxQty = $this->availableStock($product);
                if ($item->quantity > $maxQty) {
                    $item->quantity = $maxQty;
                }

                $item->unit_price = $product->price;
                $item->line_total = $this->calculateLineTotal($item->quantity, $item->unit_price);
                $item->save();
            }

            $this->recalculateTotals($cart->fresh('items'));
        }
    }

    protected function recalculateTotals(?Cart $cart): void
    {
        if (! $cart) {
            return;
        }

        $cart->loadMissing('items');
        $subtotal = $cart->items->sum('line_total');
        $discountTotal = $cart->discount_total ?? 0;
        $grandTotal = max($subtotal - $discountTotal, 0);

        $cart->fill([
            'subtotal' => $subtotal,
            'discount_total' => $discountTotal,
            'grand_total' => $grandTotal,
            'last_activity_at' => now(),
            'expires_at' => now()->addDays(CartRepository::TTL_DAYS),
        ])->save();
    }

    protected function calculateLineTotal(int $quantity, float|string $price): float
    {
        return round($quantity * (float) $price, 3);
    }

    protected function sanitizeOptions(array $options): array
    {
        return Arr::where($options, fn ($value, $key) => is_scalar($value) || is_array($value));
    }

    protected function mergeOptions(array $existing, array $new): array
    {
        return array_replace($existing, $this->sanitizeOptions($new));
    }

    protected function availableStock(Product $product): int
    {
        return max(0, (int) $product->qty);
    }

    protected function assertProductIsPurchasable(Product $product): void
    {
        if (! $this->isProductActive($product)) {
            throw new DomainException(__('cart::cart.errors.product_inactive'));
        }
    }

    protected function isProductActive(Product $product): bool
    {
        return $product->status === 'active';
    }

    protected function assertItemOwnership(CartItem $item, ?User $user, ?string $guestUuid): void
    {
        $cart = $item->cart;
        if ($user && $cart->user_id === $user->id) {
            return;
        }

        if (! $user && $guestUuid && $cart->guest_uuid === $guestUuid) {
            return;
        }

        throw new DomainException(__('cart::cart.errors.unauthorized_item'));
    }

    protected function findActiveCart(?User $user, ?string $guestUuid): ?Cart
    {
        $query = Cart::query()->active();

        if ($user) {
            $cart = $query->where('user_id', $user->id)->first();
            if (! $cart && $guestUuid) {
                $cart = Cart::query()->active()->where('guest_uuid', $guestUuid)->first();
            }
        }
        elseif ($guestUuid) {
            $cart = $query->where('guest_uuid', $guestUuid)->first();
        } else {
            $cart = null;
        }

        if ($cart && $cart->expires_at && $cart->expires_at->isPast()) {
            $cart->update(['status' => Cart::STATUS_EXPIRED]);

            return null;
        }

        return $cart;
    }

    protected function resolveCart(?User $user, ?string $guestUuid, ?int $cartId = null): ?Cart
    {
        if ($cartId) {
            $cart = Cart::query()->active()->with('items')->find($cartId);
            if ($cart && (($user && $cart->user_id === $user->id) || (!$user && $guestUuid && $cart->guest_uuid === $guestUuid))) {
                return $cart;
            }
        }

        $context = $this->getCart($user, $guestUuid, false);
        return $context['cart'];
    }

    public function finalizeCart(Cart $cart): void
    {
        DB::transaction(function () use ($cart) {
            $cart->items()->delete();
            $cart->update([
                'status' => Cart::STATUS_CHECKED_OUT,
                'subtotal' => 0,
                'discount_total' => 0,
                'grand_total' => 0,
            ]);
        });
    }
}
