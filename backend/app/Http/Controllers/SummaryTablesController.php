<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SummaryTablesController extends Controller
{
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
