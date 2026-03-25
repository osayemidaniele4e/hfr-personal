<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    protected $table = 'ou_wards';

    public function lga()
    {
        return $this->belongsTo(Lga::class, 'lga_id');
    }
}
