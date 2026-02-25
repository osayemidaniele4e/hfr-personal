<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\FacilityResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @group Facilities
 *
 * Endpoints for searching and retrieving health facilities (hospitals & clinics).
 */
class FacilityController extends BaseApiController
{
    /**
     * List / Search Facilities
     *
     * Search and filter health facilities with pagination.
     *
     * @queryParam search string Search by facility name. Example: General Hospital
     * @queryParam state_id integer Filter by state ID. Example: 25
     * @queryParam lga_id integer Filter by LGA ID. Example: 512
     * @queryParam ward_id integer Filter by ward ID. Example: 1024
     * @queryParam facility_type_id integer Filter by facility type. Example: 1
     * @queryParam facility_level_id integer Filter by level of care. Example: 2
     * @queryParam ownership_id integer Filter by ownership category. Example: 1
     * @queryParam ownership_type_id integer Filter by ownership type. Example: 3
     * @queryParam operational_status_id integer Filter by operational status. Example: 1
     * @queryParam registration_status_id integer Filter by registration status. Example: 1
     * @queryParam license_status_id integer Filter by license status. Example: 1
     * @queryParam has_coordinates boolean Filter facilities with/without GPS coordinates. Example: true
     * @queryParam service_ids string Comma-separated service IDs to filter by. Example: 1,5,12
     * @queryParam per_page integer Results per page (max 100, default 25). Example: 25
     * @queryParam page integer Page number. Example: 1
     * @queryParam sort_by string Sort field: facility_name, state, lga, updated_at. Example: facility_name
     * @queryParam sort_order string Sort direction: asc or desc. Example: asc
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string|max:255',
            'state_id' => 'nullable|integer',
            'lga_id' => 'nullable|integer',
            'ward_id' => 'nullable|integer',
            'facility_type_id' => 'nullable|integer',
            'facility_level_id' => 'nullable|integer',
            'ownership_id' => 'nullable|integer',
            'ownership_type_id' => 'nullable|integer',
            'operational_status_id' => 'nullable|integer',
            'registration_status_id' => 'nullable|integer',
            'license_status_id' => 'nullable|integer',
            'has_coordinates' => 'nullable|boolean',
            'service_ids' => 'nullable|string',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'sort_by' => 'nullable|in:facility_name,state,lga,updated_at',
            'sort_order' => 'nullable|in:asc,desc',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', 'VALIDATION_ERROR', 422, $validator->errors());
        }

        $perPage = min($request->input('per_page', 25), 100);

        $query = DB::table('hs_hospitals_history')
            ->leftJoin('ou_states', 'hs_hospitals_history.state_id', '=', 'ou_states.id')
            ->leftJoin('ou_lgas', 'hs_hospitals_history.lga_id', '=', 'ou_lgas.id')
            ->leftJoin('ou_wards', 'hs_hospitals_history.ward_id', '=', 'ou_wards.id')
            ->leftJoin('lst_facility_types', 'hs_hospitals_history.facility_type_id', '=', 'lst_facility_types.id')
            ->leftJoin('lst_level_of_care', 'hs_hospitals_history.facility_level_id', '=', 'lst_level_of_care.id')
            ->leftJoin('lst_ownerships', 'hs_hospitals_history.ownership_id', '=', 'lst_ownerships.id')
            ->leftJoin('lst_ownership_types', 'hs_hospitals_history.ownership_type_id', '=', 'lst_ownership_types.id')
            ->leftJoin('lst_oparational_status', 'hs_hospitals_history.operational_status_id', '=', 'lst_oparational_status.id')
            ->leftJoin('lst_registration_status', 'hs_hospitals_history.registration_status_id', '=', 'lst_registration_status.id')
            ->leftJoin('lst_license_status', 'hs_hospitals_history.license_status_id', '=', 'lst_license_status.id')
            ->select(
                'hs_hospitals_history.*',
                'ou_states.name as state_name',
                'ou_lgas.name as lga_name',
                'ou_wards.name as ward_name',
                'lst_facility_types.name as facility_type_name',
                'lst_level_of_care.name as facility_level_name',
                'lst_ownerships.name as ownership_name',
                'lst_ownership_types.type as ownership_type',
                'lst_oparational_status.status as operational_status_name',
                'lst_registration_status.status as registration_status_name',
                'lst_license_status.status as license_status_name'
            );

        // Apply filters
        $this->applyFilters($query, $request);

        // Apply service filter
        if ($request->filled('service_ids')) {
            $serviceIds = array_map('intval', explode(',', $request->service_ids));
            $hospitalIds = DB::table('hs_hospital_services')
                ->whereIn('service_id', $serviceIds)
                ->distinct()
                ->pluck('hospital_id')
                ->toArray();
            $query->whereIn('hs_hospitals_history.id', $hospitalIds);
        }

        // Search
        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(hs_hospitals_history.facility_name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(ou_states.name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(ou_lgas.name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(ou_wards.name) LIKE ?', ["%{$search}%"]);
            });
        }

        // Coordinates filter
        if ($request->has('has_coordinates')) {
            if ($request->boolean('has_coordinates')) {
                $query->whereNotNull('hs_hospitals_history.latitude')
                    ->where('hs_hospitals_history.latitude', '<>', '');
            } else {
                $query->where(function ($q) {
                    $q->whereNull('hs_hospitals_history.latitude')
                        ->orWhere('hs_hospitals_history.latitude', '=', '');
                });
            }
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'facility_name');
        $sortOrder = $request->input('sort_order', 'asc');
        $sortMap = [
            'facility_name' => 'hs_hospitals_history.facility_name',
            'state' => 'ou_states.name',
            'lga' => 'ou_lgas.name',
            'updated_at' => 'hs_hospitals_history.updated_at',
        ];
        $query->orderBy($sortMap[$sortBy] ?? 'hs_hospitals_history.facility_name', $sortOrder);

        $paginator = $query->paginate($perPage);

        return $this->paginated($paginator, FacilityResource::class, 'facilities');
    }

    /**
     * Get Facility Detail
     *
     * Retrieve detailed information about a single facility by ID.
     *
     * @urlParam id integer required The facility ID. Example: 123
     */
    public function show(Request $request, $id)
    {
        $facility = DB::table('hs_hospitals_history')
            ->leftJoin('ou_states', 'hs_hospitals_history.state_id', '=', 'ou_states.id')
            ->leftJoin('ou_lgas', 'hs_hospitals_history.lga_id', '=', 'ou_lgas.id')
            ->leftJoin('ou_wards', 'hs_hospitals_history.ward_id', '=', 'ou_wards.id')
            ->leftJoin('lst_facility_types', 'hs_hospitals_history.facility_type_id', '=', 'lst_facility_types.id')
            ->leftJoin('lst_level_of_care', 'hs_hospitals_history.facility_level_id', '=', 'lst_level_of_care.id')
            ->leftJoin('lst_ownerships', 'hs_hospitals_history.ownership_id', '=', 'lst_ownerships.id')
            ->leftJoin('lst_ownership_types', 'hs_hospitals_history.ownership_type_id', '=', 'lst_ownership_types.id')
            ->leftJoin('lst_oparational_status', 'hs_hospitals_history.operational_status_id', '=', 'lst_oparational_status.id')
            ->leftJoin('lst_registration_status', 'hs_hospitals_history.registration_status_id', '=', 'lst_registration_status.id')
            ->leftJoin('lst_license_status', 'hs_hospitals_history.license_status_id', '=', 'lst_license_status.id')
            ->select(
                'hs_hospitals_history.*',
                'ou_states.name as state_name',
                'ou_lgas.name as lga_name',
                'ou_wards.name as ward_name',
                'lst_facility_types.name as facility_type_name',
                'lst_level_of_care.name as facility_level_name',
                'lst_ownerships.name as ownership_name',
                'lst_ownership_types.type as ownership_type',
                'lst_oparational_status.status as operational_status_name',
                'lst_registration_status.status as registration_status_name',
                'lst_license_status.status as license_status_name'
            )
            ->where('hs_hospitals_history.id', $id)
            ->first();

        if (!$facility) {
            return $this->error('Facility not found.', 'NOT_FOUND', 404);
        }

        return $this->success([
            'facility' => new FacilityResource((object) $facility),
        ]);
    }

