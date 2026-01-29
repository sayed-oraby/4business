<?php

namespace Modules\Blog\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Activity\Services\AuditLogger;
use Modules\Blog\Models\Blog;
use Modules\Blog\Models\BlogGallery;
use Modules\Blog\Models\BlogTag;
use Modules\Blog\Repositories\BlogRepository;
use Modules\Setting\Services\Media\MediaUploader;
use Modules\User\Models\User;

class BlogService
{
    public function __construct(
        protected BlogRepository $repository,
        protected MediaUploader $uploader,
        protected AuditLogger $audit) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function store(array $data, ?User $actor = null): Blog
    {
        return DB::transaction(function () use ($data, $actor) {
        $payload = $this->preparePayload($data);
        $payload['created_by'] = $data['created_by'] ?? $actor?->id;

        $blog = $this->repository->create($payload);

            $this->syncTags($blog, Arr::wrap($data['tags'] ?? []));
            $this->attachGallery($blog, $data['gallery_token'] ?? null, $actor);

            $this->logAction($actor, 'blogs.create', $blog, 'blog::blog.audit.created');

            return $blog;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Blog $blog, array $data, ?User $actor = null): Blog
    {
        return DB::transaction(function () use ($blog, $data, $actor) {
        $payload = $this->preparePayload($data, $blog);

        if (array_key_exists('created_by', $data)) {
            $payload['created_by'] = $data['created_by'];
        }

            $updated = $this->repository->update($blog, $payload);

            $this->syncTags($updated, Arr::wrap($data['tags'] ?? []));
            $this->attachGallery($updated, $data['gallery_token'] ?? null, $actor);

            $this->logAction($actor, 'blogs.update', $updated, 'blog::blog.audit.updated');

            return $updated;
        });
    }

    public function delete(Blog $blog, ?User $actor = null): void
    {
        $this->repository->delete($blog);

        $this->logAction($actor, 'blogs.delete', $blog, 'blog::blog.audit.deleted', 'warning');
    }

    /**
     * @param  list<int>  $ids
     */
    public function bulkDelete(array $ids, ?User $actor = null): int
    {
        $blogs = Blog::withTrashed()->whereIn('id', $ids)->get();
        $deleted = Blog::whereIn('id', $ids)->delete();

        foreach ($blogs as $blog) {
            $this->logAction($actor, 'blogs.bulk_delete', $blog, 'blog::blog.audit.bulk_deleted', 'warning');
        }

        return $deleted;
    }

    public function uploadGallery(?Blog $blog, string $uploadToken, UploadedFile $file, ?User $actor = null): BlogGallery
    {
        $media = $this->uploader->upload($file, 'blogs/gallery', ['max_width' => 2000]);

        return BlogGallery::create([
            'blog_id' => $blog?->id,
            'upload_token' => $blog?->id ? null : $uploadToken,
            'image_path' => $media->path(),
            'uploaded_by' => $actor?->id,
        ]);
    }

    public function removeGallery(BlogGallery $gallery): void
    {
        if ($gallery->image_path && Storage::disk('public')->exists($gallery->image_path)) {
            Storage::disk('public')->delete($gallery->image_path);
        }

        $gallery->delete();
    }

    /**
     * @param  array<string, string>  $titles
     */
    public function createTag(array $titles): BlogTag
    {
        $titles = array_filter($titles);
        $base = $titles['en'] ?? reset($titles) ?? Str::uuid()->toString();
        $slug = Str::slug($base);

        $candidate = $slug;
        $suffix = 1;

        while (BlogTag::where('slug', $candidate)->exists()) {
            $candidate = $slug.'-'.$suffix;
            $suffix++;
        }

        return BlogTag::create([
            'slug' => $candidate,
            'title' => $titles,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function preparePayload(array $data, ?Blog $blog = null): array
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image_path'] = $this->uploader->upload($data['image'], 'blogs/cover', [
                'max_width' => 1600,
            ])->path();
        }

        unset($data['image']);

        $data['title'] = array_filter($data['title'] ?? []);
        $data['short_description'] = array_filter($data['short_description'] ?? []);
        $data['description'] = array_filter($data['description'] ?? []);

        if (empty($data['title'])) {
            $data['title'] = $blog?->getRawOriginal('title') ?? [];
        }

        if (empty($data['short_description'])) {
            $data['short_description'] = $blog?->getRawOriginal('short_description') ?? [];
        }

        if (empty($data['description'])) {
            $data['description'] = $blog?->getRawOriginal('description') ?? [];
        }

        $data['status'] = $data['status'] ?? $blog?->status ?? 'draft';

        return $data;
    }

    protected function attachGallery(Blog $blog, ?string $token, ?User $actor = null): void
    {
        if (! $token) {
            return;
        }

        retry(3, function () use ($blog, $token, $actor) {
            BlogGallery::where('upload_token', $token)
                ->whereNotNull('upload_token')
                ->update([
                    'blog_id' => $blog->id,
                    'upload_token' => null,
                    'uploaded_by' => $actor?->id,
                ]);
        }, 100);
    }

    /**
     * @param  array<int>  $tagIds
     */
    protected function syncTags(Blog $blog, array $tagIds): void
    {
        $ids = collect($tagIds)
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->all();

        $blog->tags()->sync($ids);
    }

    protected function logAction(?User $actor, string $action, Blog $blog, string $translationKey, string $level = 'info'): void
    {
        $title = $blog->title ?? ('#'.$blog->id);

        $this->audit->log(
            $actor?->id,
            $action,
            __($translationKey, ['title' => $title]),
            [
                'context' => 'blogs',
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
