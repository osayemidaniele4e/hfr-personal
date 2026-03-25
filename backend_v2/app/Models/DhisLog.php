<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DhisLog extends Model
{
    protected $table = 'dhis_log';

    public function hospital()
{
    return $this->belongsTo(HospitalHistory::class, 'hfr_id', 'id');
}

}
