<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

    

    public function rules()
    {
        return [
            'Talaba_ID' => [
                'required',
                Rule::unique('users', 'Talaba_ID')->ignore($this->route('user')),
            ],
        ];
    }

    public function messages()
    {
        return [
            'Talaba_ID.unique' => 'Bu Talaba ID allaqachon mavjud!',
        ];
    }
}

