<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectRequest extends FormRequest
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
            'nomi' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'kafedra_id' => 'nullable|exists:kafedra,id',
            'fakultet_id' => 'nullable|exists:fakultet,id',
            'oquv_yili_id' => 'nullable|string|max:255',
            'talim_tili' => 'nullable|string|max:255',
            'kredit' => 'nullable|integer|min:0',
            'teacher_id' => 'nullable|exists:users,id',
            'lesson_type_id' => 'nullable|exists:lesson_types,id',
            'semster' => 'required|integer|min:1|max:8',
        ];
    }
}
