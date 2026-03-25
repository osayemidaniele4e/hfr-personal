<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{
    protected $table = 'lb_laboratories';
    protected $guarded = ["unique_id","start_date","operational_days","equipments"];
}
