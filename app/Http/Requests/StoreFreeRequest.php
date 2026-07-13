<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFreeRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'user_id' => 'require|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'bolim_id' => 'required|exists:bolims,id',
            'yakuniy_baho' => 'required|integer',
            'status' => 'required|boolean'
        ];
    }
}
