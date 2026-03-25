<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalServiceMaster extends Model
{
    protected $table = 'lst_hosp_services';

    public function ServiceCategory()
    {
        return $this->belongsTo(HospitalServiceCategory::class, 'service_category_id');
    }
}
