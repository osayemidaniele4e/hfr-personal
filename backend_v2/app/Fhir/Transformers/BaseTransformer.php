<?php

namespace App\Fhir\Transformers;

use App\Models\FhirCodeMapping;

/**
 * Base transformer providing shared FHIR R4 building blocks.
 *
 * All FHIR resource transformers extend this class and use its
 * helper methods to construct compliant FHIR JSON structures.
 */
abstract class BaseTransformer
{
    /**
     * HFR FHIR base identifier system URI.
     */
    protected string $baseSystem;

    public function __construct()
    {
        $this->baseSystem = config('hfr.fhir.base_identifier_system', 'https://hfr.health.gov.ng/fhir');
    }

    // ──────────────────────────────────────────────────────────
    // Identifier Helpers
    // ──────────────────────────────────────────────────────────

    /**
     * Build a FHIR Identifier element.
     */
    protected function buildIdentifier(string $system, ?string $value, string $use = 'official', ?array $type = null): ?array
    {
        if (empty($value)) {
            return null;
        }

        $identifier = [
            'use' => $use,
            'system' => $system,
            'value' => $value,
        ];

        if ($type) {
            $identifier['type'] = $type;
        }

        return $identifier;
    }

    /**
     * Build the standard HFR facility code identifier.
     * Uses XX (Organization Identifier) which is in the FHIR Identifier Type Codes value set.
     */
    protected function buildFacilityCodeIdentifier(?string $uniqueId): ?array
    {
        return $this->buildIdentifier(
            "{$this->baseSystem}/identifier/facility-code",
            $uniqueId,
            'official',
            [
                'coding' => [[
                    'system' => 'http://terminology.hl7.org/CodeSystem/v2-0203',
                    'code' => 'PRN',
                    'display' => 'Provider Number',
                ]],
                'text' => 'HFR Facility Code',
            ]
        );
    }

    /**
     * Build an HFR registration number identifier.
     */
    protected function buildRegistrationIdentifier(?string $registrationNo): ?array
    {
        return $this->buildIdentifier(
            "{$this->baseSystem}/identifier/registration-number",
            $registrationNo,
            'official',
            [
                'coding' => [[
                    'system' => 'http://terminology.hl7.org/CodeSystem/v2-0203',
                    'code' => 'PRN',
                    'display' => 'Provider number',
                ]],
                'text' => 'Registration Number',
            ]
        );
    }

    // ──────────────────────────────────────────────────────────
    // CodeableConcept Helpers
    // ──────────────────────────────────────────────────────────

    /**
     * Build a FHIR CodeableConcept from HFR code mapping.
     * Falls back to a text-only concept if the mapping table is unavailable.
     */
    protected function buildCodeableConcept(string $hfrTable, ?int $hfrId, ?string $fallbackDisplay = null): ?array
    {
        try {
            return FhirCodeMapping::resolveCodeableConcept($hfrTable, $hfrId, $fallbackDisplay);
        } catch (\Exception $e) {
            // If fhir_code_mappings table doesn't exist, return fallback
            if ($fallbackDisplay) {
                return ['text' => $fallbackDisplay];
            }
            return null;
        }
    }

    // ──────────────────────────────────────────────────────────
    // Narrative (text) Helpers
    // ──────────────────────────────────────────────────────────

    /**
     * Build FHIR Resource.text (Narrative) element.
     * Satisfies dom-6 constraint: "A resource should have narrative for robust management".
     *
     * @param string $divContent  HTML content (without wrapping <div>)
     * @return array FHIR Narrative { status, div }
     */
    protected function buildNarrative(string $divContent): array
    {
        return [
            'status' => 'generated',
            'div' => '<div xmlns="http://www.w3.org/1999/xhtml">' . $divContent . '</div>',
        ];
    }

    /**
     * Build a CodeableConcept with a custom system/code directly (no lookup).
     */
    protected function buildDirectCodeableConcept(string $system, string $code, string $display): array
    {
        return [
            'coding' => [[
                'system' => $system,
                'code' => $code,
                'display' => $display,
            ]],
            'text' => $display,
        ];
    }

    // ──────────────────────────────────────────────────────────
    // Telecom / ContactPoint Helpers
    // ──────────────────────────────────────────────────────────

    /**
     * Build FHIR telecom (ContactPoint) array from facility contact fields.
     */
    protected function buildTelecom(?string $phone, ?string $altPhone, ?string $email, ?string $website): array
    {
        $telecom = [];

        if (!empty($phone)) {
            $telecom[] = [
                'system' => 'phone',
                'value' => $phone,
                'use' => 'work',
                'rank' => 1,
            ];
        }

        if (!empty($altPhone)) {
            $telecom[] = [
                'system' => 'phone',
                'value' => $altPhone,
                'use' => 'work',
                'rank' => 2,
            ];
        }

        if (!empty($email)) {
            $telecom[] = [
                'system' => 'email',
                'value' => $email,
                'use' => 'work',
            ];
        }

        if (!empty($website)) {
            $telecom[] = [
                'system' => 'url',
                'value' => $website,
                'use' => 'work',
            ];
        }

        return $telecom;
    }

