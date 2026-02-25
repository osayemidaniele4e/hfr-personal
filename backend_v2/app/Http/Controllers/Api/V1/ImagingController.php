<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\ImagingResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @group Imaging Facilities
 *
 * Endpoints for searching and retrieving radiological / imaging premises.
 */
class ImagingController extends BaseApiController
{
    /**
     * List / Search Imaging Facilities
     *
     * @queryParam search string Search by facility name. Example: Radiology Center
     * @queryParam state_id integer Filter by state. Example: 25
     * @queryParam lga_id integer Filter by LGA. Example: 512
     * @queryParam ward_id integer Filter by ward. Example: 1024
     * @queryParam ownership_id integer Filter by ownership. Example: 1
     * @queryParam operational_status_id integer Filter by operational status. Example: 1
     * @queryParam registration_status_id integer Filter by registration status. Example: 1
     * @queryParam license_status_id integer Filter by license status. Example: 1
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
            'ownership_id' => 'nullable|integer',
            'operational_status_id' => 'nullable|integer',
            'registration_status_id' => 'nullable|integer',
            'license_status_id' => 'nullable|integer',
            'has_coordinates' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', 'VALIDATION_ERROR', 422, $validator->errors());
        }

        $perPage = min($request->input('per_page', 25), 100);

        $query = DB::table('im_imagings')
            ->leftJoin('ou_states', 'im_imagings.state_id', '=', 'ou_states.id')
            ->leftJoin('ou_lgas', 'im_imagings.lga_id', '=', 'ou_lgas.id')
            ->leftJoin('ou_wards', 'im_imagings.ward_id', '=', 'ou_wards.id')
            ->leftJoin('lst_ownerships', 'im_imagings.ownership_id', '=', 'lst_ownerships.id')
            ->leftJoin('lst_oparational_status', 'im_imagings.operational_status_id', '=', 'lst_oparational_status.id')
            ->leftJoin('lst_registration_status', 'im_imagings.registration_status_id', '=', 'lst_registration_status.id')
            ->leftJoin('lst_license_status', 'im_imagings.license_status_id', '=', 'lst_license_status.id')
            ->select(
                'im_imagings.*',
                'ou_states.name as state_name',
                'ou_lgas.name as lga_name',
                'ou_wards.name as ward_name',
                'lst_ownerships.name as ownership_name',
                'lst_oparational_status.status as operational_status',
                'lst_registration_status.status as registration_status',
                'lst_license_status.status as license_status'
            );

        // Apply filters
        $filters = [
            'state_id' => 'im_imagings.state_id',
            'lga_id' => 'im_imagings.lga_id',
            'ward_id' => 'im_imagings.ward_id',
            'ownership_id' => 'im_imagings.ownership_id',
            'operational_status_id' => 'im_imagings.operational_status_id',
            'registration_status_id' => 'im_imagings.registration_status_id',
            'license_status_id' => 'im_imagings.license_status_id',
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
                $q->whereRaw('LOWER(im_imagings.facility_name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(ou_states.name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(ou_lgas.name) LIKE ?', ["%{$search}%"]);
            });
        }

        // Coordinates filter
        if ($request->has('has_coordinates')) {
            if ($request->boolean('has_coordinates')) {
                $query->whereNotNull('im_imagings.latitude')
                    ->where('im_imagings.latitude', '<>', '');
            } else {
                $query->where(function ($q) {
                    $q->whereNull('im_imagings.latitude')
                        ->orWhere('im_imagings.latitude', '=', '');
                });
            }
        }

        $query->orderBy('im_imagings.state_id')
            ->orderBy('im_imagings.lga_id')
            ->orderBy('im_imagings.facility_name');

        $paginator = $query->paginate($perPage);

        return $this->paginated($paginator, ImagingResource::class, 'imaging_facilities');
    }

    /**
     * Get Imaging Facility Detail
     *
     * @urlParam id integer required The imaging facility ID. Example: 33
     */
    public function show(Request $request, $id)
    {
        $imaging = DB::table('im_imagings')
            ->leftJoin('ou_states', 'im_imagings.state_id', '=', 'ou_states.id')
            ->leftJoin('ou_lgas', 'im_imagings.lga_id', '=', 'ou_lgas.id')
            ->leftJoin('ou_wards', 'im_imagings.ward_id', '=', 'ou_wards.id')
            ->leftJoin('lst_ownerships', 'im_imagings.ownership_id', '=', 'lst_ownerships.id')
            ->leftJoin('lst_oparational_status', 'im_imagings.operational_status_id', '=', 'lst_oparational_status.id')
            ->leftJoin('lst_registration_status', 'im_imagings.registration_status_id', '=', 'lst_registration_status.id')
            ->leftJoin('lst_license_status', 'im_imagings.license_status_id', '=', 'lst_license_status.id')
            ->select(
                'im_imagings.*',
                'ou_states.name as state_name',
                'ou_lgas.name as lga_name',
                'ou_wards.name as ward_name',
                'lst_ownerships.name as ownership_name',
                'lst_oparational_status.status as operational_status',
                'lst_registration_status.status as registration_status',
                'lst_license_status.status as license_status'
            )
            ->where('im_imagings.id', $id)
            ->first();

        if (!$imaging) {
            return $this->error('Imaging facility not found.', 'NOT_FOUND', 404);
        }

        return $this->success([
            'imaging_facility' => new ImagingResource((object) $imaging),
        ]);
    }
}
