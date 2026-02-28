<?php

namespace App\Fhir\Transformers;

/**
 * FHIR R4 CapabilityStatement Builder
 *
 * Generates the CapabilityStatement resource returned by the /metadata endpoint.
 * Declares the server's supported FHIR resources, interactions, and search parameters.
 *
 * @see https://hl7.org/fhir/R4/capabilitystatement.html
 */
class CapabilityStatementBuilder
{
    /**
     * Build the full CapabilityStatement resource.
     */
    public function build(): array
    {
        $fhirConfig = config('hfr.fhir');

        return [
            'resourceType' => 'CapabilityStatement',
            'id' => 'hfr-nigeria',
            'url' => url($fhirConfig['base_url'] . '/metadata'),
            'version' => '1.0.0',
            'name' => 'HFR_Nigeria_FHIR_Server',
            'title' => 'Nigeria Health Facility Registry FHIR Server',
            'status' => 'active',
            'experimental' => false,
            'date' => now()->format('Y-m-d'),
            'publisher' => $fhirConfig['publisher'] ?? 'Federal Ministry of Health, Nigeria',
            'contact' => [
                [
                    'name' => 'HFR Support',
                    'telecom' => [
                        [
                            'system' => 'url',
                            'value' => 'https://hfr.health.gov.ng',
                        ],
                    ],
                ],
            ],
            'description' => 'FHIR R4 read-only API for the Nigeria Health Facility Registry (HFR). '
                . 'Exposes health facilities (hospitals, laboratories, pharmacies, imaging centers) '
                . 'as FHIR Organization, Location, and HealthcareService resources.',
            'kind' => 'instance',
            'software' => [
                'name' => 'HFR FHIR API',
                'version' => '1.0.0',
            ],
            'implementation' => [
                'description' => 'Nigeria Health Facility Registry FHIR R4 Server',
                'url' => url($fhirConfig['base_url']),
            ],
            'fhirVersion' => $fhirConfig['version'] ?? '4.0.1',
            'format' => ['json'],
            'implementationGuide' => [
                $fhirConfig['ig_url'] ?? 'https://hfr.health.gov.ng/fhir/ImplementationGuide/hfr-ng',
            ],
            'rest' => [
                [
                    'mode' => 'server',
                    'documentation' => 'Read-only FHIR R4 server exposing Nigerian health facility data. '
                        . 'Supports search and read interactions for Organization, Location, and HealthcareService resources.',
                    'security' => [
                        'cors' => true,
                        'service' => [
                            [
                                'coding' => [[
                                    'system' => 'http://terminology.hl7.org/CodeSystem/restful-security-service',
                                    'code' => 'Certificates',
                                    'display' => 'Certificates',
                                ]],
                                'text' => 'API Key authentication via X-API-Key header',
                            ],
                        ],
                        'description' => 'All endpoints (except /metadata) require a valid API key '
                            . 'passed in the X-API-Key HTTP header. Request an API key at '
                            . url('/api/v1/request-key'),
                    ],
                    'resource' => [
                        $this->buildOrganizationCapability(),
                        $this->buildLocationCapability(),
                        $this->buildHealthcareServiceCapability(),
                    ],
                ],
            ],
        ];
    }

    /**
     * Organization resource capability.
     */
    protected function buildOrganizationCapability(): array
    {
        return [
            'type' => 'Organization',
            'profile' => 'http://hl7.org/fhir/StructureDefinition/Organization',
            'documentation' => 'Health facilities from all four HFR registries (hospitals, laboratories, '
                . 'pharmacies, imaging centers) mapped as Organization resources. Use the "type" search '
                . 'parameter with registry-type codes (hospital, laboratory, pharmacy, imaging) to filter.',
            'interaction' => [
                ['code' => 'read'],
                ['code' => 'search-type'],
            ],
            'versioning' => 'no-version',
            'readHistory' => false,
            'updateCreate' => false,
            'conditionalCreate' => false,
            'conditionalRead' => 'not-supported',
            'conditionalUpdate' => false,
            'conditionalDelete' => 'not-supported',
            'searchParam' => [
                ['name' => '_id', 'type' => 'token', 'documentation' => 'Resource ID (e.g. hosp-123, lab-45)'],
                ['name' => 'name', 'type' => 'string', 'documentation' => 'Facility name (partial match)'],
                ['name' => 'name:exact', 'type' => 'string', 'documentation' => 'Facility name (exact match)'],
                ['name' => 'name:contains', 'type' => 'string', 'documentation' => 'Facility name (contains)'],
                ['name' => 'identifier', 'type' => 'token', 'documentation' => 'Facility code or registration number (system|value or value only)'],
                ['name' => 'type', 'type' => 'token', 'documentation' => 'Organization type code (facility type, ownership, level of care, or registry-type)'],
                ['name' => 'address-state', 'type' => 'string', 'documentation' => 'State name'],
                ['name' => 'active', 'type' => 'token', 'documentation' => 'Active status (true/false)'],
                ['name' => '_count', 'type' => 'number', 'documentation' => 'Number of results per page (default 20, max 100)'],
                ['name' => '_offset', 'type' => 'number', 'documentation' => 'Offset for pagination (default 0)'],
                ['name' => '_sort', 'type' => 'string', 'documentation' => 'Sort field: name, -name, _lastUpdated, -_lastUpdated'],
            ],
        ];
    }

