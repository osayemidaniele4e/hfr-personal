<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * FHIR Code Mapping Model
 *
 * Maps HFR lookup table values to FHIR R4 CodeSystem codes.
 * Used by FHIR transformers to convert internal HFR codes to
 * standard FHIR CodeableConcept structures.
 * Resilient to missing table — returns null gracefully.
 *
 * @property int $id
 * @property string $hfr_table
 * @property int $hfr_id
 * @property string $hfr_display
 * @property string $fhir_system
 * @property string $fhir_code
 * @property string $fhir_display
 * @property string|null $resource_type
 * @property string|null $element_path
 * @property bool $is_active
 */
class FhirCodeMapping extends Model
{
    protected $table = 'fhir_code_mappings';

    protected $fillable = [
        'hfr_table',
        'hfr_id',
        'hfr_display',
        'fhir_system',
        'fhir_code',
        'fhir_display',
        'resource_type',
        'element_path',
        'is_active',
    ];

    protected $casts = [
        'hfr_id' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Cache TTL in seconds (1 hour).
     */
    private const CACHE_TTL = 3600;

    /**
     * Resolve an HFR lookup value to a FHIR coding array.
     *
     * @param string $hfrTable  Source lookup table (e.g. 'lst_facility_types')
     * @param int|null $hfrId   Primary key in the source table
     * @return array|null       FHIR Coding array { system, code, display } or null
     */
    public static function resolve(string $hfrTable, ?int $hfrId): ?array
    {
        if ($hfrId === null) {
            return null;
        }

        $mappings = self::getCachedMappings();
        $key = "{$hfrTable}:{$hfrId}";

        if (!isset($mappings[$key])) {
            return null;
        }

        $m = $mappings[$key];

        return [
            'system' => $m['fhir_system'],
            'code' => $m['fhir_code'],
            'display' => $m['fhir_display'],
        ];
    }

    /**
     * Resolve an HFR lookup value to a full FHIR CodeableConcept.
     *
     * @param string $hfrTable
     * @param int|null $hfrId
     * @param string|null $fallbackDisplay  Display text to use if no mapping exists
     * @return array|null  FHIR CodeableConcept { coding: [...], text: "..." }
     */
    public static function resolveCodeableConcept(string $hfrTable, ?int $hfrId, ?string $fallbackDisplay = null): ?array
    {
        $coding = self::resolve($hfrTable, $hfrId);

        if ($coding) {
            return [
                'coding' => [$coding],
                'text' => $coding['display'],
            ];
        }

        // Return a text-only CodeableConcept if we have a display name but no mapping
        if ($fallbackDisplay) {
            return [
                'text' => $fallbackDisplay,
            ];
        }

        return null;
    }

    /**
     * Get all active mappings from cache, keyed by "hfr_table:hfr_id".
     * Returns empty array if the table doesn't exist.
     */
    public static function getCachedMappings(): array
    {
        return Cache::remember('fhir_code_mappings', self::CACHE_TTL, function () {
            try {
                if (!Schema::hasTable('fhir_code_mappings')) {
                    return [];
                }

                return self::where('is_active', true)
                    ->get()
                    ->keyBy(fn($m) => "{$m->hfr_table}:{$m->hfr_id}")
                    ->map(fn($m) => [
                        'fhir_system' => $m->fhir_system,
                        'fhir_code' => $m->fhir_code,
                        'fhir_display' => $m->fhir_display,
                    ])
                    ->toArray();
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    /**
     * Clear the cached mappings (call after seeding or editing mappings).
     */
    public static function clearCache(): void
    {
        Cache::forget('fhir_code_mappings');
    }
}
