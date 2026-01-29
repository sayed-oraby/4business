<?php

namespace Modules\Brand\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Modules\Brand\Models\Brand;

class BrandRepository
{
    public function query(): Builder
    {
        return Brand::query();
    }

    /**
     * @param  array{status?: ?string, search?: ?string, trashed?: ?string}  $filters
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

        if (! empty($filters['search'])) {
            $term = '%'.$filters['search'].'%';
            $query->where(function (Builder $q) use ($term) {
                $q->where('title->en', 'like', $term)
                    ->orWhere('title->ar', 'like', $term);
            });
        }

        return $query;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function create(array $payload): Brand
    {
        return Brand::create($payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function update(Brand $brand, array $payload): Brand
    {
        $brand->update($payload);

        return $brand;
    }

    public function delete(Brand $brand): void
    {
        $brand->delete();
    }
}
