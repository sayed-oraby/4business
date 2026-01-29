<?php

namespace Modules\Banner\Services;

use Modules\Activity\Services\AuditLogger;
use Modules\Banner\Models\Banner;
use Modules\Banner\Repositories\BannerRepository;
use Modules\Setting\Services\Media\MediaUploader;
use Modules\User\Models\User;

class BannerService
{
    public function __construct(
        protected BannerRepository $repository,
        protected MediaUploader $uploader,
        protected AuditLogger $audit
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function store(array $data, ?User $actor = null): Banner
    {
        $payload = $this->preparePayload($data);
        $banner = $this->repository->create($payload);

        $this->logAction($actor, 'banners.create', $banner, 'banner::banner.audit.created');

        return $banner;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Banner $banner, array $data, ?User $actor = null): Banner
    {
        $payload = $this->preparePayload($data, $banner);
        $updated = $this->repository->update($banner, $payload);

        $this->logAction($actor, 'banners.update', $updated, 'banner::banner.audit.updated');

        return $updated;
    }

    public function delete(Banner $banner, ?User $actor = null): void
    {
        $this->repository->delete($banner);

        $this->logAction($actor, 'banners.delete', $banner, 'banner::banner.audit.deleted', 'warning');
    }

    /**
     * @param  list<int>  $ids
     */
    public function bulkDelete(array $ids, ?User $actor = null): int
    {
        $banners = Banner::withTrashed()->whereIn('id', $ids)->get();
        $deleted = Banner::whereIn('id', $ids)->delete();

        foreach ($banners as $banner) {
            $this->logAction($actor, 'banners.bulk_delete', $banner, 'banner::banner.audit.bulk_deleted', 'warning');
        }

        return $deleted;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function preparePayload(array $data, ?Banner $banner = null): array
    {
        if (isset($data['image']) && $data['image']) {
            $data['image_path'] = $this->uploader->upload($data['image'], 'banners', [
                'max_width' => 1600,
            ])->path();
        }

        unset($data['image']);

        $data['title'] = array_filter($data['title'] ?? []);
        $data['description'] = array_filter($data['description'] ?? []);

        if (empty($data['title'])) {
            $data['title'] = $banner?->getRawOriginal('title') ?? [];
        }

        if (empty($data['description'])) {
            $data['description'] = $banner?->getRawOriginal('description') ?? [];
        }

        // Handle sort_order: if not provided or empty, set to null
        // if (!isset($data['sort_order']) || $data['sort_order'] === '' || $data['sort_order'] === null) {
        //     $data['sort_order'] = null;
        // } else {
        //     $data['sort_order'] = (int) $data['sort_order'];
        // }

        return $data;
    }

    protected function logAction(?User $actor, string $action, Banner $banner, string $translationKey, string $level = 'info'): void
    {
        $title = $banner->title ?? ('#'.$banner->id);

        $this->audit->log(
            $actor?->id,
            $action,
            __($translationKey, ['title' => $title]),
            [
                'context' => 'banners',
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
