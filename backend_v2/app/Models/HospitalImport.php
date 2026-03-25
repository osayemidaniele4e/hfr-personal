<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalImport extends Model
{
    protected $table = 'hospital_imports';

    protected $fillable = [
        'batch_id',
        'uploaded_by',
        'state_unique_id',
        'registration_no',
        'start_date',
        'close_date',
        'facility_name',
        'alt_facility_name',
        'state_id',
        'lga_id',
        'ward_id',
        'ownership_id',
        'ownership_type_id',
        'facility_level_id',
        'facility_level_option_id',
        'facility_level_options_category_id',
        'physical_location',
        'postal_address',
        'longitude',
        'latitude',
        'phone_number',
        'alternate_number',
        'email_address',
        'website',
        'operational_days',
        'operational_hours',
        'operational_status_id',
        'registration_status_id',
        'license_status_id',
        'outpatient',
        'inpatient',
        'doctors',
        'pharmacists',
        'dentist',
        'pharmacy_technicians',
        'nurses',
        'lab_scientists',
        'midwifes',
        'lab_technicians',
        'nurse_midwife',
        'him_officers',
        'community_health_officer',
        'community_extension_workers',
        'jun_community_extension_worker',
        'dental_technicians',
        'env_health_officers',
        'attendants',
        'beds',
        'onsite_laboratory',
        'onsite_imaging',
        'onsite_pharmarcy',
        'mortuary_services',
        'ambulance_services',
        'validation_errors',
        'is_valid',
    ];

    protected $casts = [
        'is_valid' => 'boolean',
        'start_date' => 'date',
        'close_date' => 'date',
    ];

    /**
     * Scope to get records for a specific batch
     */
    public function scopeBatch($query, $batchId)
    {
        return $query->where('batch_id', $batchId);
    }

    /**
     * Get the state name
     */
    public function state()
    {
        return $this->belongsTo(\App\Models\State::class ?? Model::class, 'state_id');
    }
}
