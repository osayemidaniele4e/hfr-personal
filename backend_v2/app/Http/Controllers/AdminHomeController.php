<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Analytics;
use Illuminate\Support\Facades\Auth;
use Spatie\Analytics\Period;


/**
  * Admin Home Controller
  * @group Administration
  *
  * Handles the dashboard views and related data for federal (national) and state-level users.
  *
  * @authenticated
  */
class AdminHomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function indexOLD()
    {
        //national level dashabaord
        if (Auth::user()->state_id == 1) {
            $analyticsData = Analytics::fetchTotalVisitorsAndPageViews(Period::days(30));

            $dates = array();
            $visitors = array();
            foreach ($analyticsData as $a) {
                $dates[] = $a['date']->toDateString();
                $visitors[] = $a['visitors'];
            };

            $num_downloads = DB::select("SELECT date_format(created_at,'%b %y') as name,month(created_at) mon,year(created_at) year, COUNT(id) y
                    FROM downloads group by name,mon,year order by year,mon asc  limit 24");


            $facility_status = DB::table('facility_status_state_pivot')->get();

            $completenes = DB::table('signature_domain_completenes')
                ->select(DB::raw('state as name, CAST(avg(score) as unsigned) as y'))
                ->groupBy('state')
                ->orderBy('y', 'Desc')
                ->get();

            return view("dashboard.federal", compact('num_downloads', 'facility_status', 'dates', 'visitors', 'completenes'));
        } else { //state level dashboard

            $facility_status = DB::table('facility_status_lga_pivot')
                ->where('state_id', 'like', '%' .  Auth::user()->state_id . '%')
                ->orderBy('lga')
                ->get();

            $completenes_all = DB::table('signature_domain_completenes')
                ->select(DB::raw('state as name, CAST(avg(score) as unsigned) as y'))
                ->groupBy('state')
                ->orderBy('y', 'Desc')
                ->get();

            $completenes = DB::table('signature_domain_completenes')
                ->select(DB::raw('lga as name, CAST(score as unsigned) as y'))
                ->where('state_id', Auth::user()->state_id)
                ->orderBy('y', 'Desc')
                ->get();

            return view("dashboard.state", compact('facility_status', 'completenes', 'completenes_all'));
        }
    }

    public function index()
    {
        // dd(auth()->user()->getAllPermissions());
        // dd(Auth::user()->state_id);

        //national level dashboard
        if (Auth::user()->state_id == 1) {
            // Remove or comment out the Google Analytics logic
            // $analyticsData = Analytics::fetchTotalVisitorsAndPageViews(Period::days(30));

            // Alternatively, just make sure no Google Analytics data is passed
            $dates = [];
            $visitors = [];

            $num_downloads = DB::select("SELECT date_format(created_at,'%b %y') as name,month(created_at) mon,year(created_at) year, COUNT(id) y
                    FROM downloads group by name,mon,year order by year,mon asc limit 24");


            $facility_status = DB::table('facility_status_state_pivot')->get();
            // dd($facility_status);

            $completenes = DB::table('signature_domain_completenes')
                ->select(DB::raw('state as name, CAST(avg(score) as unsigned) as y'))
                ->groupBy('state')
                ->orderBy('y', 'Desc')
                ->get();

            return view("dashboard.federal", compact('num_downloads', 'facility_status', 'dates', 'visitors', 'completenes'));
        } else { //state level dashboard
            // state-level logic remains unchanged...
            $facility_status = DB::table('facility_status_lga_pivot')
                ->where('state_id', 'like', '%' .  Auth::user()->state_id . '%')
                ->orderBy('lga')
                ->get();

            $completenes_all = DB::table('signature_domain_completenes')
                ->select(DB::raw('state as name, CAST(avg(score) as unsigned) as y'))
                ->groupBy('state')
                ->orderBy('y', 'Desc')
                ->get();

            $completenes = DB::table('signature_domain_completenes')
                ->select(DB::raw('lga as name, CAST(score as unsigned) as y'))
                ->where('state_id', Auth::user()->state_id)
                ->orderBy('y', 'Desc')
                ->get();

            return view("dashboard.state", compact('facility_status', 'completenes', 'completenes_all'));
        }
    }



 /**
     * LGA Completeness Scores
     *
     * Returns completeness scores for all LGAs in a given state.
     *
     * @authenticated
     * @bodyParam state string required Name of the state. Example: Lagos
     * @response 200 [
     *   {"name": "Ikeja", "y": 85},
     *   {"name": "Surulere", "y": 78}
     * ]
     */
    public function completeness(Request $request)
    {
        $completenes = DB::table('signature_domain_completenes')
            ->select(DB::raw('lga as name, CAST(score as unsigned) as y'))
            ->where('state', $request->state)
            ->orderBy('y', 'desc')
            ->get();

        return $completenes;
    }


 /**
     * LGA Facility Status
     *
     * Returns facility status for all LGAs in a given state.
     *
     * @authenticated
     * @bodyParam state string required Name of the state. Example: Lagos
     * @response 200 [
     *   {"lga": "Ikeja", "status": "Active"},
     *   {"lga": "Surulere", "status": "Inactive"}
     * ]
     */
    public function facilityStatus(Request $request)
    {
        $facility_status = DB::table('facility_status_lga_pivot')
            ->where('state', $request->state)
            ->orderBy('lga')
            ->get();

        return $facility_status;
    }
}
