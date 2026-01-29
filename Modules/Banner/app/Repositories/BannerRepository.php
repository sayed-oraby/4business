<?php

namespace Modules\Banner\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Banner\Models\Banner;

class BannerRepository
{
    public function query(): Builder
    {
        return Banner::query();
    }

    public function datatable(array $filters = []): Builder
    {
        $query = $this->query();

        if (! empty($filters['trashed'])) {
            if ($filters['trashed'] === 'only') {
                $query->onlyTrashed();
            } elseif ($filters['trashed'] === 'with') {
                $query->withTrashed();
            }
        }

        if (! empty($filters['placement'])) {
            $query->where('placement', $filters['placement']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $term = "%{$filters['search']}%";
            $query->where(function (Builder $q) use ($term) {
                $q->where('title->en', 'like', $term)
                    ->orWhere('title->ar', 'like', $term)
                    ->orWhere('description->en', 'like', $term)
                    ->orWhere('description->ar', 'like', $term);
            });
        }

        return $query;
    }

    public function create(array $attributes): Banner
    {
        return Banner::create($attributes);
    }

    public function update(Banner $banner, array $attributes): Banner
    {
        $banner->update($attributes);

        return $banner;
    }

    public function delete(Banner $banner): void
    {
        $banner->delete();
    }

    public function activeForPlacement(string $placement, int $limit = 10): Collection
    {
        return Banner::placement($placement)
            ->activeNow()
            ->orderBy('sort_order')
            ->limit($limit)
            ->get();
    }
}
