<?php

namespace Modules\Post\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class MyPostSummaryResource extends JsonResource
{
    /**
     * Minimal data for "my posts" listing
     */
    public function toArray($request): array
    {
        $locale = app()->getLocale();

        $firstImage = $this->attachments->first();
        $imageUrl = $firstImage ? asset('storage/' . $firstImage->file_path) : asset('frontend/img/ad.png');

        $package = $this->package && $this->package->price > 0 ? $this->package : null;

        return [
            'id' => $this->uuid,

            'title' => $this->getTranslation('title', $locale),
            'description' => $this->getTranslation('description', $locale),

            'is_featured' => $package != null ? true : false,
            'is_fav' => auth()->guard('api')->check() ? \Modules\Post\Models\Fav::where('user_id', auth()->guard('api')->id())->where('post_id', $this->uuid)->exists() : false,

            'price' => $this->price,
            'is_price_contact' => (bool)$this->is_price_contact,

            'mobile_number' => $this->mobile_number,
            'whatsapp_number' => $this->whatsapp_number,

            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,
            'is_paid' => (bool) $this->is_paid,
            'requires_payment' => in_array($this->status, ['awaiting_payment', 'payment_failed']) && !$this->is_paid,

            'start_date' => Carbon::parse($this->start_date)->format('Y-m-d'),
            'end_date' => Carbon::parse($this->end_date)->format('Y-m-d'),

            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->title, // Uses custom accessor that returns localized value
                ];
            }),
            'post_type' => $this->whenLoaded('postType', function () use ($locale) {
                return [
                    'id' => $this->postType->id,
                    'name' => $this->postType->getTranslation('name', $locale),
                    'slug' => $this->postType->slug,
                ];
            }),
            'package' => $this->whenLoaded('package', function () use ($locale) {
                return [
                    'id' => $this->package->id,
                    'title' => $this->package->getTranslation('title', $locale),
                    'price' => $this->package->price,
                    'period_days' => $this->package->period_days,
                    'label_color' => $this->package->label_color,
                    'card_color' => $this->package->card_color,
                ];
            }),
            'state' => $this->whenLoaded('state', function () {
                return [
                    'id' => $this->state->id,
                    'name' => $this->state->name, // Uses custom accessor
                ];
            }),
            'city' => $this->whenLoaded('city', function () {
                return [
                    'id' => $this->city->id,
                    'name' => $this->city->name, // Uses custom accessor
                ];
            }),

            'image_url' => $imageUrl,

        ];
    }
}
