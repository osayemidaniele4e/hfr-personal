<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    protected $fillable = ['firstname','lastname','email','organisation','designation','country','purpose'];
}
