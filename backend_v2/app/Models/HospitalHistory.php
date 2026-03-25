<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;


class HospitalHistory extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'hs_hospitals_history';
    protected $guarded = ["unique_id", "image_url", "start_date", "close_date", "operational_days", "status_id", "created_by", "services"];
    protected $auditExclude = [
        'status_id',
        'created_by',
        'requested_by',
        'request_note',
        'requested_at',
        'verified_by',
        'verified_at',
        'verify_note',
        'validated_by',
        'validated_at',
        'validate_note',
        'published_by',
        'published_at',
        'publish_note',
    ];

       public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function lga()
    {
        return $this->belongsTo(Lga::class, 'lga_id');
    }

    public function ward(){
        return $this->belongsTo(Ward::class, 'ward_id');
    }

    public function ownership(){
        return $this->belongsTo(Ownership::class, 'ownership_id');
    }

    public function facilitylevelofcare(){
        return $this->belongsTo(FacilityLevelOfCare::class, 'facility_level_id');
    }

    public function facilitylevelofcareoption(){
        return $this->belongsTo(FacilityLevelOfCareOption::class, 'facility_level_option_id');
    }

    public function publishedby(){
        return $this->belongsTo(User::class, 'published_by');
    }

    

    public function arrayValuesTostring($val)
    {
        if (is_array($val)) {
            $str = implode(',', $val);
        } else {
            $str = "";
        }
        return $str;
    }

    public function generateFacilityCode($lga_id, $type, $level, $ownership)
    {
        //get state and lga code
        $state_lga = DB::select("Select concat(state_code,'/',lga_code) code from ou_lgas where
                    id = " . $lga_id . "");

        //get the largest serial number in lga
        $max_sn = DB::select("SELECT MAX(CAST(substring(unique_id,length(unique_id)-3,4) as unsigned)) val FROM hs_hospitals_history where
                    lga_id = " . $lga_id . "");

        $sn = $max_sn[0]->val + 1; //get serial number of the next HF in LGA


        $id = $state_lga[0]->code . "/" . $type . "/" . $level . "/" . $ownership . "/";

        if (strlen($sn) == 1) {
            $id = $id . '000' . $sn;
        } elseif (strlen($sn) == 2) {
            $id = $id . '00' . $sn;
        } elseif (strlen($sn) == 3) {
            $id = $id . '0' . $sn;
        } else {
            $id = $id . $sn;
        }

        return $id;
    }

    public function generateUID()
    {
        $id = DB::select('SELECT FLOOR((RAND() * (87654321-12345678))+12345678) AS id
                    FROM hs_hospitals_history
                    WHERE "id" NOT IN (SELECT id FROM hs_hospitals_history)
                    LIMIT 1');


        $uid = $id[0]->id;

        return (int)$uid;
    }


    function array_equal($a, $b)
    {
        return (is_array($a) && is_array($b) && array_diff($a, $b) === array_diff($b, $a));
    }
}
