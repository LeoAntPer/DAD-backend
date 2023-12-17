<?php

namespace App\Http\Controllers\api;

use App\Models\Transaction;
use App\Http\Controllers\Controller;
use App\Models\Vcard;
use App\Http\Resources\TransactionResource;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;

class TransactionController extends Controller
{
    public function index()
    {
        return TransactionResource::collection(Transaction::withTrashed()->get());
    }

    public function store(StoreTransactionRequest $request)
    {
        // check valid confirmation code
        $formData = $request->validated();
        if ($formData['type'] == 'D' && !Hash::check($formData['confirmation_code'], VCard::find($formData['vcard'])->confirmation_code)) {
            $formattedError = [
                'message' => 'Invalid confirmation code',
                'errors' => [
                    'confirmation_code' => [
                        'Incorret confirmation code.'
                    ]
                ], 
            ];
            return response()->json($formattedError, 422);
        }
        
        
        $date = Carbon::today();
        $datetime = Carbon::now();

        $formData['date'] = $date;
        $formData['datetime'] = $datetime;
        
        $currentBalance = Vcard::find($formData['vcard'])->balance;
        $formData['old_balance'] = $currentBalance;

        $formData['new_balance'] = $formData['type'] == 'D' ? 
            $currentBalance - $formData['value'] : $currentBalance + $formData['value'];;
        
        try {
            $newTransaction = DB::transaction(function () use ($formData) {
                //dd($formData);
                $newTransaction = Transaction::create($formData);
                
                if ($newTransaction->payment_type == 'VCARD' && $newTransaction->type == 'D') { // transaction vCard => vCard
                    $newTransaction->pair_vcard = $newTransaction->payment_reference;
                    $newTransaction->save();
                    $pairCurrentBalance = Vcard::find($newTransaction->pair_vcard)->balance;
                    $newPairTransaction = Transaction::create([
                        'vcard' => $newTransaction->pair_vcard,
                        'date' => $newTransaction->date,
                        'datetime' => $newTransaction->datetime,
                        'type' => 'C',
                        'value' => $newTransaction->value,
                        'old_balance' => $pairCurrentBalance,
                        'new_balance' => $pairCurrentBalance + $newTransaction->value,
                        'payment_type' => $newTransaction->payment_type,
                        'payment_reference' => $newTransaction->vcard,
                        'pair_transaction' => $newTransaction->id,
                        'pair_vcard' => $newTransaction->vcard,
                        'custom_options' => $newTransaction->custom_options,
                        'custom_data' => $newTransaction->custom_data
                    ]);
                    
                    $pairTransaction = $newPairTransaction->id;
                    Transaction::where('id', $newTransaction->id)->update(['pair_transaction' => $pairTransaction]);
                    Transaction::where('id', $pairTransaction)->update(['pair_transaction' => $newTransaction->id]);
                    Vcard::where('phone_number', $newTransaction->pair_vcard)->update(['balance' => $newPairTransaction->new_balance]);
                }
                else { // transaction Payment Gateway Service
                    $pamentGatewayServiceUrl = 'https://dad-202324-payments-api.vercel.app/api/';
                    $operation = $formData['type'] == 'D' ? 'credit' : 'debit'; // opposite of vcard operation type
                    $requestUrl = $pamentGatewayServiceUrl.$operation;
    
                    $response = Http::post($requestUrl, [
                        'type' => $newTransaction->payment_type,
                        'reference' => $newTransaction->payment_reference,
                        'value' => $newTransaction->value
                    ]);
    
                    if ($response->status() != 201) {
                        throw new \Exception(json_encode($response->json('message', 'Unknow error message')));
                    }
                }
        
                Vcard::where('phone_number', $newTransaction->vcard)->update(['balance' => $newTransaction->new_balance]);
                return $newTransaction;
            });

            return new TransactionResource($newTransaction);
        }
        catch (\Exception $e) {
            // Format the error message from PGS
            $isReferenceRelated = "reference";
            if (strpos($e->getMessage(), $isReferenceRelated) !== false) {
                $message = 'The payment reference must be a valid reference.';
                $field = 'payment_reference';
            } else {
                $message = 'The value limit was exceeded.';
                $field = 'value';
            }
            $formattedError = [
                'message' => $message,
                'errors' => [
                    $field => [
                        json_decode($e->getMessage())
                    ]
                ], 
            ];

            return response()->json($formattedError, 422);
        }
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

    public function getTransactionsOfVCard(Vcard $vcard) {
        return TransactionResource::collection($vcard->transactions);
    }

    public function getLatestVCardTransactions(Vcard $vcard)
    {
        return TransactionResource::collection(Transaction::where('vcard',$vcard->phone_number)->latest()->limit(3)->get());
    }
}
