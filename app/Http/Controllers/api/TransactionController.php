<?php

namespace App\Http\Controllers\api;

use App\Models\Transaction;
use App\Http\Controllers\Controller;
use App\Models\Vcard;
use App\Http\Resources\TransactionResource;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use Illuminate\Support\Carbon;

class TransactionController extends Controller
{
    public function index()
    {
        return TransactionResource::collection(Transaction::withTrashed()->get());
    }

    public function store(StoreTransactionRequest $request)
    {
        $date = Carbon::today();
        $datetime = Carbon::now();

        $formData = $request->validated();

        $currentBalance = Vcard::find($formData['vcard'])->balance;
        $pairCurrentBalance = Vcard::find($formData['pair_vcard'])->balance;

        $formData['date'] = $date;
        $formData['datetime'] = $datetime;
        $formData['type'] = 'D';
        $formData['old_balance'] = $currentBalance;
        $formData['new_balance'] = $currentBalance - $formData['value'];

        $newTransaction = Transaction::create($formData);

        $newPairTransaction = Transaction::create([
            'vcard' => $newTransaction->pair_vcard,
            'date' => $date,
            'datetime' => $datetime,
            'type' => 'C',
            'value' => $newTransaction->value,
            'old_balance' => $pairCurrentBalance,
            'new_balance' => $pairCurrentBalance + $newTransaction->value,
            'payment_type' => $newTransaction->payment_type,
            'payment_reference' => $newTransaction->payment_reference,
            'pair_transaction' => $newTransaction->id,
            'pair_vcard' => $newTransaction->vcard,
            'custom_options' => $newTransaction->custom_options,
            'custom_data' => $newTransaction->custom_data
        ]);

        $pairTransaction = $newPairTransaction->id;
        Transaction::where('id', $newTransaction->id)->update(['pair_transaction' => $pairTransaction]);

        Vcard::where('phone_number', $newTransaction->vcard)->update(['balance' => $newTransaction->new_balance]);
        Vcard::where('phone_number', $newTransaction->pair_vcard)->update(['balance' => $newPairTransaction->new_balance]);

        return new TransactionResource($newTransaction);
    }

    public function show(Transaction $transaction)
    {
        return new TransactionResource($transaction);
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $transaction->update($request->validated());
        return new TransactionResource($transaction);
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->pairVcard->trashed()) {
            $transaction->delete();
        }
        return new TransactionResource($transaction);
    }
}
