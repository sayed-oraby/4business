<?php

namespace Modules\Reports\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Product\Models\Product;

class ProductsReportService
{
    /**
     * Get inventory status report
     */
    public function getInventoryStatus(): array
    {
        $products = Product::query()
            ->select([
                'id',
                'title',
                'sku',
                'qty',
                'status',
            ])
            ->get();

        $available = $products->where('qty', '>', 0)->where('status', 'active')->count();
        $lowStock = $products->where('qty', '>', 0)->where('qty', '<=', 10)->where('status', 'active')->count();
        $outOfStock = $products->filter(function ($product) {
            return ($product->qty ?? 0) <= 0 || $product->status !== 'active';
        })->count();

        return [
            'available' => $available,
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
            'total' => $products->count(),
            'low_stock_products' => $products
                ->where('qty', '>', 0)
                ->where('qty', '<=', 10)
                ->where('status', 'active')
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'title' => is_array($product->title) ? ($product->title[app()->getLocale()] ?? reset($product->title)) : $product->title,
                        'sku' => $product->sku,
                        'qty' => $product->qty,
                    ];
                })
                ->values()
                ->toArray(),
        ];
    }

    /**
     * Get product pricing by country (if multi-currency support exists)
     */
    public function getProductPricingByCountry(): array
    {
        // This would need to be implemented based on your pricing structure
        // For now, return a placeholder structure
        return [
            'message' => 'Product pricing by country report - to be implemented based on pricing structure',
        ];
    }

    /**
     * Get price changes history
     */
    public function getPriceChanges(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        // This would require a price_history table or activity log
        // For now, return placeholder
        return [
            'message' => 'Price changes history - requires price_history table or activity log',
        ];
    }
}
