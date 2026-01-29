<?php

namespace Modules\Product\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductGalleryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'image_url' => setting_media_url($this->image_path),
            'sort_order' => (int) ($this->sort_order ?? 0),
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