    /**
     * Get Facility Services
     *
     * Retrieve services offered by a specific facility.
     *
     * @urlParam id integer required The facility ID. Example: 123
     */
    public function services(Request $request, $id)
    {
        // Verify facility exists
        $exists = DB::table('hs_hospitals_history')->where('id', $id)->exists();
        if (!$exists) {
            return $this->error('Facility not found.', 'NOT_FOUND', 404);
        }

        $services = DB::table('hs_hospital_services')
            ->join('lst_hosp_services', 'hs_hospital_services.service_id', '=', 'lst_hosp_services.id')
            ->leftJoin('lst_hosp_service_category', 'lst_hosp_services.service_category_id', '=', 'lst_hosp_service_category.id')
            ->where('hs_hospital_services.hospital_id', $id)
            ->select(
                'lst_hosp_services.id',
                'lst_hosp_services.name as service_name',
                'lst_hosp_service_category.id as category_id',
                'lst_hosp_service_category.description as category_name'
            )
            ->orderBy('lst_hosp_service_category.description')
            ->orderBy('lst_hosp_services.name')
            ->get();

        return $this->success([
            'facility_id' => (int) $id,
            'services' => $services,
            'total' => $services->count(),
        ]);
    }

    /**
     * Facility Statistics
     *
     * Get summary statistics of facilities across the registry.
     *
     * @queryParam state_id integer Filter statistics by state. Example: 25
     * @queryParam lga_id integer Filter statistics by LGA. Example: 512
     */
    public function statistics(Request $request)
    {
        $query = DB::table('hs_hospitals_history');

        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }
        if ($request->filled('lga_id')) {
            $query->where('lga_id', $request->lga_id);
        }

