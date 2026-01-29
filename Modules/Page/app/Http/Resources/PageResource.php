<?php

namespace Modules\Page\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'image_url' => setting_media_url($this->image_path),
            'status' => $this->status,
            'status_label' => __('page::page.statuses.' . $this->status),
            'state_label' => $this->trashed()
                ? __('page::page.states.archived')
                : __('page::page.states.active'),
            'title' => $this->title,
            'title_translations' => $this->title_translations,
            'description' => $this->description,
            'description_translations' => $this->description_translations,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
            'deleted_at' => optional($this->deleted_at)->toIso8601String(),
            'is_deleted' => $this->trashed(),
        ];
    }
}
