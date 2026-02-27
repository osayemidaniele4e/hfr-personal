<?php

namespace App\Http\Controllers\Api\Fhir;

use App\Fhir\Search\FhirSearchParser;
use App\Fhir\Transformers\LocationTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group FHIR - Location
 *
 * FHIR R4 Location endpoints. Each health facility's physical site is
 * represented as a Location resource with GPS coordinates, address, and
 * a reference to the managing Organization.
 */
class LocationController extends FhirBaseController
{
    protected LocationTransformer $transformer;

    public function __construct()
    {
        parent::__construct();
        $this->transformer = new LocationTransformer();
    }

    /**
     * Search Locations
     *
     * Search across all facility locations. Supports geo-search via the `near` parameter.
     *
     * @queryParam name string Location name (partial match). Example: General Hospital
     * @queryParam identifier string Facility code or registration number. Example: RC/00001
     * @queryParam status string Location status (active, suspended, inactive). Example: active
     * @queryParam type string Location type code (HOSP, MBL, PHARM, RADDX). Example: HOSP
     * @queryParam address string Free text address search. Example: Victoria Island
     * @queryParam address-state string State name. Example: Lagos
     * @queryParam organization string Managing organization reference. Example: Organization/hosp-123
     * @queryParam near string Geo-search: latitude|longitude|distance|units. Example: 6.5244|3.3792|10|km
     * @queryParam _count integer Results per page (max 100). Example: 20
     * @queryParam _offset integer Pagination offset. Example: 0
     * @queryParam _sort string Sort: name, -name, _lastUpdated, -_lastUpdated. Example: name
     */
    public function search(Request $request): JsonResponse
    {
        $search = new FhirSearchParser($request);
        $perPage = $search->getCount();
        $page = $search->getPage();

        // Determine which registries to query
        $registries = $this->resolveRegistries($request->input('type'));

        $allResources = [];
        $totalCount = 0;
        $offset = ($page - 1) * $perPage;
        $skipRemaining = $offset;
        $needed = $perPage;

        foreach ($registries as $registry) {
            $query = $this->buildRegistryQuery($registry);
            $table = $this->getTableName($registry);

            $this->applyLocationSearchParams($query, $search, $registry);

            $registryCount = (clone $query)->count();
            $totalCount += $registryCount;

            // Skip this registry entirely if offset exceeds its count
            if ($skipRemaining >= $registryCount) {
                $skipRemaining -= $registryCount;
                continue;
            }

            // Already have enough resources for this page
            if ($needed <= 0) {
                continue;
            }

            // Sorting
            $sortFields = [
                'name' => "{$table}.facility_name",
                '_lastUpdated' => "{$table}.updated_at",
            ];
            $sorts = $search->parseSortParams($sortFields);
            foreach ($sorts as [$column, $direction]) {
                $query->orderBy($column, $direction);
            }
            if (empty($sorts)) {
                $query->orderBy("{$table}.facility_name", 'asc');
            }

            // SQL-level offset and limit
            $results = $query->offset($skipRemaining)->limit($needed)->get();
            $skipRemaining = 0;

            foreach ($results as $row) {
                $allResources[] = $this->transformer->transform($row, $registry);
            }
            $needed -= $results->count();
        }

        $pagedResources = $allResources;

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedResources,
            $totalCount,
            $perPage,
            $page,
            ['path' => url(config('hfr.fhir.base_url') . '/Location')]
        );

