<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imaging extends Model
{
    protected $table = 'im_imagings';
    protected $guarded = ["unique_id","start_date","operational_days"];

}
