<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVcardRequest extends FormRequest
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
            'phone_number' => 'required|size:9',
            'name' => 'required|string|min:3|max:20',
            'email' => 'required|email',
            'photo_url' => 'nullable|file|image',
            'password' => 'required|string|min:10',
            'confirmation_code' => 'required|size:3',
            'blocked' => 'required|boolean',
            'balance' => 'required|numeric|in:0',
            'max_debit' => 'required|numeric|gt:0|lt:5001',
            'custom_options' => 'nullable',
            'custom_data' => 'nullable',
        ];
    }
}
