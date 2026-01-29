<?php

namespace Modules\Category\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Modules\Category\Models\Category;

class CategoryRepository
{
    public function query(): Builder
    {
        return Category::query()->with('parent');
    }

    /**
     * @param  array{status?: ?string, search?: ?string, trashed?: ?string, parent_id?: ?int, featured?: ?bool}  $filters
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

        if (! is_null($filters['parent_id'] ?? null)) {
            $query->where('parent_id', $filters['parent_id']);
        }

        if (! is_null($filters['featured'] ?? null)) {
            $query->where('is_featured', filter_var($filters['featured'], FILTER_VALIDATE_BOOLEAN));
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
    public function create(array $payload): Category
    {
        return Category::create($payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function update(Category $category, array $payload): Category
    {
        $category->update($payload);

        return $category->fresh(['parent']);
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
