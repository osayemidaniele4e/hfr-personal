<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class LookupResource extends JsonResource
{
    /**
     * Generic lookup resource for reference data (states, LGAs, wards, statuses, etc.)
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
        ];

        // Dynamically include common fields
        if (isset($this->name)) {
            $data['name'] = $this->name;
        }
        if (isset($this->status)) {
            $data['name'] = $this->status;
        }
        if (isset($this->type)) {
            $data['name'] = $this->type;
        }
        if (isset($this->code)) {
            $data['code'] = $this->code;
        }
        if (isset($this->state_code)) {
            $data['code'] = $this->state_code;
        }
        if (isset($this->lga_code)) {
            $data['code'] = $this->lga_code;
        }

        // Parent references
        if (isset($this->state_id)) {
            $data['state_id'] = $this->state_id;
        }
        if (isset($this->lga_id)) {
            $data['lga_id'] = $this->lga_id;
        }
        if (isset($this->category_id)) {
            $data['category_id'] = $this->category_id;
        }
        if (isset($this->ownership_id)) {
            $data['ownership_id'] = $this->ownership_id;
        }

        return $data;
    }
}
