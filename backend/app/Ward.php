<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    protected $table = 'ou_wards';

    public function lga()
    {
        return $this->belongsTo('App\Lga','lga_id');
    }
}
