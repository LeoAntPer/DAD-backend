<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vcard extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'email',
        'photo_url',
        'password',
        'confirmation_code',
        'blocked',
        'balance',
        'max_debit',
        'custom_options',
        'custom_data'
    ];

    function transactions()
    {
        return $this->hasMany(Transaction::class, 'vcard', 'phone_number');
    }

    function transactionsFrom()
    {
        return $this->hasMany(Transaction::class, 'pair_vcard', 'phone_number');
    }

    function categories()
    {
        return $this->hasMany(Category::class, 'vcard', 'phone_number');
    }
}
