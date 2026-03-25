<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lga extends Model
{
    protected $table = 'ou_lgas';

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }
}
