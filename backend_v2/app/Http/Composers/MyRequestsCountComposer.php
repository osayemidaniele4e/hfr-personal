<?php

namespace App\Http\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;


class MyRequestsCountComposer
{

    public function __construct() {}


    // public function compose(View $view)
    // {

    //     $user = Auth::user()->id;

    //     $pending = DB::select("SELECT * FROM hospital_details_history WHERE
    //             (created_by = ". Auth::user()->id ." OR requested_id = ". Auth::user()->id .")
    //             AND status_id NOT IN (0,6,13,17,20,5,7,12,14,19,21)");

    //     $rejected = DB::select("SELECT * FROM hospital_details_history WHERE
    //             (created_by = ". $user ." OR requested_id = ". $user .")
    //             AND status_id IN (3,5,7,10,12,14,17,19,21)");

    //     $approved = DB::select("SELECT * FROM hospital_details_history WHERE
    //             (created_by = ". Auth::user()->id ." OR requested_id = ". Auth::user()->id .")
    //             AND status_id IN (6,13,20)");

    //     $request_count[0] = count($pending);
    //     $request_count[1] = count($rejected);
    //     $request_count[2] = count($approved);
    //     $request_count[3] = count($rejected)+count($pending);

    //     $view->with('request_count',$request_count);
    // }


    public function compose(View $view)
    {
        $user = Auth::user()->id;

        $pending = DB::select("SELECT * FROM hospital_details_history WHERE
            created_by = $user
            AND status_id NOT IN (0,6,13,17,20,5,7,12,14,19,21)");

        $rejected = DB::select("SELECT * FROM hospital_details_history WHERE
            created_by = $user
            AND status_id IN (3,5,7,10,12,14,17,19,21)");

        $approved = DB::select("SELECT * FROM hospital_details_history WHERE
            created_by = $user
            AND status_id IN (6,13,20)");

        $request_count[0] = count($pending);
        $request_count[1] = count($rejected);
        $request_count[2] = count($approved);
        $request_count[3] = count($rejected) + count($pending);

        $view->with('request_count', $request_count);
    }
}