    /**
     * Location resource capability.
     */
    protected function buildLocationCapability(): array
    {
        return [
            'type' => 'Location',
            'profile' => 'http://hl7.org/fhir/StructureDefinition/Location',
            'documentation' => 'Physical locations of health facilities with GPS coordinates, addresses, '
                . 'and operational status. Each Location references its managing Organization.',
            'interaction' => [
                ['code' => 'read'],
                ['code' => 'search-type'],
            ],
            'versioning' => 'no-version',
            'readHistory' => false,
            'updateCreate' => false,
            'conditionalCreate' => false,
            'conditionalRead' => 'not-supported',
            'conditionalUpdate' => false,
            'conditionalDelete' => 'not-supported',
            'searchParam' => [
                ['name' => '_id', 'type' => 'token', 'documentation' => 'Resource ID (e.g. loc-hosp-123)'],
                ['name' => 'name', 'type' => 'string', 'documentation' => 'Location name (partial match)'],
                ['name' => 'name:exact', 'type' => 'string', 'documentation' => 'Location name (exact match)'],
                ['name' => 'name:contains', 'type' => 'string', 'documentation' => 'Location name (contains)'],
                ['name' => 'identifier', 'type' => 'token', 'documentation' => 'Facility code or registration number'],
                ['name' => 'status', 'type' => 'token', 'documentation' => 'Location status (active, suspended, inactive)'],
                ['name' => 'type', 'type' => 'token', 'documentation' => 'Location type code (HOSP, MBL, PHARM, RADDX)'],
                ['name' => 'address', 'type' => 'string', 'documentation' => 'Free text address search'],
                ['name' => 'address-state', 'type' => 'string', 'documentation' => 'State name'],
                ['name' => 'organization', 'type' => 'reference', 'documentation' => 'Managing organization reference'],
                ['name' => 'near', 'type' => 'special', 'documentation' => 'Geo-search: latitude|longitude|distance|units (e.g. 6.5|3.4|10|km)'],
                ['name' => '_count', 'type' => 'number', 'documentation' => 'Results per page (default 20, max 100)'],
                ['name' => '_offset', 'type' => 'number', 'documentation' => 'Pagination offset'],
                ['name' => '_sort', 'type' => 'string', 'documentation' => 'Sort field: name, -name, _lastUpdated, -_lastUpdated'],
            ],
        ];
    }

    /**
     * HealthcareService resource capability.
     */
    protected function buildHealthcareServiceCapability(): array
    {
        return [
            'type' => 'HealthcareService',
            'profile' => 'http://hl7.org/fhir/StructureDefinition/HealthcareService',
            'documentation' => 'Healthcare services offered by facilities. Currently available for '
                . 'hospital-type facilities via the hs_hospital_services registry.',
            'interaction' => [
                ['code' => 'read'],
                ['code' => 'search-type'],
            ],
            'versioning' => 'no-version',
            'readHistory' => false,
            'updateCreate' => false,
            'conditionalCreate' => false,
            'conditionalRead' => 'not-supported',
            'conditionalUpdate' => false,
            'conditionalDelete' => 'not-supported',
            'searchParam' => [
                ['name' => '_id', 'type' => 'token', 'documentation' => 'Service resource ID (e.g. svc-45)'],
                ['name' => 'organization', 'type' => 'reference', 'documentation' => 'Providing organization (Organization/{id})'],
                ['name' => 'location', 'type' => 'reference', 'documentation' => 'Service location (Location/{id})'],
                ['name' => 'service-category', 'type' => 'token', 'documentation' => 'Service category code or name'],
                ['name' => 'service-type', 'type' => 'token', 'documentation' => 'Service type code or name'],
                ['name' => 'name', 'type' => 'string', 'documentation' => 'Service name (partial match)'],
                ['name' => 'active', 'type' => 'token', 'documentation' => 'Active status (true/false)'],
                ['name' => '_count', 'type' => 'number', 'documentation' => 'Results per page (default 20, max 100)'],
                ['name' => '_offset', 'type' => 'number', 'documentation' => 'Pagination offset'],
            ],
        ];
    }
}
