<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeneralController extends Controller
{
    public function getLgaList(Request $request){
        $data = DB::table('ou_lgas')
        ->select('name','id')
        ->where('state_id', $request->id)
        ->orderByRaw('name')
        ->get();
        
        $output = '<option value="">--Select LGA--</option>';
        foreach($data as $row)
        {
            $output .= '<option value="'.$row->id.'">'.$row->name.'</option>';
        }
        return $output;
    }

    public function getWardList(Request $request){
        $data = DB::table('ou_wards')
        ->select('name','id')
        ->where('lga_id', $request->lgaId)
        ->orderByRaw('name')
        ->get();
    
        $output = '<option value="">--Select Ward--</option>';
        foreach($data as $row)
        {
            $output .= '<option value="'.$row->id.'">'.$row->name.'</option>';
        }
        return $output;
    }
   
    public function getOwnershipType(Request $request){
        $data = DB::table('lst_ownership_types')
                ->select('id','type')
                ->where('ownership_id',$request->ownership_id)
                ->orderByRaw('id')
                ->get();
    
        $output = '<option value="">--Select Ownership Type--</option>';
        foreach($data as $row)
        {
            $output .= '<option value="'.$row->id.'">'.$row->type.'</option>';
        }
        return $output;
    }

    public function getFacilityLevelOption(Request $request){
        $data = DB::table('lst_level_of_care_options')
                ->select('id','description')
                ->where('level_of_care_id', $request->id)
                ->orderByRaw('id')
                ->get();
            
        $output = '<option value="0">--Select Option--</option>';
        foreach($data as $row)
        {
            $output .= '<option value="'.$row->id.'">'.$row->description.'</option>';
        }
        return $output;
    }

    public function getSpecializedOptions(){
        $data = DB::table('lst_level_of_care_options_category')
                ->select('id','name')
                ->orderByRaw('id')
                ->get();
    
        $output = '<option value="0">--Select Option--</option>';
        foreach($data as $row)
        {
            $output .= '<option value="'.$row->id.'">'.$row->name.'</option>';
        }
        return $output;
    }

    public function getServices(Request $request){
        $data = DB::table('lst_hosp_services')
        ->select('id','name')
        ->where('service_category_id', $request->id)
        ->orderByRaw('id')
        ->get();
    
        $output = '<option value="0">--Select Services--</option>';
        foreach($data as $row)
        {
            $output .= '<option value="'.$row->id.'">'.$row->name.'</option>';
        }
        return $output;
    }
    
    
}
