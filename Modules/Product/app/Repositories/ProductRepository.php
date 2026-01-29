<?php

namespace Modules\Product\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Modules\Product\Models\Product;

class ProductRepository
{
    public function query(): Builder
    {
        return Product::query()->with(['category', 'brand', 'tags']);
    }

    /**
     * @param  array{status?: ?string, search?: ?string, trashed?: ?string, featured?: ?bool, is_new_arrival?: ?bool, is_trending?: ?bool}  $filters
     */
    public function datatable(array $filters = []): Builder
    {
        $query = $this->query();

        if (($filters['trashed'] ?? null) === 'only') {
            $query->onlyTrashed();
        } elseif (($filters['trashed'] ?? null) === 'with') {
            $query->withTrashed();
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (array_key_exists('featured', $filters) && ! is_null($filters['featured'])) {
            $query->where('is_featured', (bool) $filters['featured']);
        }

        if (array_key_exists('is_new_arrival', $filters) && ! is_null($filters['is_new_arrival'])) {
            $query->where('is_new_arrival', (bool) $filters['is_new_arrival']);
        }

        if (array_key_exists('is_trending', $filters) && ! is_null($filters['is_trending'])) {
            $query->where('is_trending', (bool) $filters['is_trending']);
        }

        if (! empty($filters['search'])) {
            $term = '%'.$filters['search'].'%';
            $query->where(function (Builder $q) use ($term) {
                $q->where('title->en', 'like', $term)
                    ->orWhere('title->ar', 'like', $term)
                    ->orWhere('sku', 'like', $term);
            });
        }

        return $query;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function create(array $payload): Product
    {
        return Product::create($payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function update(Product $product, array $payload): Product
    {
        $product->update($payload);

        return $product->refresh();
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }
}
