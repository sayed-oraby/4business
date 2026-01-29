<?php

namespace Modules\Product\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Product\Models\Product;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        $product = $this->route('product');

        return $product ? $this->user()?->can('products.update', $product) ?? false : false;
    }

    public function rules(): array
    {
        $product = $this->route('product');

        return [
            'title' => ['sometimes', 'array'],
            'title.en' => ['nullable', 'string', 'max:255'],
            'title.ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'sku' => ['sometimes', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($product?->id)],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'qty' => ['sometimes', 'integer', 'min:0'],
            'status' => ['sometimes', 'in:'.implode(',', Product::STATUSES)],
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
