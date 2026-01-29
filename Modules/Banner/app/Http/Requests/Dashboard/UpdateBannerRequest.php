<?php

namespace Modules\Banner\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Banner\Models\Banner;

class UpdateBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        $banner = $this->route('banner');

        return $banner
            ? $this->user()?->can('banners.update', $banner) ?? false
            : false;
    }

    public function rules(): array
    {
        $placements = array_keys(config('banner.placements', []));

        return [
            'image' => ['nullable', 'image', 'max:4096'],
            'title' => ['sometimes', 'array'],
            'title.en' => ['nullable', 'string', 'max:255'],
            'title.ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'button_label' => ['nullable', 'string', 'max:120'],
            'button_url' => ['nullable', 'url'],
            // 'placement' => ['sometimes', 'string', 'max:100', $placements ? Rule::in($placements) : 'string'],
            // 'starts_at' => ['nullable', 'date'],
            // 'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'status' => ['sometimes', Rule::in(Banner::STATUSES)],
            // 'sort_order' => ['nullable', 'integer', 'min:0', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'image' => __('banner::banner.form.image'),
            'title' => __('banner::banner.form.title_en'),
            'title.en' => __('banner::banner.form.title_en'),
            'title.ar' => __('banner::banner.form.title_ar'),
            'description.en' => __('banner::banner.form.description_en'),
            'description.ar' => __('banner::banner.form.description_ar'),
            'button_label' => __('banner::banner.form.button_label'),
            'button_url' => __('banner::banner.form.button_url'),
            'placement' => __('banner::banner.form.placement'),
            'starts_at' => __('banner::banner.form.starts_at'),
            'ends_at' => __('banner::banner.form.ends_at'),
            'status' => __('banner::banner.form.status'),
            'sort_order' => __('banner::banner.form.sort_order'),
        ];
    }

    public function messages(): array
    {
        // Ensure locale is set correctly
        $locale = app()->getLocale();
        
        return [
            'image.image' => trans('banner::validation.image.image', [], $locale),
            'image.max' => trans('banner::validation.image.max', [], $locale),
            'title.array' => trans('banner::validation.title.array', [], $locale),
            'title.en.string' => trans('banner::validation.title.en.string', [], $locale),
            'title.ar.string' => trans('banner::validation.title.ar.string', [], $locale),
            'title.en.max' => trans('banner::validation.title.en.max', [], $locale),
            'title.ar.max' => trans('banner::validation.title.ar.max', [], $locale),
            'description.array' => trans('banner::validation.description.array', [], $locale),
            'description.en.string' => trans('banner::validation.description.en.string', [], $locale),
            'description.ar.string' => trans('banner::validation.description.ar.string', [], $locale),
            'button_label.string' => trans('banner::validation.button_label.string', [], $locale),
            'button_label.max' => trans('banner::validation.button_label.max', [], $locale),
            'button_url.url' => trans('banner::validation.button_url.url', [], $locale),
            'placement.string' => trans('banner::validation.placement.string', [], $locale),
            'placement.max' => trans('banner::validation.placement.max', [], $locale),
            'placement.in' => trans('banner::validation.placement.in', [], $locale),
            'starts_at.date' => trans('banner::validation.starts_at.date', [], $locale),
            'ends_at.date' => trans('banner::validation.ends_at.date', [], $locale),
            'ends_at.after_or_equal' => trans('banner::validation.ends_at.after_or_equal', [], $locale),
            'status.in' => trans('banner::validation.status.in', [], $locale),
            'sort_order.integer' => trans('banner::validation.sort_order.integer', [], $locale),
            'sort_order.min' => trans('banner::validation.sort_order.min', [], $locale),
            'sort_order.max' => trans('banner::validation.sort_order.max', [], $locale),
        ];
    }
}
