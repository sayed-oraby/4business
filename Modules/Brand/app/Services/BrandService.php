<?php

namespace Modules\Brand\Services;

use Illuminate\Http\UploadedFile;
use Modules\Activity\Services\AuditLogger;
use Modules\Brand\Models\Brand;
use Modules\Brand\Repositories\BrandRepository;
use Modules\Setting\Services\Media\MediaUploader;
use Modules\User\Models\User;

class BrandService
{
    public function __construct(
        protected BrandRepository $repository,
        protected MediaUploader $uploader,
        protected AuditLogger $audit) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function store(array $data, ?User $actor = null): Brand
    {
        $payload = $this->preparePayload($data);
        $brand = $this->repository->create($payload);

        $this->logAction($actor, 'brands.create', $brand, 'brand::brand.audit.created');

        return $brand;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Brand $brand, array $data, ?User $actor = null): Brand
    {
        $payload = $this->preparePayload($data, $brand);
        $updated = $this->repository->update($brand, $payload);

        $this->logAction($actor, 'brands.update', $updated, 'brand::brand.audit.updated');

        return $updated;
    }

    public function delete(Brand $brand, ?User $actor = null): void
    {
        $this->repository->delete($brand);

        $this->logAction($actor, 'brands.delete', $brand, 'brand::brand.audit.deleted', 'warning');
    }

    /**
     * @param  list<int>  $ids
     */
    public function bulkDelete(array $ids, ?User $actor = null): int
    {
        $brands = Brand::withTrashed()->whereIn('id', $ids)->get();
        $deleted = Brand::whereIn('id', $ids)->delete();

        foreach ($brands as $brand) {
            $this->logAction($actor, 'brands.bulk_delete', $brand, 'brand::brand.audit.bulk_deleted', 'warning');
        }

        return $deleted;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function preparePayload(array $data, ?Brand $brand = null): array
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image_path'] = $this->uploader->upload($data['image'], 'brands/logo', [
                'max_width' => 600,
            ])->path();
        }

        unset($data['image']);

        $data['title'] = array_filter($data['title'] ?? []);

        if (empty($data['title'])) {
            $data['title'] = $brand?->getRawOriginal('title') ?? [];
        }

        $data['position'] = (int) ($data['position'] ?? 0);

        return $data;
    }

    protected function logAction(?User $actor, string $action, Brand $brand, string $translationKey, string $level = 'info'): void
    {
        $title = $brand->title ?? ('#'.$brand->id);

        $this->audit->log(
            $actor?->id,
            $action,
            __($translationKey, ['title' => $title]),
            [
                'context' => 'brands',
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
