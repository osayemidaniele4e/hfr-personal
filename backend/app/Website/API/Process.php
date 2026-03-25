<?php

namespace App\Website\API;

use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    protected $fillable = [
        'title', 'content'
    ];
}