    // ──────────────────────────────────────────────────────────
    // Reference Helpers
    // ──────────────────────────────────────────────────────────

    /**
     * Build a FHIR Reference element.
     */
    protected function buildReference(string $resourceType, $id, ?string $display = null): array
    {
        $ref = [
            'reference' => "{$resourceType}/{$id}",
        ];

        if ($display) {
            $ref['display'] = $display;
        }

        return $ref;
    }

    // ──────────────────────────────────────────────────────────
    // Meta Helpers
    // ──────────────────────────────────────────────────────────

    /**
     * Build the FHIR Resource.meta element.
     */
    protected function buildMeta(string $profile, ?string $lastUpdated = null, ?string $versionId = null): array
    {
        $meta = [
            'profile' => [$profile],
        ];

        if ($lastUpdated) {
            $meta['lastUpdated'] = $this->formatDateTime($lastUpdated);
        }

        if ($versionId) {
            $meta['versionId'] = (string) $versionId;
        }

        return $meta;
    }

    // ──────────────────────────────────────────────────────────
    // Address Helpers
    // ──────────────────────────────────────────────────────────

    /**
     * Build a FHIR Address element from HFR location fields.
     */
    protected function buildAddress(
        ?string $physicalLocation,
        ?string $postalAddress,
        ?string $stateName,
        ?string $lgaName,
        ?string $wardName
    ): ?array {
        if (!$physicalLocation && !$stateName && !$lgaName) {
            return null;
        }

        $address = [
            'use' => 'work',
            'type' => 'physical',
            'country' => 'NG',
        ];

        if ($physicalLocation) {
            $address['text'] = $physicalLocation;
        }

        if ($stateName) {
            $address['state'] = $stateName;
        }

        if ($lgaName) {
            $address['district'] = $lgaName;
        }

        if ($wardName) {
            $address['city'] = $wardName;
        }

        if ($postalAddress) {
            $address['line'] = [$postalAddress];
        }

        return $address;
    }

    // ──────────────────────────────────────────────────────────
    // Extension Helpers
    // ──────────────────────────────────────────────────────────

    /**
     * Build a FHIR Extension element.
     */
    protected function buildExtension(string $url, string $valueType, $value): ?array
    {
        if ($value === null) {
            return null;
        }

        return [
            'url' => $url,
            $valueType => $value,
        ];
    }

    /**
     * Build a complex (nested) extension.
     */
    protected function buildComplexExtension(string $url, array $subExtensions): ?array
    {
        $filtered = array_filter($subExtensions, fn ($ext) => $ext !== null);

        if (empty($filtered)) {
            return null;
        }

        return [
            'url' => $url,
            'extension' => array_values($filtered),
        ];
    }

    // ──────────────────────────────────────────────────────────
    // Date / Utility Helpers
    // ──────────────────────────────────────────────────────────

