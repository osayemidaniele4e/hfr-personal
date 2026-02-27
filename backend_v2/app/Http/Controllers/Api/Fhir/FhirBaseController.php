<?php

namespace App\Http\Controllers\Api\Fhir;

use App\Fhir\Search\FhirSearchParser;
use App\Fhir\Transformers\BundleTransformer;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Base Controller for FHIR R4 Endpoints
 *
 * Provides FHIR-specific response formatting methods.
 * All FHIR controllers extend this instead of BaseApiController
 * to produce compliant FHIR JSON (not the HFR v1 envelope format).
 */
class FhirBaseController extends Controller
{
    protected BundleTransformer $bundleTransformer;

    public function __construct()
    {
        $this->bundleTransformer = new BundleTransformer();
    }

    /**
     * Return a single FHIR resource as JSON.
     */
    protected function fhirResponse(array $resource, int $status = 200): JsonResponse
    {
        return response()->json($resource, $status, [
            'Content-Type' => 'application/fhir+json; fhirVersion=4.0',
        ]);
    }

    /**
     * Return a FHIR searchset Bundle built from a paginator.
     *
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator
     * @param array $resources     Transformed FHIR resources for the current page
     * @param Request $request     Current HTTP request (for building pagination URLs)
     * @param string $resourceType FHIR resource type name (for URL construction)
     */
    protected function fhirSearchBundle($paginator, array $resources, Request $request, string $resourceType): JsonResponse
    {
        $baseUrl = url(config('hfr.fhir.base_url', '/api/fhir') . "/{$resourceType}");
        $searchParams = (new FhirSearchParser($request))->getSearchParams();

        $urls = $this->bundleTransformer->buildPaginationUrls($paginator, $baseUrl, $searchParams);

        $bundle = $this->bundleTransformer->buildSearchBundle(
            $resources,
            $paginator->total(),
            $urls['self'],
            $urls['next'],
            $urls['prev'],
            $urls['first'],
            $urls['last']
        );

        return $this->fhirResponse($bundle);
    }

    /**
     * Return a FHIR OperationOutcome error.
     *
     * @see https://hl7.org/fhir/R4/operationoutcome.html
     */
    protected function fhirError(int $httpStatus, string $severity, string $code, string $diagnostics): JsonResponse
    {
        return response()->json([
            'resourceType' => 'OperationOutcome',
            'issue' => [
                [
                    'severity' => $severity,
                    'code' => $code,
                    'diagnostics' => $diagnostics,
                ],
            ],
        ], $httpStatus, [
            'Content-Type' => 'application/fhir+json; fhirVersion=4.0',
        ]);
    }

    /**
     * Return a 404 FHIR OperationOutcome.
     */
    protected function fhirNotFound(string $resourceType, $id): JsonResponse
    {
        return $this->fhirError(
            404,
            'error',
            'not-found',
            "{$resourceType}/{$id} not found"
        );
    }

    /**
     * Return a 400 FHIR OperationOutcome for invalid parameters.
     */
    protected function fhirBadRequest(string $diagnostics): JsonResponse
    {
        return $this->fhirError(400, 'error', 'invalid', $diagnostics);
    }

    // ──────────────────────────────────────────────────────────
    // Shared Query Builders (reuse existing v1 query patterns)
    // ──────────────────────────────────────────────────────────

    /**
     * Base facility query with all lookup joins.
     * Matches the exact pattern from Api\V1\FacilityController.
     */
    protected function baseFacilityQuery(): \Illuminate\Database\Query\Builder
    {
        return \Illuminate\Support\Facades\DB::table('hs_hospitals_history')
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
    }

    /**
     * Base laboratory query with all lookup joins.
     */
    protected function baseLaboratoryQuery(): \Illuminate\Database\Query\Builder
    {
        return \Illuminate\Support\Facades\DB::table('lb_laboratories')
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
                'lst_accreditation_status.status as accreditation_status'
            );
    }

    /**
     * Base pharmacy query with all lookup joins.
     */
    protected function basePharmacyQuery(): \Illuminate\Database\Query\Builder
    {
        return \Illuminate\Support\Facades\DB::table('pharmacies')
            ->leftJoin('ou_states', 'pharmacies.state_id', '=', 'ou_states.id')
            ->leftJoin('ou_lgas', 'pharmacies.lga_id', '=', 'ou_lgas.id')
            ->leftJoin('ou_wards', 'pharmacies.ward_id', '=', 'ou_wards.id')
            ->leftJoin('lst_ownerships', 'pharmacies.ownership_id', '=', 'lst_ownerships.id')
            ->leftJoin('lst_oparational_status', 'pharmacies.operational_status_id', '=', 'lst_oparational_status.id')
            ->leftJoin('lst_registration_status', 'pharmacies.registration_status_id', '=', 'lst_registration_status.id')
            ->leftJoin('lst_license_status', 'pharmacies.license_status_id', '=', 'lst_license_status.id')
            ->select(
                'pharmacies.*',
                'ou_states.name as state_name',
                'ou_lgas.name as lga_name',
                'ou_wards.name as ward_name',
                'lst_ownerships.name as ownership_name',
                'lst_oparational_status.status as operational_status_name',
                'lst_registration_status.status as registration_status_name',
                'lst_license_status.status as license_status_name'
            );
    }

    /**
     * Base imaging query with all lookup joins.
     */
    protected function baseImagingQuery(): \Illuminate\Database\Query\Builder
    {
        return \Illuminate\Support\Facades\DB::table('im_imagings')
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
                'lst_oparational_status.status as operational_status_name',
                'lst_registration_status.status as registration_status_name',
                'lst_license_status.status as license_status_name'
            );
    }
}
