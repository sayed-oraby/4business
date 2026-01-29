<?php

namespace Modules\Banner\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    /**
     * Default placeholder image for banners
     */
    protected const DEFAULT_IMAGE = 'metronic/media/svg/illustrations/easy/2.svg';

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $defaultImage = asset(self::DEFAULT_IMAGE);
        $imageUrl = setting_media_url($this->image_path, $defaultImage);

        return [
            'id' => $this->id,
            'image_url' => $imageUrl,
            'title' => $this->title,
            // 'title_translations' => $this->titleTranslations,
            'description' => $this->description,
            // 'description_translations' => $this->descriptionTranslations,
            'button' => [
                'label' => $this->button_label,
                'url' => $this->button_url,
            ],
            // 'placement' => $this->placement,
            // 'placement_label' => __('banner::banner.placements.'.($this->placement ?? 'home_hero')),
            // 'status' => $this->status,
            // 'status_label' => __('banner::banner.statuses.'.($this->status ?? 'draft')),
            // 'schedule' => [
            //     'starts_at' => $this->starts_at?->toISOString(),
            //     'ends_at' => $this->ends_at?->toISOString(),
            // ],
            // 'sort_order' => $this->sort_order,
            // 'is_deleted' => $this->trashed(),
            // 'created_at' => $this->created_at?->toISOString(),
            // 'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
