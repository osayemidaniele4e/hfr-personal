<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class FacilityResource extends JsonResource
{
    /**
     * Transform a facility (hospital) into a standardized API response.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'unique_id' => $this->unique_id ?? $this->state_unique_id ?? null,
            'registration_no' => $this->registration_no ?? null,
            'facility_name' => $this->facility_name,
            'alt_facility_name' => $this->alt_facility_name ?? null,

            'location' => [
                'state' => [
                    'id' => $this->state_id,
                    'name' => $this->state_name ?? null,
                ],
                'lga' => [
                    'id' => $this->lga_id,
                    'name' => $this->lga_name ?? null,
                ],
                'ward' => [
                    'id' => $this->ward_id,
                    'name' => $this->ward_name ?? null,
                ],
                'physical_location' => $this->physical_location ?? null,
                'postal_address' => $this->postal_address ?? null,
                'latitude' => $this->latitude ? (float) $this->latitude : null,
                'longitude' => $this->longitude ? (float) $this->longitude : null,
            ],

            'classification' => [
                'facility_type' => [
                    'id' => $this->facility_type_id ?? null,
                    'name' => $this->facility_type_name ?? null,
                ],
                'facility_level' => [
                    'id' => $this->facility_level_id ?? null,
                    'name' => $this->facility_level_name ?? null,
                ],
                'ownership' => [
                    'id' => $this->ownership_id ?? null,
                    'name' => $this->ownership_name ?? null,
                ],
                'ownership_type' => [
                    'id' => $this->ownership_type_id ?? null,
                    'name' => $this->ownership_type ?? null,
                ],
            ],

            'status' => [
                'operational' => [
                    'id' => $this->operational_status_id ?? null,
                    'name' => $this->operational_status_name ?? null,
                ],
                'registration' => [
                    'id' => $this->registration_status_id ?? null,
                    'name' => $this->registration_status_name ?? null,
                ],
                'license' => [
                    'id' => $this->license_status_id ?? null,
                    'name' => $this->license_status_name ?? null,
                ],
            ],

            'contact' => [
                'phone_number' => $this->phone_number ?? null,
                'email' => $this->email_address ?? null,
                'website' => $this->website ?? null,
            ],

            'operations' => [
                'operational_hours' => $this->operational_hours ?? null,
                'operational_days' => $this->operational_days ?? null,
                'outpatient' => $this->outpatient ?? null,
                'inpatient' => $this->inpatient ?? null,
                'beds' => $this->beds ? (int) $this->beds : null,
                'onsite_laboratory' => $this->onsite_laboratory ?? null,
                'onsite_imaging' => $this->onsite_imaging ?? null,
                'onsite_pharmacy' => $this->onsite_pharmarcy ?? null,
                'ambulance_services' => $this->ambulance_services ?? null,
            ],

            'staffing' => [
                'doctors' => $this->doctors ? (int) $this->doctors : null,
                'pharmacists' => $this->pharmacists ? (int) $this->pharmacists : null,
                'pharmacy_technicians' => $this->pharmacy_technicians ? (int) $this->pharmacy_technicians : null,
                'dentist' => $this->dentist ? (int) $this->dentist : null,
                'dental_technicians' => $this->dental_technicians ? (int) $this->dental_technicians : null,
                'nurses' => $this->nurses ? (int) $this->nurses : null,
                'midwifes' => $this->midwifes ? (int) $this->midwifes : null,
                'nurse_midwife' => $this->nurse_midwife ? (int) $this->nurse_midwife : null,
                'lab_technicians' => $this->lab_technicians ? (int) $this->lab_technicians : null,
                'lab_scientists' => $this->lab_scientists ? (int) $this->lab_scientists : null,
                'him_officers' => $this->him_officers ? (int) $this->him_officers : null,
                'community_health_officer' => $this->community_health_officer ? (int) $this->community_health_officer : null,
                'community_extension_workers' => $this->community_extension_workers ? (int) $this->community_extension_workers : null,
                'jun_community_extension_worker' => $this->jun_community_extension_worker ? (int) $this->jun_community_extension_worker : null,
                'env_health_officers' => $this->env_health_officers ? (int) $this->env_health_officers : null,
                'attendants' => $this->attendants ? (int) $this->attendants : null,
            ],

            'updated_at' => $this->updated_at ?? null,
        ];
    }
}
