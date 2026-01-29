<?php

namespace Modules\Blog\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
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
            'short_description' => $this->short_description,
            'short_description_translations' => $this->short_description_translations,
            'description' => $this->description,
            'description_translations' => $this->description_translations,
            'image_url' => setting_media_url($this->image_path),
            'status' => $this->status,
            'status_label' => __('blog::blog.statuses.' . $this->status),
            'state_label' => $this->trashed()
                ? __('blog::blog.states.archived')
                : __('blog::blog.states.active'),
            'tags' => BlogTagResource::collection($this->whenLoaded('tags')),
            'author' => $this->whenLoaded('creator', fn () => [
                'id' => $this->creator?->id,
                'name' => $this->creator?->name,
                'email' => $this->creator?->email,
            ]),
            'gallery' => BlogGalleryResource::collection($this->whenLoaded('galleries')),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
            'deleted_at' => optional($this->deleted_at)->toIso8601String(),
            'is_deleted' => $this->trashed(),
        ];
    }
}
