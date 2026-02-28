<?php

namespace App\Fhir\Transformers;

/**
 * FHIR R4 Location Transformer
 *
 * Converts an HFR facility row into a compliant FHIR Location resource.
 * Represents the physical site: GPS coordinates, address, operational
 * status, hours of operation, and a reference back to the managing Organization.
 *
 * @see https://hl7.org/fhir/R4/location.html
 */
class LocationTransformer extends BaseTransformer
{
    /**
     * Transform an HFR facility row into a FHIR Location resource.
     *
     * @param object $facility  Row from the facility query (with joined lookup columns)
     * @param string $sourceType  Source registry: 'hospital', 'laboratory', 'pharmacy', 'imaging'
     * @return array FHIR Location resource
     */
    public function transform(object $facility, string $sourceType = 'hospital'): array
    {
        $orgId = $this->buildOrganizationId($facility, $sourceType);
        $locId = $this->buildResourceId($facility, $sourceType);

        $resource = [
            'resourceType' => 'Location',
            'id' => $locId,
            'meta' => $this->buildMeta(
                'http://hl7.org/fhir/StructureDefinition/Location',
                $facility->updated_at ?? null
            ),
            'text' => $this->buildLocationNarrative($facility, $sourceType),
            'identifier' => $this->filterNullValues([
                $this->buildFacilityCodeIdentifier($facility->unique_id ?? $facility->state_unique_id ?? null),
                $this->buildRegistrationIdentifier($facility->registration_no ?? null),
            ]),
            'status' => $this->mapLocationStatus($facility->operational_status_id ?? null),
            'operationalStatus' => $this->buildOperationalStatusCoding($facility),
            'name' => $facility->facility_name,
            'description' => $this->buildDescription($facility, $sourceType),
            'mode' => 'instance',
            'type' => $this->buildLocationTypes($facility, $sourceType),
            'telecom' => $this->buildTelecom(
                $facility->phone_number ?? null,
                $facility->alternate_number ?? null,
                $facility->email_address ?? null,
                $facility->website ?? null
            ),
            'address' => $this->buildAddress(
                $facility->physical_location ?? null,
                $facility->postal_address ?? null,
                $facility->state_name ?? null,
                $facility->lga_name ?? null,
                $facility->ward_name ?? null
            ),
            'position' => $this->buildPosition($facility),
            'managingOrganization' => $this->buildReference(
                'Organization',
                $orgId,
                $facility->facility_name
            ),
        ];

        // Add alias
        if (!empty($facility->alt_facility_name)) {
            $resource['alias'] = [$facility->alt_facility_name];
        }

        // Add hours of operation (hospitals)
        if ($sourceType === 'hospital') {
            $hours = $this->buildHoursOfOperation($facility);
            if ($hours) {
                $resource['hoursOfOperation'] = $hours;
            }
        }

        // Remove null values at top level
        return array_filter($resource, fn ($v) => $v !== null && $v !== []);
    }

    /**
     * Build the Location resource ID (prefixed with loc-).
     */
    protected function buildResourceId(object $facility, string $sourceType): string
    {
        $prefix = match ($sourceType) {
            'laboratory' => 'loc-lab',
            'pharmacy' => 'loc-pharm',
            'imaging' => 'loc-img',
            default => 'loc-hosp',
        };

        return "{$prefix}-{$facility->id}";
    }

    /**
     * Build the corresponding Organization resource ID.
     */
    protected function buildOrganizationId(object $facility, string $sourceType): string
    {
        $prefix = match ($sourceType) {
            'laboratory' => 'lab',
            'pharmacy' => 'pharm',
            'imaging' => 'img',
            default => 'hosp',
        };

        return "{$prefix}-{$facility->id}";
    }

    /**
     * Build Location.operationalStatus coding (V2 table 0116).
     */
    protected function buildOperationalStatusCoding(object $facility): ?array
    {
        $statusId = $facility->operational_status_id ?? null;
        if ($statusId === null) {
            return null;
        }

        $code = match ($statusId) {
            1 => 'O',   // Occupied / Operational
            2 => 'U',   // Unoccupied / Temporarily closed
            default => 'C', // Closed
        };

        $display = match ($statusId) {
            1 => 'Occupied',
            2 => 'Unoccupied',
            default => 'Closed',
        };

        return [
            'system' => 'http://terminology.hl7.org/CodeSystem/v2-0116',
            'code' => $code,
            'display' => $display,
        ];
    }

