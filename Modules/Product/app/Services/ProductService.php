<?php

namespace Modules\Product\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Activity\Services\AuditLogger;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductGallery;
use Modules\Product\Models\ProductTag;
use Modules\Product\Repositories\ProductRepository;
use Modules\Setting\Services\Media\MediaUploader;
use Modules\User\Models\User;

class ProductService
{
    public function __construct(
        protected ProductRepository $repository,
        protected MediaUploader $uploader,
        protected AuditLogger $audit) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function store(array $data, ?User $actor = null): Product
    {
        return DB::transaction(function () use ($data, $actor) {
            $payload = $this->preparePayload($data, null, $actor);
            $product = $this->repository->create($payload);

            $this->syncTags($product, Arr::wrap($data['tags'] ?? []));
            $this->attachGallery($product, $data['gallery_token'] ?? null, $actor);

            $this->logAction($actor, 'products.create', $product, 'product::product.audit.created');

            return $product;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Product $product, array $data, ?User $actor = null): Product
    {
        return DB::transaction(function () use ($product, $data, $actor) {
            $payload = $this->preparePayload($data, $product, $actor);
            $updated = $this->repository->update($product, $payload);

            $this->syncTags($updated, Arr::wrap($data['tags'] ?? []));
            $this->attachGallery($updated, $data['gallery_token'] ?? null, $actor);

            $this->logAction($actor, 'products.update', $updated, 'product::product.audit.updated');

            return $updated;
        });
    }

    public function delete(Product $product, ?User $actor = null): void
    {
        $this->repository->delete($product);

        $this->logAction($actor, 'products.delete', $product, 'product::product.audit.deleted', 'warning');
    }

    /**
     * @param  list<int>  $ids
     */
    public function bulkDelete(array $ids, ?User $actor = null): int
    {
        $products = Product::withTrashed()->whereIn('id', $ids)->get();
        $deleted = Product::whereIn('id', $ids)->delete();

        foreach ($products as $product) {
            $this->logAction($actor, 'products.bulk_delete', $product, 'product::product.audit.bulk_deleted', 'warning');
        }

        return $deleted;
    }

    public function uploadGallery(?Product $product, string $uploadToken, UploadedFile $file, ?User $actor = null): ProductGallery
    {
        $media = $this->uploader->upload($file, 'products/gallery', [
            'max_width' => 1600,
        ]);

        return ProductGallery::create([
            'product_id' => $product?->id,
            'upload_token' => $product?->id ? null : $uploadToken,
            'image_path' => $media->path(),
            'uploaded_by' => $actor?->id,
        ]);
    }

    public function removeGallery(ProductGallery $gallery): void
    {
        if ($gallery->image_path && Storage::disk('public')->exists($gallery->image_path)) {
            Storage::disk('public')->delete($gallery->image_path);
        }

        $gallery->delete();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function preparePayload(array $data, ?Product $product = null, ?User $actor = null): array
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image_path'] = $this->uploader->upload($data['image'], 'products/cover', [
                'max_width' => 1200,
            ])->path();
        }

        unset($data['image']);

        $data['title'] = array_filter($data['title'] ?? []);
        $data['description'] = array_filter($data['description'] ?? []);

        if (empty($data['title'])) {
            $data['title'] = $product?->getRawOriginal('title') ?? [];
        }

        if (empty($data['description'])) {
            $data['description'] = $product?->getRawOriginal('description') ?? [];
        }

        $data['price'] = (float) ($data['price'] ?? 0);
        $data['qty'] = (int) ($data['qty'] ?? 0);
        $data['is_featured'] = filter_var($data['is_featured'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $data['is_new_arrival'] = filter_var($data['is_new_arrival'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $data['is_trending'] = filter_var($data['is_trending'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $data['position'] = (int) ($data['position'] ?? 0);
        $data['category_id'] = $data['category_id'] ?? null;
        $data['brand_id'] = $data['brand_id'] ?? null;

        if (isset($data['offer_type']) && $data['offer_type'] === '') {
            $data['offer_type'] = null;
        }

        if (empty($data['offer_price'])) {
            $data['offer_price'] = null;
        }

        if (empty($data['offer_starts_at'])) {
            $data['offer_starts_at'] = null;
        }

        if (empty($data['offer_ends_at'])) {
            $data['offer_ends_at'] = null;
        }

        if (! $product) {
            $data['created_by'] = $actor?->id;
        }

        return $data;
    }

    protected function attachGallery(Product $product, ?string $token, ?User $actor = null): void
    {
        if (! $token) {
            return;
        }

        ProductGallery::where('upload_token', $token)
            ->update([
                'product_id' => $product->id,
                'upload_token' => null,
                'uploaded_by' => $actor?->id,
            ]);
    }

    /**
     * @param  array<int>  $tagIds
     */
    protected function syncTags(Product $product, array $tagIds): void
    {
        $ids = collect($tagIds)
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->all();

        $product->tags()->sync($ids);
    }

    protected function logAction(?User $actor, string $action, Product $product, string $translationKey, string $level = 'info'): void
    {
        $title = $product->title ?? ('#'.$product->id);

        $this->audit->log(
            $actor?->id,
            $action,
            __($translationKey, ['title' => $title]),
            [
                'context' => 'products',
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
