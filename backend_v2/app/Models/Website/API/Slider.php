<?php

namespace App\Models\Website\API;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = [
        'image_url','title', 'sub_title','status',
    ];
}
