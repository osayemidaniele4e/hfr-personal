<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\LookupResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @group Lookups
 *
 * Reference data endpoints for states, LGAs, wards, facility types, ownership, and statuses.
 */
class LookupController extends BaseApiController
{
    /**
     * List States
     *
     * Retrieve all Nigerian states.
     */
    public function states()
    {
        $data = DB::table('ou_states')
            ->select('id', 'name', 'short_code as code')
            ->orderBy('name')
            ->get();

        return $this->success(['states' => LookupResource::collection($data)]);
    }

    /**
     * List LGAs
     *
     * Retrieve Local Government Areas, optionally filtered by state.
     *
     * @queryParam state_id integer Filter LGAs by state ID. Example: 25
     */
    public function lgas(Request $request)
    {
        $query = DB::table('ou_lgas')
            ->select('id', 'name', 'lga_code as code', 'state_id')
            ->orderBy('name');

        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }

        return $this->success(['lgas' => LookupResource::collection($query->get())]);
    }

    /**
     * List Wards
     *
     * Retrieve wards, optionally filtered by LGA.
     *
     * @queryParam lga_id integer Filter wards by LGA ID. Example: 512
     */
    public function wards(Request $request)
    {
        $query = DB::table('ou_wards')
            ->select('id', 'name', 'lga_id')
            ->orderBy('name');

        if ($request->filled('lga_id')) {
            $query->where('lga_id', $request->lga_id);
        }

        return $this->success(['wards' => LookupResource::collection($query->get())]);
    }

    /**
     * List Facility Types
     *
     * Retrieve all facility type classifications.
     */
    public function facilityTypes()
    {
        $data = DB::table('lst_facility_types')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return $this->success(['facility_types' => LookupResource::collection($data)]);
    }

    /**
     * List Facility Levels
     *
     * Retrieve all levels of care.
     */
    public function facilityLevels()
    {
        $data = DB::table('lst_level_of_care')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return $this->success(['facility_levels' => LookupResource::collection($data)]);
    }

    /**
     * List Ownership Categories
     *
     * Retrieve ownership categories (e.g., Public, Private).
     */
    public function ownership()
    {
        $data = DB::table('lst_ownerships')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return $this->success(['ownership_categories' => LookupResource::collection($data)]);
    }

    /**
     * List Ownership Types
     *
     * Retrieve ownership types, optionally filtered by ownership category.
     *
     * @queryParam ownership_id integer Filter by ownership category. Example: 1
     */
    public function ownershipTypes(Request $request)
    {
        $query = DB::table('lst_ownership_types')
            ->select('id', 'type as name', 'ownership_id')
            ->orderBy('type');

        if ($request->filled('ownership_id')) {
            $query->where('ownership_id', $request->ownership_id);
        }

        return $this->success(['ownership_types' => LookupResource::collection($query->get())]);
    }

    /**
     * List Operational Statuses
     *
     * Retrieve all operational status options.
     */
    public function operationalStatuses()
    {
        $data = DB::table('lst_oparational_status')
            ->select('id', 'status as name')
            ->orderBy('status')
            ->get();

        return $this->success(['operational_statuses' => LookupResource::collection($data)]);
    }

    /**
     * List Registration Statuses
     */
    public function registrationStatuses()
    {
        $data = DB::table('lst_registration_status')
            ->select('id', 'status as name')
            ->orderBy('status')
            ->get();

        return $this->success(['registration_statuses' => LookupResource::collection($data)]);
    }

    /**
     * List License Statuses
     */
    public function licenseStatuses()
    {
        $data = DB::table('lst_license_status')
            ->select('id', 'status as name')
            ->orderBy('status')
            ->get();

        return $this->success(['license_statuses' => LookupResource::collection($data)]);
    }

    /**
     * List Accreditation Statuses
     */
    public function accreditationStatuses()
    {
        $data = DB::table('lst_accreditation_status')
            ->select('id', 'status as name')
            ->orderBy('status')
            ->get();

        return $this->success(['accreditation_statuses' => LookupResource::collection($data)]);
    }

    /**
     * List Service Categories
     *
     * Retrieve all health service categories.
     */
    public function serviceCategories()
    {
        $data = DB::table('lst_hosp_service_category')
            ->select('id', 'description as name')
            ->orderBy('description')
            ->get();

        return $this->success(['service_categories' => LookupResource::collection($data)]);
    }

    /**
     * List Services
     *
     * Retrieve all health services, optionally filtered by category.
     *
     * @queryParam category_id integer Filter services by category. Example: 3
     */
    public function services(Request $request)
    {
        $query = DB::table('lst_hosp_services')
            ->select('id', 'name', 'service_category_id as category_id')
            ->orderBy('name');

        if ($request->filled('category_id')) {
            $query->where('service_category_id', $request->category_id);
        }

        return $this->success(['services' => LookupResource::collection($query->get())]);
    }
}
