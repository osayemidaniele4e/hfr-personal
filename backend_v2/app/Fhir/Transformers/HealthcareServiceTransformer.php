<?php

namespace App\Fhir\Transformers;

/**
 * FHIR R4 HealthcareService Transformer
 *
 * Converts HFR hospital service records (from hs_hospital_services +
 * lst_hosp_services + lst_hosp_service_category) into FHIR HealthcareService
 * resources linked to the parent Organization and Location.
 *
 * @see https://hl7.org/fhir/R4/healthcareservice.html
 */
class HealthcareServiceTransformer extends BaseTransformer
{
    /**
     * Transform a single service row into a FHIR HealthcareService resource.
     *
     * @param object $service       Service row with joined category data
     * @param object $facility      Parent facility row
     * @param string $sourceType    'hospital', 'laboratory', 'pharmacy', 'imaging'
     * @return array FHIR HealthcareService resource
     */
    public function transform(object $service, object $facility, string $sourceType = 'hospital'): array
    {
        $orgId = $this->buildOrganizationId($facility, $sourceType);
        $locId = $this->buildLocationId($facility, $sourceType);

        $resource = [
            'resourceType' => 'HealthcareService',
            'id' => "svc-{$service->id}",
            'meta' => $this->buildMeta(
                'http://hl7.org/fhir/StructureDefinition/HealthcareService'
            ),
            'text' => $this->buildServiceNarrative($service, $facility),
            'active' => true,
            'providedBy' => $this->buildReference(
                'Organization',
                $orgId,
                $facility->facility_name
            ),
            'location' => [
                $this->buildReference('Location', $locId),
            ],
            'name' => $service->service_name,
        ];

        // Category
        if (!empty($service->category_id)) {
            $category = $this->buildCodeableConcept(
                'lst_hosp_service_category',
                (int) $service->category_id,
                $service->category_name ?? null
            );
            if ($category) {
                $resource['category'] = [$category];
            } elseif (!empty($service->category_name)) {
                $resource['category'] = [[
                    'text' => $service->category_name,
                ]];
            }
        }

        // Type
        $serviceType = $this->buildCodeableConcept(
            'lst_hosp_services',
            (int) $service->id,
            $service->service_name
        );
        if ($serviceType) {
            $resource['type'] = [$serviceType];
        } else {
            $resource['type'] = [[
                'text' => $service->service_name,
            ]];
        }

        return $resource;
    }

    /**
     * Transform all services for a facility into an array of HealthcareService resources.
     *
     * @param iterable $services    Collection of service rows
     * @param object $facility      Parent facility row
     * @param string $sourceType    'hospital', 'laboratory', etc.
     * @return array Array of FHIR HealthcareService resources
     */
    public function transformCollection(iterable $services, object $facility, string $sourceType = 'hospital'): array
    {
        $resources = [];
        foreach ($services as $service) {
            $resources[] = $this->transform($service, $facility, $sourceType);
        }
        return $resources;
    }

    /**
     * Build the Organization resource ID for cross-referencing.
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
     * Build the Location resource ID for cross-referencing.
     */
    protected function buildLocationId(object $facility, string $sourceType): string
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
     * Build human-readable narrative for HealthcareService (dom-6 compliance).
     */
    protected function buildServiceNarrative(object $service, object $facility): array
    {
        $serviceName = htmlspecialchars($service->service_name ?? 'Unknown', ENT_QUOTES);
        $facilityName = htmlspecialchars($facility->facility_name ?? '', ENT_QUOTES);
        $category = htmlspecialchars($service->category_name ?? '', ENT_QUOTES);

        $html = "<p><b>{$serviceName}</b></p>";
        if ($category) {
            $html .= "<p>Category: {$category}</p>";
        }
        if ($facilityName) {
            $html .= "<p>Provided by: {$facilityName}</p>";
        }

        return $this->buildNarrative($html);
    }
}
