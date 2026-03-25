<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;


/**
 * @group Statistics - Charts & Visualizations
 *
 * APIs for accessing chart-ready data and visualizations of healthcare facility statistics.
 * Provides data formatted for chart libraries (pie charts, bar charts, etc.) including 
 * facility distributions by ownership, level, and geographic location. Also includes 
 * population-to-facility ratio analysis. Data is cached for performance. No authentication required.
 */
class SummaryChartsController extends Controller
{


     /**
     * Display National Charts Dashboard
     *
     * Returns chart-ready data for visualizing national hospital statistics including:
     * - Distribution by facility level (pie/bar chart data)
     * - Distribution by ownership type (pie/bar chart data)
     * - Breakdown by state (table/bar chart data)
     * 
     * Data is formatted with 'name' and 'y' fields for direct use in charting libraries like Highcharts.
     * Results are cached for 30-60 minutes for optimal performance.
     * This endpoint is publicly accessible without authentication.
     *
     * @response 200 scenario="Success" {
     *   "view": "public.statistic_charts",
     *   "facilities_level_state": [
     *     {"name": 1, "y": 150},
     *     {"name": 2, "y": 200},
     *     {"name": 3, "y": 100}
     *   ],
     *   "facilities_ownership_state": [
     *     {"name": 1, "y": 250},
     *     {"name": 2, "y": 180},
     *     {"name": 3, "y": 20}
     *   ],
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
     *   "lst_states": [
     *     {"id": 1, "name": "Abia"},
     *     {"id": 2, "name": "Adamawa"}
     *   ],
     *   "lst_facility_types": [
     *     {"id": 1, "name": "Hospital"},
     *     {"id": 2, "name": "Pharmacy"},
     *     {"id": 3, "name": "Laboratory"},
     *     {"id": 4, "name": "Imaging Center"}
     *   ],
     *   "facility_type_id": 1,
     *   "state_id": 0,
     *   "filtered": false
     * }
     *
     * @apiResourceAdditional facilities_level_state Chart data where 'name' is facility_level_id and 'y' is count
     * @apiResourceAdditional facilities_ownership_state Chart data where 'name' is ownership_id and 'y' is count
     * @apiResourceAdditional levels_by_state Tabular breakdown by state and facility level
     */
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


        /**
     * Get Filtered Chart Data
     *
     * Returns chart-ready data filtered by facility type and state.
     * Response structure varies by facility type:
     * - Hospitals & Laboratories: Include level and ownership charts (view: statistic_charts)
     * - Pharmacies & Imaging Centers: Include ownership chart only (view: statistic_charts2)
     * 
     * When state_id = 0, shows national aggregation.
     * When specific state selected, shows state-specific data with LGA breakdown.
     * This endpoint is publicly accessible without authentication.
     *
     * @bodyParam state_id integer required State ID. Use 0 for national/all states. Example: 1
     * @bodyParam facility_type_id integer required Facility type: 1 (Hospital), 2 (Pharmacy), 3 (Laboratory), 4 (Imaging Center). Example: 1
     *
     * @response 200 scenario="Hospitals - National View" {
     *   "view": "public.statistic_charts",
     *   "facilities_level_state": [
     *     {"name": 1, "y": 150},
     *     {"name": 2, "y": 200},
     *     {"name": 3, "y": 100}
     *   ],
     *   "facilities_ownership_state": [
     *     {"name": 1, "y": 250},
     *     {"name": 2, "y": 180},
     *     {"name": 3, "y": 20}
     *   ],
     *   "levels_by_state": [
     *     {
     *       "state": "FCT",
     *       "primary": 25,
     *       "secondary": 35,
     *       "tertiary": 15,
     *       "total": 75
     *     }
     *   ],
     *   "facility_type_id": 1,
     *   "state_id": 0,
     *   "filtered": true
     * }
     *
     * @response 200 scenario="Hospitals - Single State (LGA Breakdown)" {
     *   "view": "public.statistic_charts",
     *   "facilities_level_state": [
     *     {"name": 1, "y": 25},
     *     {"name": 2, "y": 35},
     *     {"name": 3, "y": 15}
     *   ],
     *   "facilities_ownership_state": [
     *     {"name": 1, "y": 50},
     *     {"name": 2, "y": 20},
     *     {"name": 3, "y": 5}
     *   ],
     *   "levels_by_state": [
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
     *   "facility_type_id": 1,
     *   "state_id": 1,
     *   "filtered": true
     * }
     *
     * @response 200 scenario="Pharmacies - National View" {
     *   "view": "public.statistic_charts2",
     *   "facilities_ownership_state": [
     *     {"name": 1, "y": 80},
     *     {"name": 2, "y": 200}
     *   ],
     *   "facility_type_id": 2,
     *   "state_id": 0,
     *   "filtered": true
     * }
     *
     * @response 200 scenario="Laboratories - National View" {
     *   "view": "public.statistic_charts",
     *   "facilities_level_state": [
     *     {"name": 1, "y": 100},
     *     {"name": 2, "y": 60},
     *     {"name": 3, "y": 20}
     *   ],
     *   "facilities_ownership_state": [
     *     {"name": 1, "y": 70},
     *     {"name": 2, "y": 110}
     *   ],
     *   "levels_by_state": [
     *     {
     *       "state": "FCT",
     *       "basic": 15,
     *       "intermediate": 8,
     *       "advanced": 2,
     *       "total": 25
     *     }
     *   ],
     *   "facility_type_id": 3,
     *   "state_id": 0,
     *   "filtered": true
     * }
     *
     * @response 200 scenario="Imaging Centers - Single State" {
     *   "view": "public.statistic_charts2",
     *   "facilities_ownership_state": [
     *     {"name": 1, "y": 5},
     *     {"name": 2, "y": 10}
     *   ],
     *   "facility_type_id": 4,
     *   "state_id": 1,
     *   "filtered": true
     * }
     *
     * @apiResourceAdditional facilities_level_state Only for hospitals (type 1) and laboratories (type 3). Chart data where 'name' is facility_level_id and 'y' is count
     * @apiResourceAdditional facilities_ownership_state For all facility types. Chart data where 'name' is ownership_id and 'y' is count
     * @apiResourceAdditional levels_by_state Only for hospitals and laboratories. Contains state name when state_id=0, or LGA name when specific state selected
     */
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



