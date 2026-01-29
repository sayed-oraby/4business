<?php

namespace Modules\Blog\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Blog\Models\Blog;

class UpdateBlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        $blog = $this->route('blog');

        return $blog ? $this->user()?->can('blogs.update', $blog) ?? false : false;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'array'],
            'title.en' => ['nullable', 'string', 'max:255'],
            'title.ar' => ['nullable', 'string', 'max:255'],
            'short_description' => ['nullable', 'array'],
            'short_description.en' => ['nullable', 'string', 'max:500'],
            'short_description.ar' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:'.implode(',', Blog::STATUSES)],
            'image' => ['nullable', 'image', 'max:5120'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:blog_tags,id'],
            'gallery_token' => ['required', 'string', 'max:64'],
            'created_by' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => __('blog::blog.form.title_en'),
            'title.en' => __('blog::blog.form.title_en'),
            'title.ar' => __('blog::blog.form.title_ar'),
            'short_description.en' => __('blog::blog.form.short_description_en'),
            'short_description.ar' => __('blog::blog.form.short_description_ar'),
            'description.en' => __('blog::blog.form.description_en'),
            'description.ar' => __('blog::blog.form.description_ar'),
            'status' => __('blog::blog.form.status'),
            'image' => __('blog::blog.form.image'),
            'tags' => __('blog::blog.form.tags'),
            'gallery_token' => __('blog::blog.form.gallery'),
            'created_by' => __('blog::blog.form.author'),
        ];
    }

    public function messages(): array
    {
        // Ensure locale is set correctly
        $locale = app()->getLocale();
        
        return [
            'title.array' => trans('blog::validation.title.array', [], $locale),
            'title.en.string' => trans('blog::validation.title.en.string', [], $locale),
            'title.ar.string' => trans('blog::validation.title.ar.string', [], $locale),
            'title.en.max' => trans('blog::validation.title.en.max', [], $locale),
            'title.ar.max' => trans('blog::validation.title.ar.max', [], $locale),
            'short_description.array' => trans('blog::validation.short_description.array', [], $locale),
            'short_description.en.string' => trans('blog::validation.short_description.en.string', [], $locale),
            'short_description.ar.string' => trans('blog::validation.short_description.ar.string', [], $locale),
            'short_description.en.max' => trans('blog::validation.short_description.en.max', [], $locale),
            'short_description.ar.max' => trans('blog::validation.short_description.ar.max', [], $locale),
            'description.array' => trans('blog::validation.description.array', [], $locale),
            'description.en.string' => trans('blog::validation.description.en.string', [], $locale),
            'description.ar.string' => trans('blog::validation.description.ar.string', [], $locale),
            'status.in' => trans('blog::validation.status.in', [], $locale),
            'image.image' => trans('blog::validation.image.image', [], $locale),
            'image.max' => trans('blog::validation.image.max', [], $locale),
            'tags.array' => trans('blog::validation.tags.array', [], $locale),
            'tags.*.integer' => trans('blog::validation.tags.*.integer', [], $locale),
            'tags.*.exists' => trans('blog::validation.tags.*.exists', [], $locale),
            'gallery_token.required' => trans('blog::validation.gallery_token.required', [], $locale),
            'gallery_token.string' => trans('blog::validation.gallery_token.string', [], $locale),
            'gallery_token.max' => trans('blog::validation.gallery_token.max', [], $locale),
            'created_by.integer' => trans('blog::validation.created_by.integer', [], $locale),
            'created_by.exists' => trans('blog::validation.created_by.exists', [], $locale),
        ];
    }
}
