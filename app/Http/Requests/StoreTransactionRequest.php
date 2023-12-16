<?php

namespace App\Http\Requests;

use App\Models\Vcard;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
        $vcard = Vcard::find($this->input('vcard'));
        if ($vcard) {
            $max_debit = $vcard->max_debit;
            $balance = $vcard->balance;
        } else {
            $max_debit = 0;
            $balance = 0;
        }

        return [
            'vcard' => [
                'required',
                'exists:vcards,phone_number',
                function ($attribute, $value, $fail) {
                    // Convert the numeric 'vcard' to a string
                    $vcardAsString = (string) $value;
    
                    // Check if 'vcard' is different from 'payment_reference'
                    $paymentReference = $this->input('payment_reference');
                    if ($vcardAsString === $paymentReference) {
                        $fail("The $attribute and payment_reference must have different values.");
                    }
                },
            ],
            'value' => [
                'required',
                'numeric',
                'gte:0.01',
                'lte:' . $max_debit,
                'lte:' . $balance,
            ],
            'type' => 'required|in:D,C',
            'payment_type' => 'required|in:VCARD,MBWAY,PAYPAL,IBAN,MB,VISA',
            'payment_reference' => $this->payment_reference_rules(),
            'pair_vcard' => 'nullable|exists:vcards,phone_number',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string|max:255',
            'custom_options' => 'nullable',
            'custom_data' => 'nullable',
        ];
    }

    protected function payment_reference_rules()
    {
        $payment_type = $this->input('payment_type');

        if ($payment_type == 'VCARD') {
            return [
                'required',
                'numeric',
                'regex:/^9\d{8}$/',
                'exists:vcards,phone_number',
                function ($attribute, $value, $fail) {
                    // Convert the numeric 'vcard' to a string
                    $vcardAsString = (string) $this->input('vcard');
    
                    // Check if 'vcard' is different from 'payment_reference'
                    $paymentReference = $value;
                    if ($vcardAsString === $paymentReference) {
                        $fail("The $attribute and payment_reference must have different values.");
                    }
                },
            ];
        } elseif ($payment_type == 'MBWAY') {
            return 'required|numeric|regex:/^9\d{8}$/';
        } elseif ($payment_type == 'PAYPAL') {
            return 'required|email';
        } elseif ($payment_type == 'IBAN') {
            return 'required|regex:/^[A-Za-z]{2}\d{23}$/';
        } elseif ($payment_type == 'MB') {
            return 'required|regex:/^\d{5}-\d{9}$/';
        } else {
            return 'required|numeric|regex:/^4\d{15}$/';
        }
    }

    public function messages(): array
    {
        return [
            'vcard' => 'The vcard must be a valid phone number.',
            'value' => 'The value must be a valid number.',
            'payment_type' => 'The payment type must be a valid type.',
            'payment_reference' => 'The payment reference must be a valid reference.',
            'pair_vcard' => 'The pair vcard must be a valid phone number.',
            'category_id' => 'The category id must be a valid id.'
        ];
    }
}
