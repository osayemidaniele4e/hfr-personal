<?php

namespace App\Http\Resources;

use App\Http\Resources\User as UserResouce;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "code"=>200,
            "status"=>"success",
            "message"=>"transaction was successful",
            "DeveloperMessage"=>"the list of users was ftche3d sucessfully",
            "data"=> UserResouce::collection($this->collection),
        ];
    }
}