        /**
     * Display Population Index Dashboard
     *
     * Shows the population-to-facility ratio (people per facility) at national level,
     * aggregated by state. Lower numbers indicate better facility coverage.
     * Calculation: State Population ÷ Number of Hospitals in State = People per Facility
     * 
     * Data is formatted as separate arrays for states and their corresponding ratios,
     * ready for bar chart visualization. Cached for 60 minutes.
     * This endpoint is publicly accessible without authentication.
     *
     * @response 200 scenario="Success" {
     *   "view": "public.statistic_population_index",
     *   "pop_index_states": [
     *     "Abia",
     *     "Adamawa",
     *     "FCT",
     *     "Lagos"
     *   ],
     *   "pop_index_ppf": [
     *     12500,
     *     15300,
     *     8200,
     *     11000
     *   ],
     *   "lst_states": [
     *     {"id": 1, "name": "Abia"},
     *     {"id": 2, "name": "Adamawa"}
     *   ],
     *   "state_id": 0
     * }
     *
     * @apiResourceAdditional pop_index_states Array of state names, indexed to match pop_index_ppf
     * @apiResourceAdditional pop_index_ppf Array of people per facility (population ÷ facility count), indexed to match pop_index_states. Lower values indicate better coverage.
     */
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


    /**
     * Get Filtered Population Index
     *
     * Returns population-to-facility ratio filtered by state.
     * - When state_id = 0: Shows ratio by state (national view)
     * - When specific state selected: Shows ratio by LGA within that state
     * 
     * The ratio indicates healthcare facility coverage - lower numbers mean 
     * better facility coverage per capita. For example, 10,000 means one facility 
     * serves 10,000 people on average.
     * This endpoint is publicly accessible without authentication.
     *
     * @bodyParam state_id integer required State ID. Use 0 for national state-level aggregation. Example: 1
     *
     * @response 200 scenario="National View (By State)" {
     *   "view": "public.statistic_population_index",
     *   "pop_index_states": [
     *     "Abia",
     *     "Adamawa",
     *     "FCT",
     *     "Lagos"
     *   ],
     *   "pop_index_ppf": [
     *     12500,
     *     15300,
     *     8200,
     *     11000
     *   ],
     *   "state_id": 0
     * }
     *
     * @response 200 scenario="Single State (By LGA)" {
     *   "view": "public.statistic_population_index",
     *   "pop_index_states": [
     *     "Abuja Municipal",
     *     "Gwagwalada",
     *     "Kuje",
     *     "Bwari"
     *   ],
     *   "pop_index_ppf": [
     *     9500,
     *     12000,
     *     18000,
     *     14500
     *   ],
     *   "state_id": 1
     * }
     *
     * @apiResourceAdditional pop_index_states When state_id=0: state names. When state_id>0: LGA names within that state
     * @apiResourceAdditional pop_index_ppf Population per facility ratio. Calculated as: (Area Population ÷ Hospital Count). Lower values = better coverage
     */
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
