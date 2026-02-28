<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class LaboratoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'facility_name' => $this->facility_name,
            'registration_no' => $this->registration_no ?? null,

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
                'latitude' => $this->latitude ? (float) $this->latitude : null,
                'longitude' => $this->longitude ? (float) $this->longitude : null,
            ],

            'classification' => [
                'facility_level' => [
                    'id' => $this->facility_level_id ?? null,
                    'name' => $this->facility_level_name ?? null,
                ],
                'ownership' => [
                    'id' => $this->ownership_id ?? null,
                    'name' => $this->ownership_name ?? null,
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
                'accreditation' => [
                    'id' => $this->accreditation_status_id ?? null,
                    'name' => $this->accreditation_status_name ?? null,
                ],
            ],

            'contact' => [
                'phone_number' => $this->phone_number ?? null,
                'email' => $this->email ?? null,
            ],

            'updated_at' => $this->updated_at ?? null,
        ];
    }
}
