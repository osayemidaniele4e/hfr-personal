<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FacilityCollection extends ResourceCollection
{
    public $collects = FacilityResource::class;

    /**
     * Transform the resource collection into a standardized paginated response.
     */
    public function toArray($request)
    {
        return [
            'facilities' => $this->collection,
        ];
    }

    public function with($request)
    {
        return [
            'status' => 'success',
            'meta' => [
                'api_version' => 'v1',
            ],
        ];
    }
}
