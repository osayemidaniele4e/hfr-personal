<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditTrail extends Controller
{
    public function index()
    {
        $audits = DB::table('audits')
            ->join('users', 'audits.user_id', '=', 'users.id')
            ->join('hs_hospitals_history', 'audits.auditable_id', '=', 'hs_hospitals_history.id')
            // ->join('ou_states', 'ou_lgas.state_id', '=', 'ou_states.id')
            ->select(
                'audits.*',
                'users.firstname as firstname',
                'users.lastname as lastname',
                'hs_hospitals_history.facility_name as facility_name',
            )
            ->orderByDesc('created_at')
            ->get();

        return view('masters.audit.index', compact('audits'));
    }
}