    /**
     * Build a human-readable description for the Location.
     */
    protected function buildDescription(object $facility, string $sourceType): string
    {
        $typeLabel = match ($sourceType) {
            'laboratory' => 'Laboratory',
            'pharmacy' => 'Pharmacy',
            'imaging' => 'Imaging Center',
            default => 'Health Facility',
        };

        $parts = ["{$typeLabel}: {$facility->facility_name}"];

        if (!empty($facility->state_name)) {
            $parts[] = $facility->state_name;
        }
        if (!empty($facility->lga_name)) {
            $parts[] = "{$facility->lga_name} LGA";
        }

        return implode(', ', $parts);
    }

    /**
     * Build Location.type array (CodeableConcepts).
     * Uses valid v3-RoleCode values via the shared helper.
     */
    protected function buildLocationTypes(object $facility, string $sourceType): array
    {
        $types = [];

        // HL7 ServiceDeliveryLocationRoleType — use valid codes from shared helper
        $facilityType = $facility->facility_type_name ?? null;
        $roleCode = $this->mapFacilityToRoleCode($facilityType, $sourceType);

        $types[] = $this->buildDirectCodeableConcept(
            'http://terminology.hl7.org/CodeSystem/v3-RoleCode',
            $roleCode['code'],
            $roleCode['display']
        );

        return $types;
    }

    /**
     * Build Location.position from latitude/longitude.
     */
    protected function buildPosition(object $facility): ?array
    {
        $lat = $facility->latitude ?? null;
        $lng = $facility->longitude ?? null;

        if (empty($lat) || empty($lng)) {
            return null;
        }

        return [
            'longitude' => (float) $lng,
            'latitude' => (float) $lat,
        ];
    }

    /**
     * Build Location.hoursOfOperation from HFR operational_days / operational_hours.
     */
    protected function buildHoursOfOperation(object $facility): ?array
    {
        $days = $facility->operational_days ?? null;
        $hours = $facility->operational_hours ?? null;

        if (empty($days) && empty($hours)) {
            return null;
        }

        $entry = [];

        // Parse comma-separated days (e.g. "Monday,Tuesday,Wednesday,Thursday,Friday")
        if (!empty($days)) {
            $dayMap = [
                'monday' => 'mon', 'tuesday' => 'tue', 'wednesday' => 'wed',
                'thursday' => 'thu', 'friday' => 'fri', 'saturday' => 'sat',
                'sunday' => 'sun',
            ];

            $fhirDays = [];
            foreach (explode(',', $days) as $day) {
                $normalized = strtolower(trim($day));
                if (isset($dayMap[$normalized])) {
                    $fhirDays[] = $dayMap[$normalized];
                }
            }

            if (!empty($fhirDays)) {
                $entry['daysOfWeek'] = $fhirDays;
            }
        }

        $entry['allDay'] = strtolower(trim($hours ?? '')) === '24 hours';

        return [$entry];
    }

    /**
     * Build human-readable narrative for Location (dom-6 compliance).
     */
    protected function buildLocationNarrative(object $facility, string $sourceType): array
    {
        $name = htmlspecialchars($facility->facility_name ?? 'Unknown', ENT_QUOTES);
        $state = htmlspecialchars($facility->state_name ?? '', ENT_QUOTES);
        $lga = htmlspecialchars($facility->lga_name ?? '', ENT_QUOTES);
        $status = $this->mapLocationStatus($facility->operational_status_id ?? null);

        $html = "<p><b>{$name}</b></p>";
        if ($state) {
            $html .= "<p>Location: {$lga}, {$state}, Nigeria</p>";
        }
        $html .= "<p>Status: {$status}</p>";

        $lat = $facility->latitude ?? null;
        $lng = $facility->longitude ?? null;
        if ($lat && $lng) {
            $html .= "<p>Coordinates: {$lat}, {$lng}</p>";
        }

        return $this->buildNarrative($html);
    }
}
