<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Hospital extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'hs_hospitals_history';

    protected $guarded = [
        'unique_id',
        'start_date',
        'operational_days',
        'status_id',
        'created_by',
        'services',
    ];

    /**
     * Convert array values to comma-separated string
     */
    public function arrayValuesToString($val): string
    {
        return is_array($val) ? implode(',', $val) : '';
    }

    /**
     * Generate a unique facility code based on LGA and other details
     */
    public function generateFacilityCode($lga_id, $type, $level, $owner): string
    {
        $state_lga = DB::selectOne("
            SELECT CONCAT(state_code, '/', lga_code) AS code
            FROM ou_lgas
            WHERE id = ?
        ", [$lga_id]);

        $max_sn = DB::selectOne("
            SELECT MAX(CAST(SUBSTRING(unique_id, LENGTH(unique_id) - 3, 4) AS UNSIGNED)) AS val
            FROM hs_hospitals_history
            WHERE lga_id = ?
        ", [$lga_id]);

        $sn = ($max_sn->val ?? 0) + 1;

        $id = "{$state_lga->code}/{$type}/{$level}/{$owner}/" . str_pad($sn, 4, '0', STR_PAD_LEFT);

        return $id;
    }
}
