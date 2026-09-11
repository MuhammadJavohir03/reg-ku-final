<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreElonRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

    
    public function rules(): array
    {
        return [
            'admin_id' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'short_content' => 'required|string|max:500',
            'full_content' => 'required|string',
            'photo' => 'nullable|image|max:2048',
            'category_id' => 'nullable|exists:categories,id',
            'kurs' => 'nullable|integer',
        ];
    }
}
