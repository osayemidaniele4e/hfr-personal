<?php

use App\Models\Lga;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

if (!function_exists('getStates')) {
    function getStates()
    {
        return DB::table('ou_states')
            ->select('id', 'name')
            ->orderByRaw('name ASC')
            ->get();
    }
}
if (!function_exists('getLga')) {
    function getLga()
    {
        return DB::table('ou_lgas')
            ->select('id', 'name')
            ->orderByRaw('name ASC')
            ->get();
    }
}

if (!function_exists('getAssignedState')) {
    function getAssignedState()
    {
        return DB::table('ou_states')
            ->select('id', 'name')
            ->where('id', 'like', '%' . Auth::user()->state_id . '%')
            ->orderByRaw('name ASC')
            ->get();
    }
}

if (!function_exists('getOwnership')) {

    function getOwnership()
    {
        return DB::table('lst_ownerships')
            ->select('id', 'name')
            ->get();
    }
}

if (!function_exists('getFacilityTypes')) {

    function getFacilityTypes()
    {
        return DB::table('lst_facility_types')
            ->select('id', 'name')
            ->get();
    }
}

if (!function_exists('getLevelOfCare')) {
    function getLevelOfCare()
    {
        return DB::table('lst_level_of_care')
            ->select('id', 'name')
            ->get();
    }
}

if (!function_exists('getAssignedLga')) {

    function getAssignedLga()
    {
        $user = Auth::user();

        // Example if you store LGA IDs as permissions
        $lgaIds = $user->permissions
            ->where('name', 'like', 'lga-%') // assuming permission names like "lga-1506"
            ->map(function ($perm) {
                return (int) str_replace('lga-', '', $perm->name);
            })
            ->toArray();

        return Lga::whereIn('id', $lgaIds)->get();
    }
}

if (!function_exists('getOperationalStatus')) {

    function getOperationalStatus()
    {
        return DB::table('lst_oparational_status')
            ->select('id', 'status')
            ->where('category', '1')
            ->get();
    }
}

if (!function_exists('getLabOperationalStatus')) {

    function getLabOperationalStatus()
    {
        return DB::table('lst_oparational_status')
            ->select('id', 'status')
            ->where('category', '2')
            ->get();
    }
}

if (!function_exists('getRegistrationStatus')) {
    function getRegistrationStatus()
    {
        return DB::table('lst_registration_status')
            ->select('id', 'status')
            ->where('category', '1')
            ->get();
    }
}



if (!function_exists('getLabRegistrationStatus')) {
    function getLabRegistrationStatus()
    {
        return DB::table('lst_registration_status')
            ->select('id', 'status')
            ->where('category', '2')
            ->get();
    }
}


if (!function_exists('getLicenseStatus')) {
    function getLicenseStatus()
    {
        return DB::table('lst_license_status')
            ->select('id', 'status')
            ->get();
    }
}

if (!function_exists('getAccreditationStatus')) {
    function getAccreditationStatus()
    {
        return DB::table('lst_accreditation_status')
            ->select('id', 'status')
            ->get();
    }
}

if (!function_exists('getPremisesType')) {
    function getPremisesType()
    {
        return DB::table('lst_premises_type')
            ->select('id', 'name')
            ->get();
    }
}


if (!function_exists('getServiceCategory')) {
    function getServiceCategory()
    {
        return DB::table('lst_hosp_service_category')
            ->get();
    }
}

if (!function_exists('getOutletCategory')) {
    function getOutletCategory()
    {
        return DB::table('lst_outlet_category')
            ->select('id', 'name')
            ->get();
    }
}

if (!function_exists('getRolesold')) {
    function getRolesold()
    {
        $role = DB::table('roles')
            ->select('roles_below')
            ->where('name', implode(", ", Auth::user()->getRoleNames()->toArray()))
            ->get();

        $roles_below = explode(',', $role[0]->roles_below);

        return DB::table('roles')
            ->select('id', 'name')
            ->whereIn('id', $roles_below)
            ->orderBy('name')
            ->get();
    }
}


if (!function_exists('getRoles')) {
    function getRoles()
    {
        $userRoleName = Auth::user()->getRoleNames()->first(); // Use first() instead of implode

        $role = DB::table('roles')
            ->select('roles_below')
            ->where('name', $userRoleName)
            ->first(); // Use first() for single result

        if (!$role || empty($role->roles_below)) {
            return collect(); // return empty collection
        }

        $roles_below = explode(',', $role->roles_below);

        return DB::table('roles')
            ->select('id', 'name')
            ->whereIn('id', $roles_below)
            ->orderBy('name')
            ->get();
    }
}


if (!function_exists('subRolesold')) {
    function subRolesold()
    {
        $role = DB::table('roles')
            ->select('roles_below')
            ->where('name', implode(", ", Auth::user()->getRoleNames()->toArray()))
            ->get();

        return explode(',', $role[0]->roles_below);
    }
}

if (!function_exists('subRoles')) {
    function subRoles()
    {
        $userRoleName = Auth::user()->getRoleNames()->first();

        $role = DB::table('roles')
            ->select('roles_below')
            ->where('name', $userRoleName)
            ->first();

        return $role && $role->roles_below
            ? explode(',', $role->roles_below)
            : [];
    }
}

if (!function_exists('getRolesAll')) {
    function getRolesAll()
    {
        return DB::table('roles')
            ->select('id', 'name')
            // ->where('id','>','1')
            ->orderBy('name')
            ->get();
    }
}


if (!function_exists('isValidationAccepted')) {
    function isValidationAccepted($statusId)
    {
        return in_array($statusId, [4, 11, 18, 7, 14, 21]);
    }
}



