<?php

namespace Modules\Blog\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Modules\Blog\Models\Blog;

class BlogRepository
{
    public function query(): Builder
    {
        return Blog::query()->with(['creator', 'tags']);
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
                    ->orWhere('title->ar', 'like', $term)
                    ->orWhere('short_description->en', 'like', $term)
                    ->orWhere('short_description->ar', 'like', $term)
                    ->orWhere('description->en', 'like', $term)
                    ->orWhere('description->ar', 'like', $term);
            });
        }

        return $query;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function create(array $payload): Blog
    {
        return Blog::create($payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function update(Blog $blog, array $payload): Blog
    {
        $blog->update($payload);

        return $blog->fresh(['creator', 'tags', 'galleries']);
    }

    public function delete(Blog $blog): void
    {
        $blog->delete();
    }
}
