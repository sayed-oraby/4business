<?php

namespace Modules\Brand\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Brand\Models\Brand;

class StoreBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('brands.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'array'],
            'title.en' => ['required_without:title.ar', 'string', 'max:255'],
            'title.ar' => ['required_without:title.en', 'string', 'max:255'],
            'status' => ['required', 'in:'.implode(',', Brand::STATUSES)],
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
            'title.required' => trans('brand::validation.title.required', [], $locale),
            'title.array' => trans('brand::validation.title.array', [], $locale),
            'title.en.required_without' => trans('brand::validation.title.en.required_without', [], $locale),
            'title.ar.required_without' => trans('brand::validation.title.ar.required_without', [], $locale),
            'title.en.string' => trans('brand::validation.title.en.string', [], $locale),
            'title.ar.string' => trans('brand::validation.title.ar.string', [], $locale),
            'title.en.max' => trans('brand::validation.title.en.max', [], $locale),
            'title.ar.max' => trans('brand::validation.title.ar.max', [], $locale),
            'status.required' => trans('brand::validation.status.required', [], $locale),
            'status.in' => trans('brand::validation.status.in', [], $locale),
            'position.integer' => trans('brand::validation.position.integer', [], $locale),
            'position.min' => trans('brand::validation.position.min', [], $locale),
            'position.max' => trans('brand::validation.position.max', [], $locale),
            'image.image' => trans('brand::validation.image.image', [], $locale),
            'image.max' => trans('brand::validation.image.max', [], $locale),
        ];
    }
}
