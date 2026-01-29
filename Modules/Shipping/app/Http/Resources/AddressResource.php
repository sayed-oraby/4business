<?php

namespace Modules\Shipping\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'country' => $this->whenLoaded('country', fn () => new CountryResource($this->country)),
            'country_iso2' => $this->country_iso2,
            'state' => [
                'code' => $this->state_code,
                'name_en' => $this->state_name_en,
                'name_ar' => $this->state_name_ar,
            ],
            'city' => [
                'code' => $this->city_code,
                'name_en' => $this->city_name_en,
                'name_ar' => $this->city_name_ar,
            ],
            'block' => $this->block,
            'street' => $this->street,
            'avenue' => $this->avenue,
            'building' => $this->building,
            'floor' => $this->floor,
            'apartment' => $this->apartment,
            'postal_code' => $this->postal_code,
            'additional_details' => $this->additional_details,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'is_default_shipping' => $this->is_default_shipping,
            'is_default_billing' => $this->is_default_billing,
        ];
    }
}
