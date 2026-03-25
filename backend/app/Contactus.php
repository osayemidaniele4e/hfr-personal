<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contactus extends Model
{
    protected $table = 'contact_us';
    protected $fillable = ['full_name','email','subject','message'];
}
