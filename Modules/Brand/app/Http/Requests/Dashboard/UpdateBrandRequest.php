<?php

namespace Modules\Brand\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Brand\Models\Brand;

class UpdateBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        $brand = $this->route('brand');

        return $brand ? $this->user()?->can('brands.update', $brand) ?? false : false;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'array'],
            'title.en' => ['nullable', 'string', 'max:255'],
            'title.ar' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'in:'.implode(',', Brand::STATUSES)],
            'position' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'image' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => __('brand::brand.form.title_en'),
            'title.en' => __('brand::brand.form.title_en'),
            'title.ar' => __('brand::brand.form.title_ar'),
            'status' => __('brand::brand.form.status'),
            'position' => __('brand::brand.form.position'),
            'image' => __('brand::brand.form.image'),
        ];
    }

    public function messages(): array
    {
        // Ensure locale is set correctly
        $locale = app()->getLocale();
        
        return [
            'title.array' => trans('brand::validation.title.array', [], $locale),
            'title.en.string' => trans('brand::validation.title.en.string', [], $locale),
            'title.ar.string' => trans('brand::validation.title.ar.string', [], $locale),
            'title.en.max' => trans('brand::validation.title.en.max', [], $locale),
            'title.ar.max' => trans('brand::validation.title.ar.max', [], $locale),
            'status.in' => trans('brand::validation.status.in', [], $locale),
            'position.integer' => trans('brand::validation.position.integer', [], $locale),
            'position.min' => trans('brand::validation.position.min', [], $locale),
            'position.max' => trans('brand::validation.position.max', [], $locale),
            'image.image' => trans('brand::validation.image.image', [], $locale),
            'image.max' => trans('brand::validation.image.max', [], $locale),
        ];
    }
}
