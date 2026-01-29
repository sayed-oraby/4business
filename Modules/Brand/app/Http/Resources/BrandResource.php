<?php

namespace Modules\Brand\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'title_translations' => $this->title_translations,
            'status' => $this->status,
            'status_label' => __('brand::brand.statuses.' . $this->status),
            'image_url' => setting_media_url($this->image_path),
            'position' => $this->position,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
            'deleted_at' => optional($this->deleted_at)->toIso8601String(),
            'is_deleted' => $this->trashed(),
        ];
    }
}
