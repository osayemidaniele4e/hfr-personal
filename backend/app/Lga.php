<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lga extends Model
{
    protected $table = 'ou_lgas';

    public function state()
    {
        return $this->belongsTo('App\State','state_id');
    }
}
