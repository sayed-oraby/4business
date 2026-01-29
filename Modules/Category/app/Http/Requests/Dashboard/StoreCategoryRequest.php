<?php

namespace Modules\Category\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Category\Models\Category;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('categories.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'array'],
            'title.en' => ['required_without:title.ar', 'string', 'max:255'],
            'title.ar' => ['required_without:title.en', 'string', 'max:255'],
            'status' => ['required', 'in:'.implode(',', Category::STATUSES)],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'is_featured' => ['nullable', 'boolean'],
            'featured_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'position' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'image' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => __('category::category.form.title'),
            'title.en' => __('category::category.form.title_en'),
            'title.ar' => __('category::category.form.title_ar'),
            'status' => __('category::category.form.status'),
            'parent_id' => __('category::category.form.parent'),
            'is_featured' => __('category::category.form.is_featured'),
            'featured_order' => __('category::category.form.featured_order'),
            'position' => __('category::category.form.position'),
            'image' => __('category::category.form.image'),
        ];
    }

    public function messages(): array
    {
        // Ensure locale is set correctly
        $locale = app()->getLocale();
        
        return [
            'title.required' => trans('category::validation.title.required', [], $locale),
            'title.array' => trans('category::validation.title.array', [], $locale),
            'title.en.required_without' => trans('category::validation.title.en.required_without', [], $locale),
            'title.ar.required_without' => trans('category::validation.title.ar.required_without', [], $locale),
            'status.required' => trans('category::validation.status.required', [], $locale),
            'status.in' => trans('category::validation.status.in', [], $locale),
            'parent_id.integer' => trans('category::validation.parent_id.integer', [], $locale),
            'parent_id.exists' => trans('category::validation.parent_id.exists', [], $locale),
            'parent_id.not_in' => trans('category::validation.parent_id.not_in', [], $locale),
            'image.image' => trans('category::validation.image.image', [], $locale),
            'image.max' => trans('category::validation.image.max', [], $locale),
            'position.integer' => trans('category::validation.position.integer', [], $locale),
            'position.min' => trans('category::validation.position.min', [], $locale),
            'position.max' => trans('category::validation.position.max', [], $locale),
            'featured_order.integer' => trans('category::validation.featured_order.integer', [], $locale),
            'featured_order.min' => trans('category::validation.featured_order.min', [], $locale),
            'featured_order.max' => trans('category::validation.featured_order.max', [], $locale),
        ];
    }
}
