<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateVcardRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'photo_url' => 'nullable|file|image',
            'password' => 'required|string|max:255',
            'confirmation_code' => 'nullable',
            'blocked' => 'required|boolean',
            'balance' => 'required|numeric',
            'max_debit' => 'required|numeric',
            'custom_options' => 'nullable',
            'custom_data' => 'nullable',
        ];
    }
}
