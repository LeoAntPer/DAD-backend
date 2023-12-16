<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVcardRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'string|min:3|max:50',
            'email' => 'email',
            'photo_url' => 'nullable|string',
            'password' => 'string|min:15',
            'confirmation_code' => 'size:3',
            'blocked' => 'boolean',
            'max_debit' => 'numeric',
            'custom_options' => 'nullable',
            'custom_data' => 'nullable',
        ];
    }
}
