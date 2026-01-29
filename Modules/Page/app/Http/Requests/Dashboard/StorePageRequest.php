<?php

namespace Modules\Page\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Page\Models\Page;

class StorePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('pages.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'image' => ['nullable', 'image', 'max:4096'],
            'title' => ['required', 'array'],
            'title.en' => ['required_without:title.ar', 'string', 'max:255'],
            'title.ar' => ['required_without:title.en', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash'],
            'status' => ['required', 'in:'.implode(',', Page::STATUSES)],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => __('page::page.form.title_en'),
            'title.en' => __('page::page.form.title_en'),
            'title.ar' => __('page::page.form.title_ar'),
            'description.en' => __('page::page.form.description_en'),
            'description.ar' => __('page::page.form.description_ar'),
            'slug' => __('page::page.form.slug'),
            'status' => __('page::page.form.status'),
            'image' => __('page::page.form.image'),
        ];
    }

    public function messages(): array
    {
        // Ensure locale is set correctly
        $locale = app()->getLocale();
        
        return [
            'title.required' => trans('page::validation.title.required', [], $locale),
            'title.array' => trans('page::validation.title.array', [], $locale),
            'title.en.required_without' => trans('page::validation.title.en.required_without', [], $locale),
            'title.ar.required_without' => trans('page::validation.title.ar.required_without', [], $locale),
            'title.en.string' => trans('page::validation.title.en.string', [], $locale),
            'title.ar.string' => trans('page::validation.title.ar.string', [], $locale),
            'title.en.max' => trans('page::validation.title.en.max', [], $locale),
            'title.ar.max' => trans('page::validation.title.ar.max', [], $locale),
            'description.array' => trans('page::validation.description.array', [], $locale),
            'description.en.string' => trans('page::validation.description.en.string', [], $locale),
            'description.ar.string' => trans('page::validation.description.ar.string', [], $locale),
            'slug.string' => trans('page::validation.slug.string', [], $locale),
            'slug.max' => trans('page::validation.slug.max', [], $locale),
            'slug.alpha_dash' => trans('page::validation.slug.alpha_dash', [], $locale),
            'status.required' => trans('page::validation.status.required', [], $locale),
            'status.in' => trans('page::validation.status.in', [], $locale),
            'image.image' => trans('page::validation.image.image', [], $locale),
            'image.max' => trans('page::validation.image.max', [], $locale),
        ];
    }
}
