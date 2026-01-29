<?php

return [
    'messages' => [
        'loaded' => 'Cart loaded successfully.',
        'item_added' => 'Item added to cart.',
        'item_updated' => 'Cart item updated.',
        'item_removed' => 'Item removed from cart.',
        'refreshed' => 'Cart refreshed.',
        'checkout_ready' => 'Cart validated and ready for checkout.',
    ],
    'errors' => [
        'missing_owner' => 'Unable to resolve cart owner.',
        'out_of_stock' => 'This product is out of stock.',
        'product_inactive' => 'This product is not available for purchase.',
        'unauthorized_item' => 'You cannot modify this cart item.',
        'expired' => 'Your cart has expired. Please start again.',
        'empty' => 'No active cart found.',
        'empty_after_refresh' => 'Your cart became empty after refreshing.',
        'item_not_found' => 'Cart item could not be found.',
    ],
];
