<?php

namespace Modules\Shipping\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class CountryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $flagValue = $this->flag_svg;

        if (! $flagValue && $this->iso2) {
            $flagValue = $this->isoToEmoji($this->iso2);
        }

        $flagUrl = $this->resolveFlagUrl($this->flag_svg) ?? $this->defaultFlagUrl($this->iso2);

        return [
            'id' => $this->id,
            'iso2' => $this->iso2,
            'iso3' => $this->iso3,
            'name_en' => $this->name_en,
            'name_ar' => $this->name_ar,
            'phone_code' => $this->phone_code,
            'flag' => $flagValue,
            'flag_url' => $flagUrl,
            'is_active' => (bool) $this->is_active,
            'is_shipping_enabled' => (bool) $this->is_shipping_enabled,
            'sort_order' => (int) $this->sort_order,
        ];
    }

    protected function resolveFlagUrl(?string $flag): ?string
    {
        if (! $flag) {
            return null;
        }

        if (filter_var($flag, FILTER_VALIDATE_URL)) {
            return $flag;
        }

        if (Str::startsWith($flag, ['data:image', 'data:'])) {
            return $flag;
        }

        if (Str::startsWith($flag, ['/'])) {
            return asset($flag);
        }

        if (Str::contains($flag, ['.svg', '.png', '.jpg', '.jpeg', '.webp'])) {
            return asset($flag);
        }

        return null;
    }

    protected function defaultFlagUrl(?string $iso2): ?string
    {
        if (! $iso2 || strlen($iso2) !== 2) {
            return null;
        }

        return sprintf('https://flagcdn.com/w40/%s.png', strtolower($iso2));
    }

    protected function isoToEmoji(?string $iso2): ?string
    {
        if (! $iso2 || strlen($iso2) !== 2) {
            return null;
        }

        $iso2 = strtoupper($iso2);
        $emoji = '';

        foreach (str_split($iso2) as $char) {
            $emoji .= mb_chr(0x1F1E6 - ord('A') + ord($char));
        }

        return $emoji;
    }
}
