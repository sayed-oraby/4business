<?php

namespace Modules\Category\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Modules\Activity\Services\AuditLogger;
use Modules\Category\Models\Category;
use Modules\Category\Repositories\CategoryRepository;
use Modules\Setting\Services\Media\MediaUploader;
use Modules\User\Models\User;

class CategoryService
{
    public function __construct(
        protected CategoryRepository $repository,
        protected MediaUploader $uploader,
        protected AuditLogger $audit) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function store(array $data, ?User $actor = null): Category
    {
        return DB::transaction(function () use ($data, $actor) {
            $payload = $this->preparePayload($data);
            $category = $this->repository->create($payload);

            $this->logAction($actor, 'categories.create', $category, 'category::category.audit.created');

            return $category;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Category $category, array $data, ?User $actor = null): Category
    {
        return DB::transaction(function () use ($category, $data, $actor) {
            $payload = $this->preparePayload($data, $category);
            $updated = $this->repository->update($category, $payload);

            $this->logAction($actor, 'categories.update', $updated, 'category::category.audit.updated');

            return $updated;
        });
    }

    public function delete(Category $category, ?User $actor = null): void
    {
        $this->repository->delete($category);

        $this->logAction($actor, 'categories.delete', $category, 'category::category.audit.deleted', 'warning');
    }

    /**
     * @param  list<int>  $ids
     */
    public function bulkDelete(array $ids, ?User $actor = null): int
    {
        $categories = Category::withTrashed()->whereIn('id', $ids)->get();
        $deleted = Category::whereIn('id', $ids)->delete();

        foreach ($categories as $category) {
            $this->logAction($actor, 'categories.bulk_delete', $category, 'category::category.audit.bulk_deleted', 'warning');
        }

        return $deleted;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function preparePayload(array $data, ?Category $category = null): array
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image_path'] = $this->uploader->upload($data['image'], 'categories/cover', [
                'max_width' => 800,
            ])->path();
        }

        unset($data['image']);

        $data['title'] = array_filter($data['title'] ?? []);

        if (empty($data['title'])) {
            $data['title'] = $category?->getRawOriginal('title') ?? [];
        }

        $data['is_featured'] = filter_var($data['is_featured'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $data['featured_order'] = (int) ($data['featured_order'] ?? 0);
        $data['position'] = (int) ($data['position'] ?? 0);

        if (! empty($data['parent_id'])) {
            $data['parent_id'] = (int) $data['parent_id'];
            if (isset($category) && $category->id === $data['parent_id']) {
                unset($data['parent_id']);
            }
        } else {
            $data['parent_id'] = null;
        }

        return $data;
    }

    protected function logAction(?User $actor, string $action, Category $category, string $translationKey, string $level = 'info'): void
    {
        $title = $category->title ?? ('#'.$category->id);

        $this->audit->log(
            $actor?->id,
            $action,
            __($translationKey, ['title' => $title]),
            [
                'context' => 'categories',
                'notification_type' => 'audit',
                'level' => $level,
                'description_key' => $translationKey,
                'description_params' => ['title' => $title],
                'notification_message_key' => $translationKey,
                'notification_message_params' => ['title' => $title],
            ]
        );
    }
}
