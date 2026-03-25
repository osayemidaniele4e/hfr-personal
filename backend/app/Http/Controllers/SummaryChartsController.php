<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SummaryChartsController extends Controller
{

    public function index()
    {

        //facilities by levels
        $facilities_level_state = Cache::remember('facilities_level_state', 30, function () {
            return DB::select("SELECT facility_level_id as name,COUNT(id) AS y FROM hospital_details GROUP BY facility_level_id order by facility_level_id");
        });


        //by ownership
        $facilities_ownership_state = Cache::remember('facilities_ownership_state', 30, function () {
            return DB::select("SELECT ownership_id as name,COUNT(id) AS y FROM hospital_details GROUP BY ownership_id order by ownership_id");
        });

        //get health facilities by level of care by state
        $levels_by_state = Cache::remember('levels_by_state', 60, function () {
            return DB::table('hospitals_count_by_level_state_column')
                ->orderByRaw('state')
                ->get();
        });

        //get state list
        $lst_states = Cache::remember('lst_states', 60, function () {
            return DB::table('ou_states')
                ->select('id', 'name')
                ->orderByRaw('name ASC')
                ->get();
        });
        //get facility types
        $lst_facility_types = Cache::remember('lst_facility_types', 60, function () {
            return DB::table('lst_facility_types')
                ->select('id', 'name')
                ->get();
        });


        $filtered = FALSE;
        $facility_type_id = 1;
        $state_id = 0;

        return view('public.statistic_charts', compact(
            'facilities_ownership_state',
            'facilities_level_state',
            'levels_by_state',
            'lst_facility_types',
            'lst_states',
            'facility_type_id',
            'state_id',
            'filtered'
        ));
    }

    public function filter(Request $request)
    {
        $state_id = $request->state_id;
        $facility_type_id = $request->facility_type_id;
        $filtered = TRUE;

        //get state list
        $lst_states = Cache::remember('lst_states', 60, function () {
            return DB::table('ou_states')
                ->select('id', 'name')
                ->orderByRaw('name ASC')
                ->get();
        });
        //get facility types
        $lst_facility_types = Cache::remember('lst_facility_types', 60, function () {
            return DB::table('lst_facility_types')
                ->select('id', 'name')
                ->get();
        });

        //hospitals
        if ($facility_type_id == 1) {

            if ($state_id == 0) {
                $facilities_level_state = Cache::remember('facilities_level_state', 30, function () {
                    return DB::select("SELECT facility_level_id as name,COUNT(id) AS y FROM hospital_details 
                                GROUP BY facility_level_id order by facility_level_id");
                });

                //by ownership
                $facilities_ownership_state = Cache::remember('facilities_ownership_state', 30, function () {
                    return DB::select("SELECT ownership_id as name,COUNT(id) AS y FROM hospital_details 
                                GROUP BY ownership_id order by ownership_id");
                });

                //get health facilities by level of care by state
                $levels_by_state = DB::table('hospitals_count_by_level_state_column')
                    ->orderByRaw('state')
                    ->get();
            } else {
                $facilities_level_state = DB::select("SELECT facility_level_id as name,COUNT(id) AS y FROM hospital_details 
                                WHERE state_id=" . $state_id . " GROUP BY facility_level_id order by facility_level_id");


                //by ownership
                $facilities_ownership_state =  DB::select("SELECT ownership_id as name,COUNT(id) AS y FROM hospital_details 
                                WHERE state_id=" . $state_id . "  GROUP BY ownership_id order by ownership_id");


                //get health facilities by level of care by state
                $levels_by_state = DB::table('hospitals_count_by_level_lga_column')
                    ->where('state_id', $state_id)
                    ->orderByRaw('lga')
                    ->get();
            }

            return view('public.statistic_charts', compact(
                'facilities_level_state',
                'facilities_ownership_state',
                'levels_by_state',
                'lst_facility_types',
                'lst_states',
                'facility_type_id',
                'state_id',
                'filtered'
            ));
        }
        //pharmacies
        if ($facility_type_id == 2) {
            if ($state_id == 0) {
                //by ownership
                $facilities_ownership_state = DB::select("SELECT ownership_id name,COUNT(id) AS y FROM pharmacy_details 
                                GROUP BY ownership_id order by ownership_id");
            } else {

                $facilities_ownership_state =  DB::select("SELECT ownership_id as name,COUNT(id) AS y FROM pharmacy_details 
                                WHERE state_id=" . $state_id . "  GROUP BY ownership_id order by ownership_id");
            }

            return view('public.statistic_charts2', compact(
                'facilities_ownership_state',
                'lst_facility_types',
                'lst_states',
                'facility_type_id',
                'state_id',
                'filtered'
            ));
        }
        //labs
        if ($facility_type_id == 3) {
            //facilities by levels
            if ($state_id == 0) {
                $facilities_level_state = Cache::remember('lab_level_state', 30, function () {
                    return DB::select("SELECT facility_level_id as name,COUNT(id) AS y FROM laboratory_details 
                                    GROUP BY facility_level_id order by facility_level_id");
                });

                //by ownership
                $facilities_ownership_state = Cache::remember('lab_ownership_state', 30, function () {
                    return DB::select("SELECT ownership_id name,COUNT(id) AS y FROM laboratory_details 
                                    GROUP BY ownership_id order by ownership_id");
                });

                //get lab by level of care by state
                $levels_by_state = DB::table('laboratory_count_by_level_state_column')
                    ->orderByRaw('state')
                    ->get();
            } else {
                $facilities_level_state = DB::select("SELECT facility_level_id as name,COUNT(id) AS y FROM laboratory_details 
                                    WHERE state_id=" . $state_id . " GROUP BY facility_level_id order by facility_level_id");

                //by ownership
                $facilities_ownership_state =  DB::select("SELECT ownership_id as name,COUNT(id) AS y FROM laboratory_details 
                                    WHERE state_id=" . $state_id . "  GROUP BY ownership_id order by ownership_id");

                //get health facilities by level of care by state
                $levels_by_state = DB::table('laboratory_count_by_level_lga_column')
                    ->where('state_id', $state_id)
                    ->orderByRaw('lga')
                    ->get();
            }

            return view('public.statistic_charts', compact(
                'facilities_level_state',
                'facilities_ownership_state',
                'levels_by_state',
                'lst_facility_types',
                'lst_states',
                'facility_type_id',
                'state_id',
                'filtered'
            ));
        }

        //radiologies
        if ($facility_type_id == 4) {
            if ($state_id == 0) {

                $facilities_ownership_state = DB::select("SELECT ownership_id name,COUNT(id) AS y FROM imaging_details 
                                GROUP BY ownership_id order by ownership_id");
            } else {

                $facilities_ownership_state =  DB::select("SELECT ownership_id as name,COUNT(id) AS y FROM imaging_details 
                                WHERE state_id=" . $state_id . "  GROUP BY ownership_id order by ownership_id");
            }

            return view('public.statistic_charts2', compact(
                'facilities_ownership_state',
                'lst_facility_types',
                'lst_states',
                'facility_type_id',
                'state_id',
                'filtered'
            ));
        }
    }


    public function population_index()
    {
        //get state list
        $lst_states = Cache::remember('lst_states', 60, function () {
            return DB::table('ou_states')
                ->select('id', 'name')
                ->orderByRaw('name ASC')
                ->get();
        });
        $state_id = 0;

        $population_index = Cache::remember('population_index', 60, function () {
            return DB::select("SELECT state, ROUND(p.population/COUNT(h.id)) AS ppf FROM hospital_details h
                    JOIN population_by_state p ON p.state_id=h.state_id
                    GROUP BY state,p.population
                    ORDER BY state");
        });

        $pop_index_states = array();
        $pop_index_ppf = array();

        foreach ($population_index as $indx) {
            $pop_index_states[] = $indx->state;
            $pop_index_ppf[] = (int)$indx->ppf;
        };
        // dd($pop_index_ppf);

        return view('public.statistic_population_index', compact('pop_index_states', 'pop_index_ppf', 'lst_states', 'state_id'));
    }

    public function population_index_filter(Request $request)
    {
        $state_id = $request->state_id;

        //get state list
        $lst_states = Cache::remember('lst_states', 60, function () {
            return DB::table('ou_states')
                ->select('id', 'name')
                ->orderByRaw('name ASC')
                ->get();
        });

        if ($state_id == 0) {
            $population_index =  DB::select("SELECT state AS name, ROUND(p.population/COUNT(h.id)) AS ppf FROM hospital_details h
                         JOIN population_by_state p ON p.state_id=h.state_id
                         GROUP BY state,p.population
                         ORDER BY state");
        } else {
            $population_index =  DB::select("SELECT lga AS name, ROUND(p.population/COUNT(h.id)) AS ppf FROM hospital_details h
                        JOIN population p ON p.lga_id=h.lga_id
                        WHERE h.state_id = " . $state_id . "
                        GROUP BY lga,population
                        ORDER BY lga");
        }


        $pop_index_states = array();
        $pop_index_ppf = array();

        foreach ($population_index as $indx) {
            $pop_index_states[] = $indx->name;
            $pop_index_ppf[] = (int)$indx->ppf;
        };
        // dd($pop_index_ppf);

        return view('public.statistic_population_index', compact('pop_index_states', 'pop_index_ppf', 'lst_states', 'state_id'));
    }
}
