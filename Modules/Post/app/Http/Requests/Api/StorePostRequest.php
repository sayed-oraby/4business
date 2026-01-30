<?php

namespace Modules\Post\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function rules(): array
    {
        $isCreating = $this->isMethod('POST');
        
        return [
            'category_id' => 'required|exists:categories,id',
            'post_type_id' => 'required|exists:post_types,id',
            'package_id' => $isCreating ? 'required|exists:packages,id' : 'sometimes|exists:packages,id',
           
            'state_id' => 'required|exists:shipping_states,id',
            'city_id' => 'required|exists:shipping_cities,id',

            'title' => 'required|string', // JSON string or array
            'description' => 'required|string', // JSON string or array

            'price' => 'nullable',
            
            // 'years_of_experience' => 'required|integer|min:0',
            // 'nationality' => 'required|string',
            // 'gender' => 'required|in:male,female,both',
            // 'full_name' => 'required|string',

            'mobile_number' => 'required|string',

            // 'birthdate' => 'required|date',
            // 'display_personal_details' => 'boolean',
            
            // 'cover_image' => $isCreating ? 'required|image|max:2048' : 'nullable|image|max:2048',
            
            // 'skills' => 'nullable|array',
            // 'skills.*' => 'string', // Skill names to tag
            
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,png,jpg,jpeg|max:25600',

            'is_price_contact' => 'nullable|boolean',
            'whatsapp_number' => 'nullable|string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
