<?php

namespace Modules\Page\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Modules\Page\Models\Page;

class PageRepository
{
    public function query(): Builder
    {
        return Page::query();
    }

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
                    ->orWhere('title->ar', 'like', $term)
                    ->orWhere('description->en', 'like', $term)
                    ->orWhere('description->ar', 'like', $term)
                    ->orWhere('slug', 'like', $term);
            });
        }

        return $query;
    }

    public function create(array $payload): Page
    {
        return Page::create($payload);
    }

    public function update(Page $page, array $payload): Page
    {
        $page->update($payload);

        return $page;
    }

    public function delete(Page $page): void
    {
        $page->delete();
    }
}
