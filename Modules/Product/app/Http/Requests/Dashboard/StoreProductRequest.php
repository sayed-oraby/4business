<?php

namespace Modules\Product\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Product\Models\Product;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('products.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'array'],
            'title.en' => ['required_without:title.ar', 'string', 'max:255'],
            'title.ar' => ['required_without:title.en', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'price' => ['required', 'numeric', 'min:0'],
            'qty' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:'.implode(',', Product::STATUSES)],
            'is_featured' => ['nullable', 'boolean'],
            'is_new_arrival' => ['nullable', 'boolean'],
            'is_trending' => ['nullable', 'boolean'],
            'position' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'offer_type' => ['nullable', Rule::in(['percentage', 'fixed'])],
            'offer_price' => ['nullable', 'numeric', 'min:0'],
            'offer_starts_at' => ['nullable', 'date'],
            'offer_ends_at' => ['nullable', 'date', 'after_or_equal:offer_starts_at'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:product_tags,id'],
            'image' => ['nullable', 'image', 'max:5120'],
            'gallery_token' => ['required', 'string', 'max:64'],
        ];
    }
}