    /**
     * Format a date/datetime to FHIR instant format (ISO 8601).
     */
    protected function formatDateTime(?string $dateTime): ?string
    {
        if (empty($dateTime)) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($dateTime)->toIso8601String();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Format a date to FHIR date format (YYYY-MM-DD).
     */
    protected function formatDate(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Map an HFR operational status ID to FHIR active boolean.
     * Used for Organization.active and HealthcareService.active.
     */
    protected function mapActive(?int $operationalStatusId): bool
    {
        // Operational status 1 = "Operational" → active = true
        // Null also treated as active (many records don't have status set)
        return $operationalStatusId === 1 || $operationalStatusId === null;
    }

    /**
     * Map HFR facility type / source to valid HL7 v3-RoleCode.
     * Only returns codes that actually exist in the CodeSystem.
     *
     * @see https://terminology.hl7.org/CodeSystem-v3-RoleCode.html
     */
    protected function mapFacilityToRoleCode(?string $facilityType, string $sourceType = 'hospital'): array
    {
        // Default by registry type — all valid v3-RoleCode values
        $defaults = [
            'hospital'   => ['code' => 'HOSP', 'display' => 'Hospital'],
            'pharmacy'   => ['code' => 'PHARM', 'display' => 'Pharmacy'],
            'laboratory' => ['code' => 'MBL', 'display' => 'Medical Laboratory'],
            'imaging'    => ['code' => 'RADDX', 'display' => 'Radiology Diagnostics or Therapeutics Unit'],
        ];

        if (!$facilityType) {
            return $defaults[$sourceType] ?? $defaults['hospital'];
        }

        $lower = strtolower(trim($facilityType));

        // Map to VALID v3-RoleCode values (NOT 'HOSPIT' which doesn't exist)
        $map = [
            'hospital'               => ['code' => 'HOSP', 'display' => 'Hospital'],
            'general hospital'       => ['code' => 'HOSP', 'display' => 'Hospital'],
            'specialist hospital'    => ['code' => 'HOSP', 'display' => 'Hospital'],
            'teaching hospital'      => ['code' => 'HOSP', 'display' => 'Hospital'],
            'federal medical centre' => ['code' => 'HOSP', 'display' => 'Hospital'],
            'clinic'                 => ['code' => 'PC', 'display' => 'Primary Care Clinic'],
            'primary health'         => ['code' => 'PC', 'display' => 'Primary Care Clinic'],
            'health centre'          => ['code' => 'PC', 'display' => 'Primary Care Clinic'],
            'health post'            => ['code' => 'PC', 'display' => 'Primary Care Clinic'],
            'dispensary'             => ['code' => 'PC', 'display' => 'Primary Care Clinic'],
            'maternity'              => ['code' => 'HOSP', 'display' => 'Hospital'],
            'pharmacy'               => ['code' => 'PHARM', 'display' => 'Pharmacy'],
            'laboratory'             => ['code' => 'MBL', 'display' => 'Medical Laboratory'],
            'imaging'                => ['code' => 'RADDX', 'display' => 'Radiology Diagnostics or Therapeutics Unit'],
            'radiology'              => ['code' => 'RADDX', 'display' => 'Radiology Diagnostics or Therapeutics Unit'],
            'dental'                 => ['code' => 'DENT', 'display' => 'Dental Clinic'],
            'nursing home'           => ['code' => 'NCCF', 'display' => 'Nursing or Custodial Care Facility'],
            'psychiatric'            => ['code' => 'PHU', 'display' => 'Psychiatric Hospital Unit'],
            'rehabilitation'         => ['code' => 'RH', 'display' => 'Rehabilitation Hospital'],
            'eye'                    => ['code' => 'OPTC', 'display' => 'Optometry Clinic'],
        ];

        foreach ($map as $pattern => $code) {
            if (str_contains($lower, $pattern)) {
                return $code;
            }
        }

        return $defaults[$sourceType] ?? $defaults['hospital'];
    }

    /**
     * Map HFR ownership to VALID organization-type code with CORRECT display name.
     * Fixes validator error: Wrong Display Name for bus.
     *
     * @see https://terminology.hl7.org/CodeSystem-organization-type.html
     */
    protected function mapOwnershipToOrganizationType(?string $ownership): array
    {
        if (!$ownership) {
            return ['code' => 'other', 'display' => 'Other'];
        }

        $lower = strtolower(trim($ownership));

        if (str_contains($lower, 'government') || str_contains($lower, 'public') || str_contains($lower, 'federal') || str_contains($lower, 'state') || str_contains($lower, 'local')) {
            return ['code' => 'govt', 'display' => 'Government'];
        }

        if (str_contains($lower, 'faith') || str_contains($lower, 'religious') || str_contains($lower, 'mission') || str_contains($lower, 'church')) {
            return ['code' => 'reli', 'display' => 'Religious Institution'];
        }

        if (str_contains($lower, 'ngo') || str_contains($lower, 'non-governmental') || str_contains($lower, 'community')) {
            return ['code' => 'cg', 'display' => 'Community Group'];
        }

        if (str_contains($lower, 'private')) {
            // EXACT display name from the spec (was incorrectly 'Non-Healthcare Business')
            return ['code' => 'bus', 'display' => 'Non-Healthcare Business or Corporation'];
        }

        return ['code' => 'other', 'display' => 'Other'];
    }

    /**
     * Map an HFR operational status ID to FHIR Location.status.
     * FHIR Location.status: active | suspended | inactive
     */
    protected function mapLocationStatus(?int $operationalStatusId): string
    {
        return match ($operationalStatusId) {
            1 => 'active',          // Operational
            2 => 'suspended',       // Temporarily closed
            default => 'inactive',  // Closed / Unknown
        };
    }

    /**
     * Remove null values from an array recursively.
     */
    protected function filterNulls(array $array): array
    {
        return array_filter($array, fn ($value) => $value !== null && $value !== [] && $value !== '');
    }

    /**
     * Remove null entries from an indexed array and re-index.
     */
    protected function filterNullValues(array $array): array
    {
        return array_values(array_filter($array, fn ($v) => $v !== null));
    }
}
