<?php

namespace Modules\Page\Services;

use Illuminate\Support\Str;
use Modules\Activity\Services\AuditLogger;
use Modules\Page\Models\Page;
use Modules\Page\Repositories\PageRepository;
use Modules\Setting\Services\Media\MediaUploader;
use Modules\User\Models\User;

class PageService
{
    public function __construct(
        protected PageRepository $repository,
        protected MediaUploader $uploader,
        protected AuditLogger $audit
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function store(array $data, ?User $actor = null): Page
    {
        $payload = $this->preparePayload($data);
        $page = $this->repository->create($payload);

        $this->logAction($actor, 'pages.create', $page, 'page::page.audit.created');

        return $page;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Page $page, array $data, ?User $actor = null): Page
    {
        $payload = $this->preparePayload($data, $page);
        $updated = $this->repository->update($page, $payload);

        $this->logAction($actor, 'pages.update', $updated, 'page::page.audit.updated');

        return $updated;
    }

    public function delete(Page $page, ?User $actor = null): void
    {
        $this->repository->delete($page);

        $this->logAction($actor, 'pages.delete', $page, 'page::page.audit.deleted', 'warning');
    }

    /**
     * @param  list<int>  $ids
     */
    public function bulkDelete(array $ids, ?User $actor = null): int
    {
        $pages = Page::withTrashed()->whereIn('id', $ids)->get();
        $deleted = Page::whereIn('id', $ids)->delete();

        foreach ($pages as $page) {
            $this->logAction($actor, 'pages.bulk_delete', $page, 'page::page.audit.bulk_deleted', 'warning');
        }

        return $deleted;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function preparePayload(array $data, ?Page $page = null): array
    {
        if (isset($data['image']) && $data['image']) {
            $data['image_path'] = $this->uploader->upload($data['image'], 'pages', [
                'max_width' => 1600,
            ])->path();
        }

        unset($data['image']);

        $data['title'] = array_filter($data['title'] ?? []);
        $data['description'] = array_filter($data['description'] ?? []);

        if (empty($data['title'])) {
            $data['title'] = $page?->getRawOriginal('title') ?? [];
        }

        if (empty($data['description'])) {
            $data['description'] = $page?->getRawOriginal('description') ?? [];
        }

        $data['slug'] = $this->generateSlug($data['slug'] ?? null, $data['title'], $page);

        return $data;
    }

    protected function generateSlug(?string $slug, array $titleTranslations, ?Page $existing = null): string
    {
        $base = $slug ?: ($titleTranslations['en'] ?? reset($titleTranslations) ?? Str::uuid()->toString());
        $base = Str::slug($base);
        if ($base === '') {
            $base = Str::slug('page '.now()->timestamp);
        }

        $candidate = $base;
        $suffix = 1;

        while ($this->slugExists($candidate, $existing)) {
            $candidate = $base.'-'.$suffix;
            $suffix++;
        }

        return $candidate;
    }

    protected function slugExists(string $slug, ?Page $existing): bool
    {
        $query = Page::where('slug', $slug);

        if ($existing) {
            $query->where('id', '!=', $existing->id);
        }

        return $query->exists();
    }

    protected function logAction(?User $actor, string $action, Page $page, string $translationKey, string $level = 'info'): void
    {
        $title = $page->title ?? ('#'.$page->id);

        $this->audit->log(
            $actor?->id,
            $action,
            __($translationKey, ['title' => $title]),
            [
                'context' => 'pages',
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
