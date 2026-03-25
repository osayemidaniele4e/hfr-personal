<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;


/**
 * @group Statistics
 *
 * APIs for accessing aggregated statistics and analytics about healthcare facilities.
 * Provides summary counts, breakdowns by ownership, facility level, and geographic distribution.
 * Data is cached for 30 minutes to improve performance. No authentication required.
 */
class SummaryTablesController extends Controller
{


      /**
     * Display National Statistics Dashboard
     *
     * Shows comprehensive national-level statistics for all facility types (hospitals, laboratories, 
     * pharmacies, imaging centers). Includes breakdowns by ownership, facility level, and state.
     * Data is cached for 30 minutes for optimal performance.
     * This endpoint is publicly accessible without authentication.
     *
     * @response 200 scenario="Success" {
     *   "view": "public.statistics",
     *   "total_num_fac": [450, 180, 280, 120],
     *   "levels_by_state": [
     *     {
     *       "state": "FCT",
     *       "primary": 25,
     *       "secondary": 35,
     *       "tertiary": 15,
     *       "total": 75
     *     },
     *     {
     *       "state": "Lagos",
     *       "primary": 45,
     *       "secondary": 55,
     *       "tertiary": 25,
     *       "total": 125
     *     }
     *   ],
     *   "ownerships_by_state": [
     *     {
     *       "state": "FCT",
     *       "public": 50,
     *       "private": 20,
     *       "faith_based": 5,
     *       "total": 75
     *     }
     *   ],
     *   "levels_ownership_by_state": [
     *     {
     *       "state": "FCT",
     *       "public_primary": 15,
     *       "public_secondary": 25,
     *       "public_tertiary": 10,
     *       "private_primary": 10,
     *       "private_secondary": 10,
     *       "private_tertiary": 5
     *     }
     *   ],
     *   "lst_states": [
     *     {"id": 1, "name": "Abia"},
     *     {"id": 2, "name": "Adamawa"}
     *   ],
     *   "lst_facility_types": [
     *     {"id": 1, "name": "Hospital"},
     *     {"id": 2, "name": "Pharmacy"},
     *     {"id": 3, "name": "Laboratory"},
     *     {"id": 4, "name": "Imaging Center"}
     *   ]
     * }
     *
     * @apiResourceAdditional total_num_fac Array indexed as [0: hospitals, 1: laboratories, 2: pharmacies, 3: imaging centers]
     */
    public function index(){      
       
        $results = DB::select("SELECT  
            (SELECT count(id) FROM hospital_details) AS hosp,
            (SELECT count(id) FROM  laboratory_details) AS lab,
            (SELECT count(id) FROM pharmacy_details) AS pharma,
            (SELECT count(id) FROM imaging_details) AS radio
             FROM dual");

        $total_num_fac=array();

        foreach ($results as $r){
            $total_num_fac[0] = $r->hosp;
            $total_num_fac[1] = $r->lab;
            $total_num_fac[2] = $r->pharma;
            $total_num_fac[3] = $r->radio;
        };

       
        $levels_by_state = Cache::remember('levels_by_state', 30, function () {
            return DB::table('hospitals_count_by_level_state_column')->get();
        });

        $ownerships_by_state = Cache::remember('ownerships_by_state', 30, function () {
            return DB::table('hospitals_count_by_ownership_state_column')->get();
        });
    
        $levels_ownership_by_state = Cache::remember('levels_ownership_by_state', 30, function () {
            return DB::table('hospitals_count_by_ownership_level_state_column')->get();
        });
        
        $lst_states = Cache::remember('lst_states', 30, function () {
            return DB::table('ou_states')
                    ->select('id','name')
                    ->orderByRaw('name ASC')
                    ->get();
        });

        $lst_facility_types = Cache::remember('lst_facility_types', 30, function () {
            return DB::table('lst_facility_types')
                    ->select('id','name')
                    ->get();
        });

        return view('public.statistics', compact('total_num_fac','ownerships_by_state',
        'levels_by_state','levels_ownership_by_state','lst_facility_types','lst_states'));
    }



    /**
     * Get Filtered Statistics
     *
     * Retrieves facility statistics filtered by state and facility type.
     * Returns different data structures based on facility type:
     * - Hospitals & Laboratories: Include level and ownership breakdowns
     * - Pharmacies & Imaging Centers: Include ownership breakdowns only
     * 
     * When state_id = 0 (all states), data is aggregated by state.
     * When specific state is selected, data is broken down by LGA within that state.
     * This endpoint is publicly accessible without authentication.
     *
     * @bodyParam state_id integer required State ID. Use 0 for national/all states aggregation. Example: 1
     * @bodyParam facility_type_id integer required Facility type: 1 (Hospital), 2 (Pharmacy), 3 (Laboratory), 4 (Imaging Center). Example: 1
     *
     * @response 200 scenario="Hospitals - National View" {
     *   "view": "public.statistics_filtered",
     *   "total_num_fac": [450, 180, 280, 120],
     *   "levels_by_lga": [
     *     {
     *       "state": "FCT",
     *       "primary": 25,
     *       "secondary": 35,
     *       "tertiary": 15,
     *       "total": 75
     *     }
     *   ],
     *   "ownerships_by_lga": [
     *     {
     *       "state": "FCT",
     *       "public": 50,
     *       "private": 20,
     *       "faith_based": 5,
     *       "total": 75
     *     }
     *   ],
     *   "levels_ownership_by_lga": [
     *     {
     *       "state": "FCT",
     *       "public_primary": 15,
     *       "public_secondary": 25,
     *       "public_tertiary": 10,
     *       "private_primary": 10,
     *       "private_secondary": 10,
     *       "private_tertiary": 5
     *     }
     *   ],
     *   "facility_type_id": 1,
     *   "state_id": 0
     * }
     *
     * @response 200 scenario="Hospitals - Single State (LGA Breakdown)" {
     *   "view": "public.statistics_filtered",
     *   "total_num_fac": [75, 25, 35, 15],
     *   "levels_by_lga": [
     *     {
     *       "lga": "Abuja Municipal",
     *       "primary": 10,
     *       "secondary": 15,
     *       "tertiary": 8,
     *       "total": 33
     *     },
     *     {
     *       "lga": "Gwagwalada",
     *       "primary": 15,
     *       "secondary": 20,
     *       "tertiary": 7,
     *       "total": 42
     *     }
     *   ],
     *   "ownerships_by_lga": [
     *     {
     *       "lga": "Abuja Municipal",
     *       "public": 20,
     *       "private": 10,
     *       "faith_based": 3,
     *       "total": 33
     *     }
     *   ],
     *   "levels_ownership_by_lga": [
     *     {
     *       "lga": "Abuja Municipal",
     *       "public_primary": 5,
     *       "public_secondary": 10,
     *       "public_tertiary": 5,
     *       "private_primary": 5,
     *       "private_secondary": 5,
     *       "private_tertiary": 3
     *     }
     *   ],
     *   "facility_type_id": 1,
     *   "state_id": 1
     * }
     *
     * @response 200 scenario="Pharmacies - National View" {
     *   "view": "public.statistics_filtered2",
     *   "total_num_fac": [450, 180, 280, 120],
     *   "ownerships_by_lga": [
     *     {
     *       "state": "FCT",
     *       "public": 15,
     *       "private": 60,
     *       "total": 75
     *     },
     *     {
     *       "state": "Lagos",
     *       "public": 25,
     *       "private": 120,
     *       "total": 145
     *     }
     *   ],
     *   "facility_type_id": 2,
     *   "state_id": 0
     * }
     *
     * @response 200 scenario="Pharmacies - Single State (LGA Breakdown)" {
     *   "view": "public.statistics_filtered2",
     *   "total_num_fac": [75, 25, 35, 15],
     *   "ownerships_by_lga": [
     *     {
     *       "lga": "Abuja Municipal",
     *       "public": 5,
     *       "private": 20,
     *       "total": 25
     *     },
     *     {
     *       "lga": "Gwagwalada",
     *       "public": 10,
     *       "private": 40,
     *       "total": 50
     *     }
     *   ],
     *   "facility_type_id": 2,
     *   "state_id": 1
     * }
     *
     * @response 200 scenario="Laboratories - National View" {
     *   "view": "public.statistics_filtered",
     *   "total_num_fac": [450, 180, 280, 120],
     *   "levels_by_lga": [
     *     {
     *       "state": "FCT",
     *       "basic": 15,
     *       "intermediate": 8,
     *       "advanced": 2,
     *       "total": 25
     *     }
     *   ],
     *   "ownerships_by_lga": [
     *     {
     *       "state": "FCT",
     *       "public": 10,
     *       "private": 15,
     *       "total": 25
     *     }
     *   ],
     *   "facility_type_id": 3,
     *   "state_id": 0
     * }
     *
     * @response 200 scenario="Imaging Centers - Single State" {
     *   "view": "public.statistics_filtered2",
     *   "total_num_fac": [75, 25, 35, 15],
     *   "ownerships_by_lga": [
     *     {
     *       "lga": "Abuja Municipal",
     *       "public": 3,
     *       "private": 10,
     *       "total": 13
     *     }
     *   ],
     *   "facility_type_id": 4,
     *   "state_id": 1
     * }
     *
     * @apiResourceAdditional total_num_fac Array indexed as [0: hospitals, 1: laboratories, 2: pharmacies, 3: imaging centers]. Counts are filtered by selected state_id.
     * @apiResourceAdditional levels_by_lga Only returned for hospitals (type 1) and laboratories (type 3). Variable name "lga" contains state name when state_id=0, or LGA name when specific state selected.
     * @apiResourceAdditional ownerships_by_lga Returned for all facility types. Variable name "lga" contains state name when state_id=0, or LGA name when specific state selected.
     * @apiResourceAdditional levels_ownership_by_lga Only returned for hospitals (type 1). Shows cross-tabulation of levels and ownership types.
     */
    public function filter(Request $request){
        $state_id = $request->state_id;
        $facility_type_id = $request->facility_type_id;

        if($state_id==0){
            $state_id2 = "";
        }else {
            $state_id2 = $state_id;
        }
        //get all num of facilities for all facility types
        $results = DB::select("SELECT  
            (SELECT count(id) FROM hospital_details WHERE state_id LIKE '%". $state_id2 ."%') AS hosp,
            (SELECT count(id) FROM  laboratory_details WHERE state_id LIKE '%". $state_id2 ."%') AS lab,
            (SELECT count(id) FROM pharmacy_details WHERE state_id LIKE '%". $state_id2 ."%') AS pharma,
            (SELECT count(id) FROM imaging_details WHERE state_id LIKE '%". $state_id2 ."%') AS radio
            FROM dual");

        $total_num_fac=array();

        foreach ($results as $r){
            $total_num_fac[0] = $r->hosp;
            $total_num_fac[1] = $r->lab;
            $total_num_fac[2] = $r->pharma;
            $total_num_fac[3] = $r->radio;
        };
        
        //get state list
        $lst_states = Cache::remember('lst_states', 30, function () {
            return DB::table('ou_states')
                    ->select('id','name')
                    ->orderByRaw('name ASC')
                    ->get();
        });
        //get facility types list
        $lst_facility_types = Cache::remember('lst_facility_types', 30, function () {
            return DB::table('lst_facility_types')
                    ->select('id','name')
                    ->get();
        });

        // if hospitals
        if ($facility_type_id==1){
            if($state_id==0){
                $levels_by_lga = DB::table('hospitals_count_by_level_state_column')
                            ->get();
                            
                $ownerships_by_lga= DB::table('hospitals_count_by_ownership_state_column')
                            ->get();

                $levels_ownership_by_lga = DB::table('hospitals_count_by_ownership_level_state_column')
                            ->get();
            }
            else{
                $levels_by_lga = DB::table('hospitals_count_by_level_lga_column')
                ->where('state_id',$state_id)
                ->orderByRaw('lga')
                ->get();
                            
                $ownerships_by_lga= DB::table('hospitals_count_by_ownership_lga_column')
                            ->where('state_id',$state_id)
                            ->orderByRaw('lga')
                            ->get();

                $levels_ownership_by_lga = DB::table('hospitals_count_by_ownership_level_lga_column')
                            ->where('state_id',$state_id)
                            ->orderByRaw('lga')
                            ->get();
              
            }
            return view('public.statistics_filtered', compact('total_num_fac','ownerships_by_lga',
            'levels_by_lga','levels_ownership_by_lga','lst_facility_types','lst_states','facility_type_id','state_id'));

        }

        //if pharmacies
        if ($facility_type_id==2){
           
            if($state_id==0){  //if all states
                $ownerships_by_lga= DB::table('pharmacy_count_by_ownership_state_column')
                            ->orderbyraw('state')
                            ->get();
            }
            else {
                //selected state
                $ownerships_by_lga= DB::table('pharmacy_count_by_ownership_lga_column')
                            ->where('state_id',$state_id)
                            ->orderByRaw('lga')
                            ->get();
            }

            return view('public.statistics_filtered2', compact('total_num_fac','ownerships_by_lga',
            'lst_facility_types','lst_states','facility_type_id','state_id'));
           
        }
        // fi laboratories
        if ($facility_type_id==3){

            if($state_id==0){  //if all states
                $levels_by_lga = DB::table('laboratory_count_by_level_state_column')
                            ->orderbyraw('state')
                            ->get();
                
                $ownerships_by_lga= DB::table('laboratory_count_by_ownership_state_column')
                            ->orderbyraw('state')
                            ->get();
            }
            else {
                //selected state
                $levels_by_lga = DB::table('laboratory_count_by_level_lga_column')
                            ->where('state_id',$state_id)
                            ->orderByRaw('lga')
                            ->get();
                            
                $ownerships_by_lga= DB::table('laboratory_count_by_ownership_lga_column')
                            ->where('state_id',$state_id)
                            ->orderByRaw('lga')
                            ->get();
            }

            return view('public.statistics_filtered', compact('total_num_fac','ownerships_by_lga',
            'levels_by_lga','lst_facility_types','lst_states','facility_type_id','state_id'));
           
        }
        if ($facility_type_id==4){
            if($state_id==0){  //if all states
                $ownerships_by_lga= DB::table('imaging_count_by_ownership_state_column')
                            ->orderbyraw('state')
                            ->get();
            }
            else {
                //selected state
                $ownerships_by_lga= DB::table('imaging_count_by_ownership_lga_column')
                            ->where('state_id',$state_id)
                            ->orderByRaw('lga')
                            ->get();
            }

            return view('public.statistics_filtered2', compact('total_num_fac','ownerships_by_lga',
            'lst_facility_types','lst_states','facility_type_id','state_id'));
           
        }

    }

}
