<?php

namespace Modules\Cart\Observers;

use Modules\Cart\Services\CartService;
use Modules\Product\Models\Product;

class CartProductObserver
{
    public function __construct(
        protected CartService $cartService
    ) {
    }

    public function updated(Product $product): void
    {
        if ($product->wasChanged(['status', 'qty', 'price'])) {
            $this->cartService->syncProductAcrossCarts($product);
        }
    }

    public function deleted(Product $product): void
    {
        $this->cartService->removeProductFromCarts($product->id);
    }

    public function forceDeleted(Product $product): void
    {
        $this->deleted($product);
    }
}
