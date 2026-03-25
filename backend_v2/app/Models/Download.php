<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'organisation',
        'designation',
        'country',
        'purpose',
        'token',
        'token_expires_at'
    ];
}
