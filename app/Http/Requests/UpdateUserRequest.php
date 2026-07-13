<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */

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