        $total = (clone $query)->count();

        $byOwnership = DB::table('hs_hospitals_history')
            ->join('lst_ownerships', 'hs_hospitals_history.ownership_id', '=', 'lst_ownerships.id')
            ->when($request->filled('state_id'), fn($q) => $q->where('hs_hospitals_history.state_id', $request->state_id))
            ->when($request->filled('lga_id'), fn($q) => $q->where('hs_hospitals_history.lga_id', $request->lga_id))
            ->groupBy('lst_ownerships.id', 'lst_ownerships.name')
            ->select('lst_ownerships.id', 'lst_ownerships.name', DB::raw('COUNT(*) as count'))
            ->get();

        $byLevel = DB::table('hs_hospitals_history')
            ->join('lst_level_of_care', 'hs_hospitals_history.facility_level_id', '=', 'lst_level_of_care.id')
            ->when($request->filled('state_id'), fn($q) => $q->where('hs_hospitals_history.state_id', $request->state_id))
            ->when($request->filled('lga_id'), fn($q) => $q->where('hs_hospitals_history.lga_id', $request->lga_id))
            ->groupBy('lst_level_of_care.id', 'lst_level_of_care.name')
            ->select('lst_level_of_care.id', 'lst_level_of_care.name', DB::raw('COUNT(*) as count'))
            ->get();

        $byState = DB::table('hs_hospitals_history')
            ->join('ou_states', 'hs_hospitals_history.state_id', '=', 'ou_states.id')
            ->when($request->filled('state_id'), fn($q) => $q->where('hs_hospitals_history.state_id', $request->state_id))
            ->when($request->filled('lga_id'), fn($q) => $q->where('hs_hospitals_history.lga_id', $request->lga_id))
            ->groupBy('ou_states.id', 'ou_states.name')
            ->select('ou_states.id', 'ou_states.name', DB::raw('COUNT(*) as count'))
            ->orderBy('ou_states.name')
            ->get();

        return $this->success([
            'total_facilities' => $total,
            'by_ownership' => $byOwnership,
            'by_level_of_care' => $byLevel,
            'by_state' => $byState,
        ]);
    }

    /**
     * Apply common filters to the facility query.
     */
    private function applyFilters($query, Request $request): void
    {
        $filters = [
            'state_id' => 'hs_hospitals_history.state_id',
            'lga_id' => 'hs_hospitals_history.lga_id',
            'ward_id' => 'hs_hospitals_history.ward_id',
            'facility_type_id' => 'hs_hospitals_history.facility_type_id',
            'facility_level_id' => 'hs_hospitals_history.facility_level_id',
            'ownership_id' => 'hs_hospitals_history.ownership_id',
            'ownership_type_id' => 'hs_hospitals_history.ownership_type_id',
            'operational_status_id' => 'hs_hospitals_history.operational_status_id',
            'registration_status_id' => 'hs_hospitals_history.registration_status_id',
            'license_status_id' => 'hs_hospitals_history.license_status_id',
        ];

        foreach ($filters as $param => $column) {
            if ($request->filled($param)) {
                $query->where($column, $request->input($param));
            }
        }
    }
}
