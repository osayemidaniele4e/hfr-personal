<?php

namespace App\Http\Controllers\Api\Fhir;

use App\Fhir\Search\FhirSearchParser;
use App\Fhir\Transformers\HealthcareServiceTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @group FHIR - HealthcareService
 *
 * FHIR R4 HealthcareService endpoints. Services offered by hospital-type
 * facilities, sourced from the hs_hospital_services registry.
 */
class HealthcareServiceController extends FhirBaseController
{
    protected HealthcareServiceTransformer $transformer;

    public function __construct()
    {
        parent::__construct();
        $this->transformer = new HealthcareServiceTransformer();
    }

    /**
     * Search HealthcareServices
     *
     * Search healthcare services offered by facilities.
     *
     * @queryParam organization string Providing organization reference. Example: Organization/hosp-123
     * @queryParam location string Service location reference. Example: Location/loc-hosp-123
     * @queryParam name string Service name (partial match). Example: Surgery
     * @queryParam service-category string Service category. Example: Clinical
     * @queryParam service-type string Service type code or name. Example: Radiology
     * @queryParam active string Active status. Example: true
     * @queryParam _count integer Results per page (max 100). Example: 20
     * @queryParam _offset integer Pagination offset. Example: 0
     */
    public function search(Request $request): JsonResponse
    {
        $search = new FhirSearchParser($request);
        $perPage = $search->getCount();
        $page = $search->getPage();

        $query = $this->buildServiceQuery();
        $this->applyServiceSearchParams($query, $search);

        $total = (clone $query)->count();

        // Sorting
        $query->orderBy('lst_hosp_service_category.description', 'asc')
            ->orderBy('lst_hosp_services.name', 'asc');

        // Paginate
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        // Transform each service — we need the parent facility for references
        $resources = [];
        foreach ($paginator->items() as $service) {
            // Build a minimal facility object for the transformer's reference building
            $facilityStub = (object) [
                'id' => $service->hospital_id,
                'facility_name' => $service->facility_name ?? 'Unknown Facility',
            ];
            $resources[] = $this->transformer->transform($service, $facilityStub, 'hospital');
        }

        return $this->fhirSearchBundle($paginator, $resources, $request, 'HealthcareService');
    }

    /**
     * Read HealthcareService
     *
     * Retrieve a single HealthcareService by its FHIR resource ID.
     *
     * @urlParam id string required The HealthcareService resource ID. Example: svc-45
     */
    public function read(string $id): JsonResponse
    {
        // Parse svc-{id}
        $numericId = $this->parseServiceId($id);
        if ($numericId === null) {
            return $this->fhirNotFound('HealthcareService', $id);
        }

        $service = $this->buildServiceQuery()
            ->where('hs_hospital_services.id', $numericId)
            ->first();

        if (!$service) {
            return $this->fhirNotFound('HealthcareService', $id);
        }

        $facilityStub = (object) [
            'id' => $service->hospital_id,
            'facility_name' => $service->facility_name ?? 'Unknown Facility',
        ];

        return $this->fhirResponse($this->transformer->transform($service, $facilityStub, 'hospital'));
    }

    // ──────────────────────────────────────────────────────────
    // Private Helpers
    // ──────────────────────────────────────────────────────────

    /**
     * Build the base service query with all necessary joins.
     */
    protected function buildServiceQuery(): \Illuminate\Database\Query\Builder
    {
        return DB::table('hs_hospital_services')
            ->join('lst_hosp_services', 'hs_hospital_services.service_id', '=', 'lst_hosp_services.id')
            ->leftJoin('lst_hosp_service_category', 'lst_hosp_services.service_category_id', '=', 'lst_hosp_service_category.id')
            ->leftJoin('hs_hospitals_history', 'hs_hospital_services.hospital_id', '=', 'hs_hospitals_history.id')
            ->select(
                'hs_hospital_services.id',
                'hs_hospital_services.hospital_id',
                'lst_hosp_services.id as service_master_id',
                'lst_hosp_services.name as service_name',
                'lst_hosp_service_category.id as category_id',
                'lst_hosp_service_category.description as category_name',
                'hs_hospitals_history.facility_name'
            );
    }

    /**
     * Apply FHIR search parameters for HealthcareService.
     */
    protected function applyServiceSearchParams($query, FhirSearchParser $search): void
    {
        // organization (reference to Organization/hosp-{id})
        $orgParam = request()->input('organization');
        if ($orgParam) {
            $orgId = $this->extractNumericId($orgParam);
            if ($orgId) {
                $query->where('hs_hospital_services.hospital_id', '=', $orgId);
            }
        }

        // location (reference to Location/loc-hosp-{id})
        $locParam = request()->input('location');
        if ($locParam) {
            $locId = $this->extractNumericId($locParam);
            if ($locId) {
                $query->where('hs_hospital_services.hospital_id', '=', $locId);
            }
        }

        // name
        $search->applyStringSearch($query, 'name', 'lst_hosp_services.name');

        // service-category
        $categoryParam = request()->input('service-category');
        if ($categoryParam) {
            if (is_numeric($categoryParam)) {
                $query->where('lst_hosp_service_category.id', '=', (int) $categoryParam);
            } else {
                $query->whereRaw('LOWER(lst_hosp_service_category.description) LIKE ?', ['%' . strtolower($categoryParam) . '%']);
            }
        }

        // service-type
        $typeParam = request()->input('service-type');
        if ($typeParam) {
            if (is_numeric($typeParam)) {
                $query->where('lst_hosp_services.id', '=', (int) $typeParam);
            } else {
                $query->whereRaw('LOWER(lst_hosp_services.name) LIKE ?', ['%' . strtolower($typeParam) . '%']);
            }
        }

        // _id
        $idParam = request()->input('_id');
        if ($idParam) {
            $numericId = $this->parseServiceId($idParam);
            if ($numericId) {
                $query->where('hs_hospital_services.id', '=', $numericId);
            }
        }
    }

    /**
     * Parse a HealthcareService resource ID (svc-{id} or plain numeric).
     */
    protected function parseServiceId(string $id): ?int
    {
        if (preg_match('/^svc-(\d+)$/', $id, $matches)) {
            return (int) $matches[1];
        }

        if (is_numeric($id)) {
            return (int) $id;
        }

        return null;
    }

    /**
     * Extract numeric ID from a FHIR reference or prefixed ID.
     */
    protected function extractNumericId(string $value): ?int
    {
        // Handle "Organization/hosp-123" or "Location/loc-hosp-123" or "hosp-123"
        if (str_contains($value, '/')) {
            $value = explode('/', $value, 2)[1];
        }

        // Strip prefix
        $numeric = preg_replace('/^(loc-)?(hosp|lab|pharm|img|svc)-/', '', $value);

        return is_numeric($numeric) ? (int) $numeric : null;
    }
}