        return $this->fhirSearchBundle($paginator, $pagedResources, $request, 'Location');
    }

    /**
     * Read Location
     *
     * Retrieve a single Location by its FHIR resource ID.
     * IDs are prefixed: loc-hosp-{id}, loc-lab-{id}, loc-pharm-{id}, loc-img-{id}.
     *
     * @urlParam id string required The Location resource ID. Example: loc-hosp-123
     */
    public function read(string $id): JsonResponse
    {
        $parsed = $this->parseLocationId($id);

        if (!$parsed) {
            return $this->fhirNotFound('Location', $id);
        }

        [$registry, $numericId] = $parsed;

        $query = $this->buildRegistryQuery($registry);
        $facility = $query->where($this->getTableName($registry) . '.id', $numericId)->first();

        if (!$facility) {
            return $this->fhirNotFound('Location', $id);
        }

        return $this->fhirResponse($this->transformer->transform($facility, $registry));
    }

    // ──────────────────────────────────────────────────────────
    // Private Helpers
    // ──────────────────────────────────────────────────────────

    protected function resolveRegistries(?string $typeParam): array
    {
        if ($typeParam === null) {
            return ['hospital', 'laboratory', 'pharmacy', 'imaging'];
        }

        $code = strtolower($typeParam);

        return match ($code) {
            'hosp', 'hospital' => ['hospital'],
            'mbl', 'laboratory', 'lab' => ['laboratory'],
            'pharm', 'pharmacy' => ['pharmacy'],
            'raddx', 'imaging', 'img' => ['imaging'],
            default => ['hospital', 'laboratory', 'pharmacy', 'imaging'],
        };
    }

    protected function buildRegistryQuery(string $registry): \Illuminate\Database\Query\Builder
    {
        return match ($registry) {
            'laboratory' => $this->baseLaboratoryQuery(),
            'pharmacy' => $this->basePharmacyQuery(),
            'imaging' => $this->baseImagingQuery(),
            default => $this->baseFacilityQuery(),
        };
    }

    protected function getTableName(string $registry): string
    {
        return match ($registry) {
            'laboratory' => 'lb_laboratories',
            'pharmacy' => 'pharmacies',
            'imaging' => 'im_imagings',
            default => 'hs_hospitals_history',
        };
    }

    protected function applyLocationSearchParams($query, FhirSearchParser $search, string $registry): void
    {
        $table = $this->getTableName($registry);

        // name
        $search->applyStringSearch($query, 'name', "{$table}.facility_name");

        // identifier
        $search->applyIdentifierSearch($query, 'identifier', [
            "{$table}.unique_id",
            "{$table}.state_unique_id",
            "{$table}.registration_no",
        ]);

        // status (FHIR Location.status: active|suspended|inactive → operational_status_id)
        $statusParam = request()->input('status');
        if ($statusParam) {
            $statusMap = [
                'active' => 1,
                'suspended' => 2,
                'inactive' => 3,
            ];
            if (isset($statusMap[$statusParam])) {
                $query->where("{$table}.operational_status_id", '=', $statusMap[$statusParam]);
            }
        }

        // address (free text across location fields)
        $search->applyAddressSearch($query, 'address', [
            "{$table}.physical_location",
            "{$table}.postal_address",
            'ou_states.name',
            'ou_lgas.name',
            'ou_wards.name',
        ]);

        // address-state
        $search->applyAddressStateSearch($query, 'ou_states.name', "{$table}.state_id");

        // organization (reference filter)
        $search->applyReferenceSearch($query, 'organization', "{$table}.id");

        // near (geo-search)
        $search->applyNearSearch($query, "{$table}.latitude", "{$table}.longitude");

        // _id
        $idParam = request()->input('_id');
        if ($idParam) {
            $parsed = $this->parseLocationId($idParam);
            if ($parsed) {
                $query->where("{$table}.id", '=', $parsed[1]);
            }
        }
    }

    /**
     * Parse a FHIR Location resource ID.
     * Format: loc-hosp-{id}, loc-lab-{id}, loc-pharm-{id}, loc-img-{id}
     */
    protected function parseLocationId(string $id): ?array
    {
        if (preg_match('/^loc-(hosp|lab|pharm|img)-(\d+)$/', $id, $matches)) {
            $registry = match ($matches[1]) {
                'lab' => 'laboratory',
                'pharm' => 'pharmacy',
                'img' => 'imaging',
                default => 'hospital',
            };
            return [$registry, (int) $matches[2]];
        }

        // Fallback: plain numeric → hospital
        if (is_numeric($id)) {
            return ['hospital', (int) $id];
        }

        return null;
    }
}
