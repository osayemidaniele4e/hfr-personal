<?php

namespace App\Fhir\Transformers;

/**
 * FHIR R4 Organization Transformer
 *
 * Converts an HFR facility row (from hs_hospitals_history, lb_laboratories,
 * pharmacies, or im_imagings with their joined lookup columns) into a
 * compliant FHIR Organization resource.
 *
 * @see https://hl7.org/fhir/R4/organization.html
 */
class OrganizationTransformer extends BaseTransformer
{
    /**
     * Transform an HFR facility row into a FHIR Organization resource.
     *
     * @param object $facility  Row from the facility query (with joined lookup columns)
     * @param string $sourceType  Source registry: 'hospital', 'laboratory', 'pharmacy', 'imaging'
     * @return array FHIR Organization resource
     */
    public function transform(object $facility, string $sourceType = 'hospital'): array
    {
        $resource = [
            'resourceType' => 'Organization',
            'id' => $this->buildResourceId($facility, $sourceType),
            'meta' => $this->buildMeta(
                'http://hl7.org/fhir/StructureDefinition/Organization',
                $facility->updated_at ?? null
            ),
            'text' => $this->buildOrganizationNarrative($facility, $sourceType),
            'identifier' => $this->filterNullValues([
                $this->buildFacilityCodeIdentifier($facility->unique_id ?? $facility->state_unique_id ?? null),
                $this->buildRegistrationIdentifier($facility->registration_no ?? null),
                $this->buildSourceRegistryIdentifier($facility, $sourceType),
            ]),
            'active' => $this->mapActive($facility->operational_status_id ?? null),
            'type' => $this->buildOrganizationTypes($facility, $sourceType),
            'name' => $facility->facility_name,
        ];

        // FHIR R4: Organization does NOT have top-level telecom/address.
        // Use Organization.contact[] instead (validated by FHIR spec).
        $contact = $this->buildOrganizationContact($facility);
        if (!empty($contact)) {
            $resource['contact'] = $contact;
        }

        // Add alias if alternate name exists
        if (!empty($facility->alt_facility_name)) {
            $resource['alias'] = [$facility->alt_facility_name];
        }

        return $resource;
    }

    /**
     * Build the resource ID combining source type and database ID.
     */
    protected function buildResourceId(object $facility, string $sourceType): string
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
     * Build source-registry-specific identifier (e.g. pharmacist reg number).
     */
    protected function buildSourceRegistryIdentifier(object $facility, string $sourceType): ?array
    {
        return match ($sourceType) {
            'laboratory' => $this->buildIdentifier(
                "{$this->baseSystem}/identifier/medical-laboratory-number",
                $facility->medical_laboratory_number ?? null,
                'secondary'
            ),
            'pharmacy' => $this->buildIdentifier(
                "{$this->baseSystem}/identifier/pharmacist-registration-number",
                $facility->pharmacists_reg_number ?? null,
                'secondary'
            ),
            'imaging' => $this->buildIdentifier(
                "{$this->baseSystem}/identifier/radiographer-registration-number",
                $facility->radiographers_reg_number ?? null,
                'secondary'
            ),
            default => null,
        };
    }

