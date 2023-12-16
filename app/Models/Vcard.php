<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vcard extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $timestamps = false;
    protected $primaryKey = 'phone_number';

    protected $fillable = [
        'phone_number',
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'confirmation_code',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'confirmation_code' => 'hashed'
    ];

    public function getRouteKeyName(){
        return 'phone_number';
    }

    function transactions()
    {
        return $this->hasMany(Transaction::class, 'vcard', 'phone_number')->orderBy('datetime', 'desc');
    }

    function transactionsPair()
    {
        return $this->hasMany(Transaction::class, 'pair_vcard', 'phone_number');
    }

    function categories()
    {
        return $this->hasMany(Category::class, 'vcard', 'phone_number');
    }
}
