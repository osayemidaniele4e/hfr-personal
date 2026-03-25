<?php

use Illuminate\Support\Facades\DB;

function getStates(){
    return DB::table('ou_states')
    ->select('id','name')
    ->orderByRaw('name ASC')
    ->get();
}

function getAssignedState(){
    return DB::table('ou_states')
    ->select('id','name')
    ->where('id', 'like','%'. Auth::user()->state_id. '%')
    ->orderByRaw('name ASC')
    ->get();
}


function getOwnership(){
    return DB::table('lst_ownerships')
            ->select('id','name')
            ->get();
}

function getFacilityTypes(){
    return DB::table('lst_facility_types')
    ->select('id','name')
    ->get();
}

function getLevelOfCare(){
    return DB::table('lst_level_of_care')
    ->select('id','name')
    ->get();
}

function getOperationalStatus(){
    return DB::table('lst_oparational_status')
    ->select('id','status')
    ->where('category','1')
    ->get();
}

function getLabOperationalStatus(){
    return DB::table('lst_oparational_status')
    ->select('id','status')
    ->where('category','2')
    ->get();
}

function getRegistrationStatus(){
    return DB::table('lst_registration_status')
    ->select('id','status')
    ->where('category','1')
    ->get();
}

function getLabRegistrationStatus(){
    return DB::table('lst_registration_status')
    ->select('id','status')
    ->where('category','2')
    ->get();
}


function getLicenseStatus(){
    return DB::table('lst_license_status')
    ->select('id','status')
    ->get();
}

function getAccreditationStatus(){
    return DB::table('lst_accreditation_status')
    ->select('id','status')
    ->get();
}

function getPremisesType(){
    return DB::table('lst_premises_type')
    ->select('id','name')
    ->get();
}

function getServiceCategory(){
    return DB::table('lst_hosp_service_category')
    ->get();
}

function getOutletCategory(){
    return DB::table('lst_outlet_category')
    ->select('id','name')
    ->get();
}

function getRoles(){
    $role = DB::table('roles')
    ->select('roles_below')
    ->where('name',implode(", ", Auth::user()->getRoleNames()->toArray()))
    ->get();
    
    $roles_below = explode(',', $role[0]->roles_below);
    
    return DB::table('roles')
    ->select('id','name')
    ->whereIn('id',$roles_below)
    ->orderBy('name')
    ->get();
}

function subRoles(){
    $role = DB::table('roles')
    ->select('roles_below')
    ->where('name',implode(", ", Auth::user()->getRoleNames()->toArray()))
    ->get();
    
    return explode(',', $role[0]->roles_below);
}

function getRolesAll(){
    return DB::table('roles')
    ->select('id','name')
    // ->where('id','>','1')
    ->orderBy('name')
    ->get();
}