    /**
     * Build Organization.type array (CodeableConcepts).
     *
     * Uses valid HL7 codes only:
     * - v3-RoleCode for facility types (HOSP, MBL, PHARM, RADDX, PC, etc.)
     * - organization-type for ownership (govt, bus, reli, etc.)
     * - Custom CodeSystem for registry-type and level-of-care (warnings expected)
     */
    protected function buildOrganizationTypes(object $facility, string $sourceType): array
    {
        $types = [];

        // Registry type discriminator (custom CodeSystem — validator warning expected)
        $registryType = match ($sourceType) {
            'laboratory' => $this->buildDirectCodeableConcept(
                "{$this->baseSystem}/CodeSystem/registry-type",
                'laboratory',
                'Laboratory'
            ),
            'pharmacy' => $this->buildDirectCodeableConcept(
                "{$this->baseSystem}/CodeSystem/registry-type",
                'pharmacy',
                'Pharmacy'
            ),
            'imaging' => $this->buildDirectCodeableConcept(
                "{$this->baseSystem}/CodeSystem/registry-type",
                'imaging',
                'Imaging Center'
            ),
            default => $this->buildDirectCodeableConcept(
                "{$this->baseSystem}/CodeSystem/registry-type",
                'hospital',
                'Hospital / Health Facility'
            ),
        };
        $types[] = $registryType;

        // Facility type → valid v3-RoleCode (fixes HOSPIT error)
        $facilityTypeName = $facility->facility_type_name ?? null;
        $roleCode = $this->mapFacilityToRoleCode($facilityTypeName, $sourceType);
        $types[] = $this->buildDirectCodeableConcept(
            'http://terminology.hl7.org/CodeSystem/v3-RoleCode',
            $roleCode['code'],
            $roleCode['display']
        );

        // Level of care (custom CodeSystem — validator warning expected)
        if (isset($facility->facility_level_id) && !empty($facility->facility_level_name)) {
            $types[] = $this->buildDirectCodeableConcept(
                "{$this->baseSystem}/CodeSystem/level-of-care",
                strtolower(str_replace(' ', '-', $facility->facility_level_name)),
                $facility->facility_level_name
            );
        }

        // Ownership → valid organization-type code with CORRECT display
        $ownershipName = $facility->ownership_name ?? null;
        if ($ownershipName) {
            $orgType = $this->mapOwnershipToOrganizationType($ownershipName);
            $types[] = $this->buildDirectCodeableConcept(
                'http://terminology.hl7.org/CodeSystem/organization-type',
                $orgType['code'],
                $orgType['display']
            );
        }

        return $types;
    }

    /**
     * Build Organization.contact[] — the FHIR R4 compliant way to put
     * telecom and address on an Organization resource.
     *
     * In FHIR R4, Organization does NOT have top-level telecom or address.
     * Those properties exist on Location. Organization uses contact[].
     */
    protected function buildOrganizationContact(object $facility): array
    {
        $telecom = $this->buildTelecom(
            $facility->phone_number ?? null,
            $facility->alternate_number ?? null,
            $facility->email_address ?? null,
            $facility->website ?? null
        );

        $address = $this->buildAddress(
            $facility->physical_location ?? null,
            $facility->postal_address ?? null,
            $facility->state_name ?? null,
            $facility->lga_name ?? null,
            $facility->ward_name ?? null
        );

        if (empty($telecom) && !$address) {
            return [];
        }

        $contact = ['purpose' => $this->buildDirectCodeableConcept(
            'http://terminology.hl7.org/CodeSystem/contactentity-type',
            'ADMIN',
            'Administrative'
        )];

        if (!empty($telecom)) {
            $contact['telecom'] = $telecom;
        }
        if ($address) {
            $contact['address'] = $address;
        }

        return [$contact];
    }

    /**
     * Build human-readable narrative for Organization (dom-6 compliance).
     */
    protected function buildOrganizationNarrative(object $facility, string $sourceType): array
    {
        $name = htmlspecialchars($facility->facility_name ?? 'Unknown', ENT_QUOTES);
        $type = ucfirst($sourceType);
        $state = htmlspecialchars($facility->state_name ?? '', ENT_QUOTES);
        $lga = htmlspecialchars($facility->lga_name ?? '', ENT_QUOTES);
        $ownership = htmlspecialchars($facility->ownership_name ?? '', ENT_QUOTES);

        $html = "<p><b>{$name}</b></p>";
        $html .= "<p>Type: {$type}</p>";
        if ($state) {
            $html .= "<p>Location: {$lga}, {$state}, Nigeria</p>";
        }
        if ($ownership) {
            $html .= "<p>Ownership: {$ownership}</p>";
        }

        return $this->buildNarrative($html);
    }
}
