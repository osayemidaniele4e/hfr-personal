<?php

namespace App\Http\Controllers;

use Validator;
use App\User;
use Illuminate\Http\Request;
use App\Http\Resources\User as UserResouce;
use App\Http\Resources\UserCollection;

class apiController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "name"=>"required",
            "hhh"=>"required",
        ]);

        if($validator->fails()){
            return $validator->errors();
        }
    }


    public function index(){ 

     $response =  new UserResouce(User::find(4));

     return response()->json([
                    "code"=>200,
                    "status"=>"success",
                    "message"=>"transaction was successful",
                    "DeveloperMessage"=>"the list of users was ftche3d sucessfully",
                    "data"=>$response
     ]);

        // return response()->json([
        //     "code"=>200,
        //     "status"=>"success",
        //     "message"=>"transaction was successful",
        //     "DeveloperMessage"=>"the list of users was ftche3d sucessfully",
        //     "data"=>["users"=>$users]
        // ],200); 
    }

    public function collection()
    {
        return new UserCollection(User::find([4,9,10]));
    }
}
