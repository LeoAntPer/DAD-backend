<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateTransactionRequest extends FormRequest
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
            'vcard' => 'required|exists:vcard,id',
            'type' => 'required|in:C,D',
            'value' => [
                'required',
                'numeric',
                'gt:0',
                Rule::exists('vcard', 'balance')->where(function ($query) {
                    $query->where('max_debit', '<', $this->input('value'));
                })
            ],
            'old_balance' => 'required|numeric',
            'new_balance' => 'required|numeric',
            'payment_type' => 'required|in:VCARD,MBWAY,PAYPAL,IBAN,MB,VISA',
            'payment_reference' => $this->payment_reference_rules(),
            'category_id' => 'required|exists:category,id',
            'description' => 'nullable|string|max:255',
            'custom_options' => 'nullable',
            'custom_data' => 'nullable',
        ];
    }

    protected function payment_reference_rules()
    {
        $payment_type = $this->input('payment_type');

        if ($payment_type == 'VCARD') {
            return 'required|numeric|regex:/^9\d{8}$/|exists:vcard,phone_number';
        } elseif ($payment_type == 'MBWAY') {
            return 'required|numeric|regex:/^4\d{8}$/';
        } elseif ($payment_type == 'PAYPAL') {
            return 'required|email';
        } elseif ($payment_type == 'IBAN') {
            return 'required|regex:/^[A-Za-z]{2}\d{23}$/';
        } elseif ($payment_type == 'MB') {
            return 'required|numeric|regex:/^\d{5}-\d{9}$/';
        } else {
            return 'required|numeric|regex:/^4\d{15}$/';
        }
    }
}
