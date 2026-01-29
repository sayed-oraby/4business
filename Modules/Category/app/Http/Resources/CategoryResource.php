<?php

namespace Modules\Category\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Default placeholder image for categories
     */
    protected const DEFAULT_IMAGE = 'metronic/media/svg/files/folder-document.svg';

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Use setting_media_url with default fallback
        $defaultImage = asset(self::DEFAULT_IMAGE);
        $imageUrl = setting_media_url($this->image_path, $defaultImage);
        
        return [
            'id' => $this->id,
            'title' => $this->title,
            // 'title_translations' => $this->titleTranslations,
            // 'parent_id' => $this->parent_id,
            'parent' => $this->whenLoaded('parent', function () {
                return [
                    'id' => $this->parent->id,
                    'title' => $this->parent->title,
                    'title_translations' => $this->parent->titleTranslations,
                ];
            }),
            'image_url' => $imageUrl,
            // 'image_path' => $this->image_path,
            // 'status' => $this->status,
            // 'status_label' => __('category::category.statuses.' . ($this->status ?? 'draft')),
            // 'is_featured' => $this->is_featured,
            // 'featured_order' => $this->featured_order,
            // 'position' => $this->position,
            // 'created_at' => $this->created_at,
            // 'updated_at' => $this->updated_at?->format('Y-m-d H:i'),
        ];
    }
}
