<?php

namespace App\Http\Controllers\Api\Fhir;

use App\Fhir\Search\FhirSearchParser;
use App\Fhir\Transformers\OrganizationTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group FHIR - Organization
 *
 * FHIR R4 Organization endpoints. Each health facility (hospital, laboratory,
 * pharmacy, imaging center) is represented as an Organization resource.
 */
class OrganizationController extends FhirBaseController
{
    protected OrganizationTransformer $transformer;

    public function __construct()
    {
        parent::__construct();
        $this->transformer = new OrganizationTransformer();
    }

    /**
     * Search Organizations
     *
     * Search across all facility registries. Use the `type` parameter with
     * registry-type codes (hospital, laboratory, pharmacy, imaging) to filter.
     *
     * @queryParam name string Facility name (partial match). Example: General Hospital
     * @queryParam name:exact string Facility name (exact match). Example: General Hospital Lagos
     * @queryParam name:contains string Facility name (contains). Example: General
     * @queryParam identifier string Facility code or registration number. Example: RC/00001
     * @queryParam type string Organization type code (registry-type: hospital|laboratory|pharmacy|imaging). Example: hospital
     * @queryParam address-state string State name. Example: Lagos
     * @queryParam active string Active status. Example: true
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
        $registryType = $request->input('type');
        $registries = $this->resolveRegistries($registryType);

        // Collect results using SQL-level pagination across registries
        $allResources = [];
        $totalCount = 0;
        $offset = ($page - 1) * $perPage;
        $skipRemaining = $offset;
        $needed = $perPage;

        foreach ($registries as $registry) {
            $query = $this->buildRegistryQuery($registry);
            $this->applyCommonSearchParams($query, $search, $registry);

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

            // Apply sorting
            $sortFields = $this->getSortFields($registry);
            $sorts = $search->parseSortParams($sortFields);
            foreach ($sorts as [$column, $direction]) {
                $query->orderBy($column, $direction);
            }
            if (empty($sorts)) {
                $query->orderBy($this->getTableName($registry) . '.facility_name', 'asc');
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

        // Build pagination manually
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedResources,
            $totalCount,
            $perPage,
            $page,
            ['path' => url(config('hfr.fhir.base_url') . '/Organization')]
        );

        return $this->fhirSearchBundle($paginator, $pagedResources, $request, 'Organization');
    }

    /**
     * Read Organization
     *
     * Retrieve a single Organization by its FHIR resource ID.
     * IDs are prefixed by registry type: hosp-{id}, lab-{id}, pharm-{id}, img-{id}.
     *
     * @urlParam id string required The Organization resource ID. Example: hosp-123
     */
    public function read(string $id): JsonResponse
    {
        $parsed = $this->parseResourceId($id);

        if (!$parsed) {
            return $this->fhirNotFound('Organization', $id);
        }

        [$registry, $numericId] = $parsed;

        $query = $this->buildRegistryQuery($registry);
        $facility = $query->where($this->getTableName($registry) . '.id', $numericId)->first();

        if (!$facility) {
            return $this->fhirNotFound('Organization', $id);
        }

        return $this->fhirResponse($this->transformer->transform($facility, $registry));
    }

    // ──────────────────────────────────────────────────────────
    // Private Helpers
    // ──────────────────────────────────────────────────────────

    /**
     * Resolve which registries to query based on the type parameter.
     */
    protected function resolveRegistries(?string $typeParam): array
    {
        if ($typeParam === null) {
            return ['hospital', 'laboratory', 'pharmacy', 'imaging'];
        }

        $token = (new FhirSearchParser(request()))->parseToken($typeParam);
        $code = strtolower($token['code']);

        return match ($code) {
            'hospital', 'hosp' => ['hospital'],
            'laboratory', 'lab', 'mbl' => ['laboratory'],
            'pharmacy', 'pharm' => ['pharmacy'],
            'imaging', 'img', 'raddx' => ['imaging'],
            default => ['hospital', 'laboratory', 'pharmacy', 'imaging'],
        };
    }

    /**
     * Build the base query for a given registry type.
     */
    protected function buildRegistryQuery(string $registry): \Illuminate\Database\Query\Builder
    {
        return match ($registry) {
            'laboratory' => $this->baseLaboratoryQuery(),
            'pharmacy' => $this->basePharmacyQuery(),
            'imaging' => $this->baseImagingQuery(),
            default => $this->baseFacilityQuery(),
        };
    }

    /**
     * Get the primary table name for a registry.
     */
    protected function getTableName(string $registry): string
    {
        return match ($registry) {
            'laboratory' => 'lb_laboratories',
            'pharmacy' => 'pharmacies',
            'imaging' => 'im_imagings',
            default => 'hs_hospitals_history',
        };
    }

    /**
     * Apply FHIR search parameters common to all registries.
     */
    protected function applyCommonSearchParams($query, FhirSearchParser $search, string $registry): void
    {
        $table = $this->getTableName($registry);

        // name search
        $search->applyStringSearch($query, 'name', "{$table}.facility_name");

        // identifier search
        $search->applyIdentifierSearch($query, 'identifier', [
            "{$table}.unique_id",
            "{$table}.state_unique_id",
            "{$table}.registration_no",
        ]);

        // address-state
        $search->applyAddressStateSearch($query, 'ou_states.name', "{$table}.state_id");

        // active (maps to operational_status_id)
        $search->applyBooleanSearch($query, 'active', "{$table}.operational_status_id", 1);

        // _id (direct resource ID)
        $idParam = request()->input('_id');
        if ($idParam) {
            $parsed = $this->parseResourceId($idParam);
            if ($parsed) {
                [$_, $numericId] = $parsed;
                $query->where("{$table}.id", '=', $numericId);
            }
        }
    }

    /**
     * Get sort field mapping for a registry.
     */
    protected function getSortFields(string $registry): array
    {
        $table = $this->getTableName($registry);
        return [
            'name' => "{$table}.facility_name",
            '_lastUpdated' => "{$table}.updated_at",
        ];
    }

    /**
     * Parse a FHIR Organization resource ID into [registry, numericId].
     *
     * @return array|null [registry, numericId] or null if invalid
     */
    protected function parseResourceId(string $id): ?array
    {
        if (preg_match('/^(hosp|lab|pharm|img)-(\d+)$/', $id, $matches)) {
            $registry = match ($matches[1]) {
                'lab' => 'laboratory',
                'pharm' => 'pharmacy',
                'img' => 'imaging',
                default => 'hospital',
            };
            return [$registry, (int) $matches[2]];
        }

        // If it's just a numeric ID, default to hospital
        if (is_numeric($id)) {
            return ['hospital', (int) $id];
        }

        return null;
    }
}
