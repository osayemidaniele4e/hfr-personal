<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HospitalServiceMaster extends Model
{
    protected $table = 'lst_hosp_services';

    public function ServiceCategory()
    {
        return $this->belongsTo('App\HospitalServiceCategory','service_category_id');
    }
}
