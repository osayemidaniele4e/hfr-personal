<?php

namespace App\Website\API;

use Illuminate\Database\Eloquent\Model;

class AboutUs extends Model
{
    protected $table = 'contact_us'; // Explicitly defining the table name

    protected $fillable = [
        'full_name',
        'email',
        'subject',
        'message',
    ];
}
