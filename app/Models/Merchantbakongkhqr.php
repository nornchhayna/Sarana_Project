<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merchantbakongkhqr extends Model
{
    protected $fillable = [
        'bakongAccountID',
        'merchantName',
        'merchantCity',
        'currency',
        'countryCode',
    ];
}
