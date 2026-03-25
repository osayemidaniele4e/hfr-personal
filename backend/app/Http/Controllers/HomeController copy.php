<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;


class HomeController extends Controller
{
    public function about(){
        return view('public.about');
    }

 
    public function index()
    {
         //get number of facilities by state
        $total_facilities_state= Cache::remember('total_facilities_home', 30, function () {
            return DB::select("SELECT s.short_code statecode,count(h.id) as 'value' FROM hs_hospitals h 
            JOIN ou_states s ON s.id = h.state_id GROUP BY s.short_code");
        });      

            //facilities by levels
        $facilities_level_state = Cache::remember('facilities_level_home', 30, function () {
            return DB::select("SELECT facility_level as name,COUNT(id) AS y FROM hospital_details GROUP BY facility_level order by facility_level");
        });
 
        //by ownership
        $facilities_ownership_state = Cache::remember('facilities_ownership_home', 30, function () {
            return DB::select("SELECT ownership as name,COUNT(id) AS y FROM hospital_details GROUP BY ownership order by ownership");
        });

        //facilities with geo codes
        $geo_percent = Cache::remember('percent_facilities_geo', 30, function () {
            return DB::select("SELECT state as name, cast(SUM(case when latitude <> '' then 1 else 0 end)/count(id)*100 as unsigned) as y
                    FROM hospital_details group by state order by y desc");
         });
         
       
        return view('public.home',compact('total_facilities_state','facilities_ownership_state','facilities_level_state','geo_percent'));
    }

    public function getFacilitesByLGA(Request $request){
        $total_facilities_lga = DB::select("SELECT l.map_code LGA_UID,count(h.id) value 
                    FROM hs_hospitals h 
                    JOIN ou_lgas l ON l.id = h.lga_id 
                    JOIN ou_states s ON s.id=l.state_id
                    WHERE s.short_code ='".$request->state_code.
                    "'GROUP BY l.map_code");

   
        $state = DB::table('ou_states')
            ->select('name','id')
            ->where('short_code', $request->state_code)
            ->get();

        $state_id = $state[0]->id;

        //get by level of care
        $by_level = DB::select("SELECT facility_level as name,COUNT(id) AS y FROM hospital_details 
                WHERE state_id=". $state_id ." GROUP BY facility_level order by facility_level");

        //by ownership
        $by_ownership =  DB::select("SELECT ownership as name,COUNT(id) AS y FROM hospital_details 
                WHERE state_id=". $state_id ."  GROUP BY ownership order by ownership");

        //fac with Geo codes
        $geo_codes =  DB::select("SELECT lga as name, cast(SUM(case when latitude <> '' then 1 else 0 end)/count(id)*100 as unsigned) as y 
        FROM hospital_details WHERE state_id=". $state_id ."  GROUP BY lga order by y desc");


        $result  = array();
        $result['state'] =  $state[0]->name;
        $result['facilities'] =  $total_facilities_lga;
        $result['by_ownership'] =  $by_ownership;
        $result['by_level'] =  $by_level;
        $result['geo_codes'] =  $geo_codes;

        return $result;
    }

    public function getFacilitesGMap(Request $request)
    {   
        //get lga id and name
        $lga = DB::table('ou_lgas')
            ->select('id','name')
            ->where('map_code', $request->lga_code)
            ->get();
        
         $lga_details = array();
 
         foreach ($lga as $l){
             $lga_details[0] = $l->id; //lga id
             $lga_details[1] = $l->name; //lga name
         };

     
        $facilities = DB::select("SELECT * FROM hospital_details where latitude != '' and 
                        lga_id='". $lga_details[0] . "'");
       
        $lga_name =  $lga_details[1];

        $result  = array();
        $result['lga_name'] = $lga_name;
        $result['facilities_list'] =  $facilities;

        return  $result;
    }

    public function getFacilityDetails(Request $request){
        $hosp = DB::table('hospital_details')
        ->where('id',$request->id)
        ->get();

        return $hosp;
    }
    
}
