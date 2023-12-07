<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'vcard',
        'type',
        'name',
        'custom_options',
        'custom_data'
    ];

    function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    function vcard()
    {
        return $this->belongsTo(Vcard::class, 'vcard', 'phone_number');
    }
}
