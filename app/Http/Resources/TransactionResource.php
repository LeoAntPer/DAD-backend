<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [ //TODO
            "id" => $this->id,
            "vcard" => $this->vcard,
            "vcard_name" => $this->transaction_vcard->name,
            "date" => $this->date,
            "datetime" => $this->datetime,
            "type" => $this->type,
            "value" => $this->value,
            "old_balance" => $this->old_balance,
            "new_balance" => $this->new_balance,
            "payment_type" => $this->payment_type,
            "payment_reference" => $this->payment_reference,
            "pair_transaction" => $this->pair_transaction,
            "pair_vcard" => $this->pair_vcard,
            "pair_vcard_name" => $this->pair_transaction_vcard ? $this->pair_transaction_vcard->name : '',
            "category_id" => $this->category_id,
            "category_name" => $this->category ? $this->category->name : '',
            "description" => $this->description,
            "custom_options" => $this->custom_options,
            "custom_data" => $this->custom_data
        ];
    }
}
