<?php

namespace App\Http\Controllers\Api\Fhir;

use App\Fhir\Transformers\CapabilityStatementBuilder;
use Illuminate\Http\JsonResponse;

/**
 * @group FHIR - Metadata
 *
 * FHIR CapabilityStatement endpoint (no authentication required).
 */
class MetadataController extends FhirBaseController
{
    /**
     * Get CapabilityStatement
     *
     * Returns the server's CapabilityStatement describing supported
     * FHIR resources, interactions, and search parameters.
     *
     * @response 200 scenario="Success" {
     *   "resourceType": "CapabilityStatement",
     *   "fhirVersion": "4.0.1",
     *   "format": ["json"]
     * }
     */
    public function index(): JsonResponse
    {
        $builder = new CapabilityStatementBuilder();
        return $this->fhirResponse($builder->build());
    }
}
