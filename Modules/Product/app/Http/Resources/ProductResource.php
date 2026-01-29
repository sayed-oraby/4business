<?php

namespace Modules\Product\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'title_translations' => $this->title_translations ?? [],
            'description' => $this->description,
            'description_translations' => $this->description_translations ?? [],
            'sku' => $this->sku,
            'price' => $this->price !== null ? (float) $this->price : null,
            'qty' => (int) ($this->qty ?? 0),
            'status' => $this->status,
            'status_label' => __('product::product.statuses.' . $this->status),
            'is_featured' => (bool) $this->is_featured,
            'is_new_arrival' => (bool) $this->is_new_arrival,
            'is_trending' => (bool) $this->is_trending,
            'position' => (int) ($this->position ?? 0),
            'image_url' => setting_media_url($this->image_path),
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category?->id,
                'title' => $this->category?->title,
            ]),
            'brand' => $this->whenLoaded('brand', fn () => [
                'id' => $this->brand?->id,
                'title' => $this->brand?->title,
            ]),
            'tags' => $this->whenLoaded('tags', fn () => ProductTagResource::collection($this->tags)->resolve()),
            'galleries' => $this->whenLoaded('galleries', fn () => ProductGalleryResource::collection($this->galleries)->resolve()),
            'offer' => [
                'type' => $this->offer_type,
                'price' => $this->offer_price !== null ? (float) $this->offer_price : null,
                'starts_at' => optional($this->offer_starts_at)->toIso8601String(),
                'ends_at' => optional($this->offer_ends_at)->toIso8601String(),
            ],
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
            'deleted_at' => optional($this->deleted_at)->toIso8601String(),
            'is_deleted' => method_exists($this, 'trashed') ? $this->trashed() : false,
        ];
    }
}
