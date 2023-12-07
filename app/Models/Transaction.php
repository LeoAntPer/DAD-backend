<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'vcard',
        'date',
        'datetime',
        'type',
        'value',
        'old_balance',
        'new_balance',
        'payment_type',
        'payment_reference',
        'pair_vcard',
        'category_id',
        'description',
        'custom_options',
        'custom_data'
    ];

    function category()
    {
        return $this->belongsTo(Category::class);
    }

    function transaction_vcard()
    {
        return $this->belongsTo(Vcard::class, 'vcard', 'phone_number');
    }

    function pair_transaction_vcard()
    {
        return $this->belongsTo(Vcard::class, 'pair_vcard', 'phone_number');
    }

    function pairTransaction()
    {
        return $this->belongsTo(Transaction::class, 'pair_transaction');
    }
}
