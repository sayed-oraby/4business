<?php

namespace Modules\Page\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Page\Models\Page;

class UpdatePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $page = $this->route('page');

        return $page ? $this->user()?->can('pages.update', $page) ?? false : false;
    }

    public function rules(): array
    {
        return [
            'image' => ['nullable', 'image', 'max:4096'],
            'title' => ['sometimes', 'array'],
            'title.en' => ['nullable', 'string', 'max:255'],
            'title.ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash'],
            'status' => ['sometimes', 'in:'.implode(',', Page::STATUSES)],
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
            'title.array' => trans('page::validation.title.array', [], $locale),
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
            'status.in' => trans('page::validation.status.in', [], $locale),
            'image.image' => trans('page::validation.image.image', [], $locale),
            'image.max' => trans('page::validation.image.max', [], $locale),
        ];
    }
}
