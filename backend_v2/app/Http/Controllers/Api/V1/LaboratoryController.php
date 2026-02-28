<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\LaboratoryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @group Laboratories
 *
 * Endpoints for searching and retrieving laboratory premises.
 */
class LaboratoryController extends BaseApiController
{
    /**
     * List / Search Laboratories
     *
     * Search and filter laboratory premises with pagination.
     *
     * @queryParam search string Search by facility name. Example: National Lab
     * @queryParam state_id integer Filter by state. Example: 25
     * @queryParam lga_id integer Filter by LGA. Example: 512
     * @queryParam ward_id integer Filter by ward. Example: 1024
     * @queryParam facility_level_id integer Filter by facility level. Example: 2
     * @queryParam ownership_id integer Filter by ownership. Example: 1
     * @queryParam operational_status_id integer Filter by operational status. Example: 1
     * @queryParam registration_status_id integer Filter by registration status. Example: 1
     * @queryParam license_status_id integer Filter by license status. Example: 1
     * @queryParam accreditation_status_id integer Filter by accreditation status. Example: 1
     * @queryParam has_coordinates boolean Filter by GPS availability. Example: true
     * @queryParam per_page integer Results per page (max 100, default 25). Example: 25
     * @queryParam page integer Page number. Example: 1
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string|max:255',
            'state_id' => 'nullable|integer',
            'lga_id' => 'nullable|integer',
            'ward_id' => 'nullable|integer',
            'facility_level_id' => 'nullable|integer',
            'ownership_id' => 'nullable|integer',
            'operational_status_id' => 'nullable|integer',
            'registration_status_id' => 'nullable|integer',
            'license_status_id' => 'nullable|integer',
            'accreditation_status_id' => 'nullable|integer',
            'has_coordinates' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', 'VALIDATION_ERROR', 422, $validator->errors());
        }

        $perPage = min($request->input('per_page', 25), 100);

        $query = DB::table('lb_laboratories')
            ->leftJoin('ou_states', 'lb_laboratories.state_id', '=', 'ou_states.id')
            ->leftJoin('ou_lgas', 'lb_laboratories.lga_id', '=', 'ou_lgas.id')
            ->leftJoin('ou_wards', 'lb_laboratories.ward_id', '=', 'ou_wards.id')
            ->leftJoin('lst_level_of_care', 'lb_laboratories.facility_level_id', '=', 'lst_level_of_care.id')
            ->leftJoin('lst_ownerships', 'lb_laboratories.ownership_id', '=', 'lst_ownerships.id')
            ->leftJoin('lst_oparational_status', 'lb_laboratories.operational_status_id', '=', 'lst_oparational_status.id')
            ->leftJoin('lst_registration_status', 'lb_laboratories.registration_status_id', '=', 'lst_registration_status.id')
            ->leftJoin('lst_license_status', 'lb_laboratories.license_status_id', '=', 'lst_license_status.id')
            ->leftJoin('lst_accreditation_status', 'lb_laboratories.accreditation_status_id', '=', 'lst_accreditation_status.id')
            ->select(
                'lb_laboratories.*',
                'ou_states.name as state_name',
                'ou_lgas.name as lga_name',
                'ou_wards.name as ward_name',
                'lst_level_of_care.name as facility_level_name',
                'lst_ownerships.name as ownership_name',
                'lst_oparational_status.status as operational_status_name',
                'lst_registration_status.status as registration_status_name',
                'lst_license_status.status as license_status_name',
                'lst_accreditation_status.status as accreditation_status_name'
            );

        // Apply filters
        $filters = [
            'state_id' => 'lb_laboratories.state_id',
            'lga_id' => 'lb_laboratories.lga_id',
            'ward_id' => 'lb_laboratories.ward_id',
            'facility_level_id' => 'lb_laboratories.facility_level_id',
            'ownership_id' => 'lb_laboratories.ownership_id',
            'operational_status_id' => 'lb_laboratories.operational_status_id',
            'registration_status_id' => 'lb_laboratories.registration_status_id',
            'license_status_id' => 'lb_laboratories.license_status_id',
            'accreditation_status_id' => 'lb_laboratories.accreditation_status_id',
        ];

        foreach ($filters as $param => $column) {
            if ($request->filled($param)) {
                $query->where($column, $request->input($param));
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(lb_laboratories.facility_name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(ou_states.name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(ou_lgas.name) LIKE ?', ["%{$search}%"]);
            });
        }

        // Coordinates filter
        if ($request->has('has_coordinates')) {
            if ($request->boolean('has_coordinates')) {
                $query->whereNotNull('lb_laboratories.latitude')
                    ->where('lb_laboratories.latitude', '<>', '');
            } else {
                $query->where(function ($q) {
                    $q->whereNull('lb_laboratories.latitude')
                        ->orWhere('lb_laboratories.latitude', '=', '');
                });
            }
        }

        $query->orderBy('lb_laboratories.state_id')
            ->orderBy('lb_laboratories.lga_id')
            ->orderBy('lb_laboratories.facility_name');

        $paginator = $query->paginate($perPage);

        return $this->paginated($paginator, LaboratoryResource::class, 'laboratories');
    }

    /**
     * Get Laboratory Detail
     *
     * @urlParam id integer required The laboratory ID. Example: 78
     */
    public function show(Request $request, $id)
    {
        $lab = DB::table('lb_laboratories')
            ->leftJoin('ou_states', 'lb_laboratories.state_id', '=', 'ou_states.id')
            ->leftJoin('ou_lgas', 'lb_laboratories.lga_id', '=', 'ou_lgas.id')
            ->leftJoin('ou_wards', 'lb_laboratories.ward_id', '=', 'ou_wards.id')
            ->leftJoin('lst_level_of_care', 'lb_laboratories.facility_level_id', '=', 'lst_level_of_care.id')
            ->leftJoin('lst_ownerships', 'lb_laboratories.ownership_id', '=', 'lst_ownerships.id')
            ->leftJoin('lst_oparational_status', 'lb_laboratories.operational_status_id', '=', 'lst_oparational_status.id')
            ->leftJoin('lst_registration_status', 'lb_laboratories.registration_status_id', '=', 'lst_registration_status.id')
            ->leftJoin('lst_license_status', 'lb_laboratories.license_status_id', '=', 'lst_license_status.id')
            ->leftJoin('lst_accreditation_status', 'lb_laboratories.accreditation_status_id', '=', 'lst_accreditation_status.id')
            ->select(
                'lb_laboratories.*',
                'ou_states.name as state_name',
                'ou_lgas.name as lga_name',
                'ou_wards.name as ward_name',
                'lst_level_of_care.name as facility_level_name',
                'lst_ownerships.name as ownership_name',
                'lst_oparational_status.status as operational_status_name',
                'lst_registration_status.status as registration_status_name',
                'lst_license_status.status as license_status_name',
                'lst_accreditation_status.status as accreditation_status_name'
            )
            ->where('lb_laboratories.id', $id)
            ->first();

        if (!$lab) {
            return $this->error('Laboratory not found.', 'NOT_FOUND', 404);
        }

        return $this->success([
            'laboratory' => new LaboratoryResource((object) $lab),
        ]);
    }
}
