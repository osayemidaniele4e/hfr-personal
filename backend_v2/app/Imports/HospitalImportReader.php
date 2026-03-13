<?php

namespace App\Imports;

use App\Models\HospitalImport;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HospitalImportReader implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    private string $batchId;
    private int $uploadedBy;
    private int $importedCount = 0;
    private array $rowErrors = [];

    /**
     * Track LGA-level serial numbers within this import batch
     * to avoid duplicate state_unique_ids when multiple rows share the same LGA.
     */
    private array $lgaSerialCounters = [];

    /**
     * Max lengths matching the staging table column sizes
     */
    private array $maxLengths = [
        'facility_name'       => 200,
        'alt_facility_name'   => 200,
        'state_unique_id'     => 100,
        'registration_no'     => 100,
        'physical_location'   => 500,
        'postal_address'      => 500,
        'phone_number'        => 50,
        'alternate_number'    => 50,
        'email_address'       => 255,
        'website'             => 255,
        'operational_days'    => 255,
        'operational_hours'   => 100,
    ];

    public function __construct(string $batchId, int $uploadedBy)
    {
        $this->batchId = $batchId;
        $this->uploadedBy = $uploadedBy;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getRowErrors(): array
    {
        return $this->rowErrors;
    }

    /**
     * Process all rows from the file. Each row is handled individually
     * so one bad row doesn't crash the entire import.
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 = heading row + 0-index

            try {
                // Normalize keys
                $cleaned = [];
                foreach ($row->toArray() as $key => $value) {
                    $cleaned[$this->cleanKey((string) $key)] = $value;
                }

                // Skip completely empty rows
                $filled = array_filter($cleaned, fn($v) => $v !== null && $v !== '');
                if (empty($filled)) {
                    continue;
                }

                $errors = $this->validateRow($cleaned);

                // Auto-generate state_unique_id: {state_short_code}/{lga_code}/{serial}
                $stateUniqueId = $this->generateStateUniqueId(
                    $cleaned['state_id'] ?? null,
                    $cleaned['lga_id'] ?? null
                );

                $record = new HospitalImport([
                    'batch_id'                          => $this->batchId,
                    'uploaded_by'                       => $this->uploadedBy,
                    'state_unique_id'                   => $stateUniqueId,
                    'registration_no'                   => $this->truncate('registration_no', $cleaned['registration_no'] ?? null),
                    'start_date'                        => $this->parseDate($cleaned['start_date'] ?? null),
                    'close_date'                        => $this->parseDate($cleaned['close_date'] ?? null),
                    'facility_name'                     => $this->truncate('facility_name', $cleaned['facility_name'] ?? null),
                    'alt_facility_name'                 => $this->truncate('alt_facility_name', $cleaned['alt_facility_name'] ?? null),
                    'state_id'                          => $cleaned['state_id'] ?? null,
                    'lga_id'                            => $cleaned['lga_id'] ?? null,
                    'ward_id'                           => $cleaned['ward_id'] ?? null,
                    'ownership_id'                      => $cleaned['ownership_id'] ?? null,
                    'ownership_type_id'                 => $cleaned['ownership_type_id'] ?? null,
                    'facility_level_id'                 => $cleaned['facility_level_id'] ?? null,
                    'facility_level_option_id'          => $cleaned['facility_level_option_id'] ?? null,
                    'facility_level_options_category_id' => $cleaned['facility_level_options_category_id'] ?? null,
                    'physical_location'                 => $this->truncate('physical_location', $cleaned['physical_location'] ?? null),
                    'postal_address'                    => $this->truncate('postal_address', $cleaned['postal_address'] ?? null),
                    'longitude'                         => $cleaned['longitude'] ?? null,
                    'latitude'                          => $cleaned['latitude'] ?? null,
                    'phone_number'                      => $this->truncate('phone_number', $cleaned['phone_number'] ?? null),
                    'alternate_number'                  => $this->truncate('alternate_number', $cleaned['alternate_number'] ?? null),
                    'email_address'                     => $this->truncate('email_address', $cleaned['email_address'] ?? null),
                    'website'                           => $this->truncate('website', $cleaned['website'] ?? null),
                    'operational_days'                  => $this->truncate('operational_days', $cleaned['operational_days'] ?? null),
                    'operational_hours'                 => $this->truncate('operational_hours', $cleaned['operational_hours'] ?? null),
                    'operational_status_id'             => $cleaned['operational_status_id'] ?? null,
                    'registration_status_id'            => $cleaned['registration_status_id'] ?? null,
                    'license_status_id'                 => $cleaned['license_status_id'] ?? null,
                    'outpatient'                        => $cleaned['outpatient'] ?? null,
                    'inpatient'                         => $cleaned['inpatient'] ?? null,
                    'doctors'                           => $cleaned['doctors'] ?? null,
                    'pharmacists'                       => $cleaned['pharmacists'] ?? null,
                    'dentist'                           => $cleaned['dentist'] ?? null,
                    'pharmacy_technicians'              => $cleaned['pharmacy_technicians'] ?? null,
                    'nurses'                            => $cleaned['nurses'] ?? null,
                    'lab_scientists'                    => $cleaned['lab_scientists'] ?? null,
                    'midwifes'                          => $cleaned['midwifes'] ?? null,
                    'lab_technicians'                   => $cleaned['lab_technicians'] ?? null,
                    'nurse_midwife'                     => $cleaned['nurse_midwife'] ?? null,
                    'him_officers'                      => $cleaned['him_officers'] ?? null,
                    'community_health_officer'          => $cleaned['community_health_officer'] ?? null,
                    'community_extension_workers'       => $cleaned['community_extension_workers'] ?? null,
                    'jun_community_extension_worker'    => $cleaned['jun_community_extension_worker'] ?? null,
                    'dental_technicians'                => $cleaned['dental_technicians'] ?? null,
                    'env_health_officers'               => $cleaned['env_health_officers'] ?? null,
                    'attendants'                        => $cleaned['attendants'] ?? null,
                    'beds'                              => $cleaned['beds'] ?? null,
                    'onsite_laboratory'                 => $cleaned['onsite_laboratory'] ?? null,
                    'onsite_imaging'                    => $cleaned['onsite_imaging'] ?? null,
                    'onsite_pharmarcy'                  => $cleaned['onsite_pharmarcy'] ?? null,
                    'mortuary_services'                 => $cleaned['mortuary_services'] ?? null,
                    'ambulance_services'                => $cleaned['ambulance_services'] ?? null,
                    'validation_errors'                 => !empty($errors) ? json_encode($errors) : null,
                    'is_valid'                          => empty($errors),
                ]);

                $record->save();
                $this->importedCount++;

            } catch (\Exception $e) {
                // Log the error but continue processing remaining rows
                Log::warning("Import row {$rowNumber} failed: " . $e->getMessage());
                $this->rowErrors[] = "Row {$rowNumber}: " . $e->getMessage();
            }
        }
    }

    /**
     * Normalize heading row keys: strip asterisks, trim whitespace
     */
    private function cleanKey(string $key): string
    {
        return trim(str_replace(['*', ' '], '', $key));
    }

    /**
     * Truncate string to the max column length
     */
    private function truncate(string $field, $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = (string) $value;
        $max = $this->maxLengths[$field] ?? 255;

        return mb_strlen($value) > $max ? mb_substr($value, 0, $max) : $value;
    }

    /**
     * Validate a single row and return array of error messages
     */
    private function validateRow(array $row): array
    {
        $errors = [];

        // Required fields
        if (empty($row['facility_name'])) {
            $errors[] = 'Facility name is required';
        }
        if (empty($row['state_id'])) {
            $errors[] = 'State ID is required';
        }
        if (empty($row['lga_id'])) {
            $errors[] = 'LGA ID is required';
        }
        if (empty($row['ownership_id'])) {
            $errors[] = 'Ownership ID is required';
        }
        if (empty($row['ownership_type_id'])) {
            $errors[] = 'Ownership Type ID is required';
        }
        if (empty($row['facility_level_id'])) {
            $errors[] = 'Facility Level ID is required';
        }
        if (empty($row['operational_status_id'])) {
            $errors[] = 'Operational Status ID is required';
        }

        // Foreign key validation
        if (!empty($row['state_id']) && !DB::table('ou_states')->where('id', $row['state_id'])->exists()) {
            $errors[] = "Invalid state_id: {$row['state_id']}";
        }
        if (!empty($row['lga_id']) && !DB::table('ou_lgas')->where('id', $row['lga_id'])->exists()) {
            $errors[] = "Invalid lga_id: {$row['lga_id']}";
        }
        if (!empty($row['ward_id']) && !DB::table('ou_wards')->where('id', $row['ward_id'])->exists()) {
            $errors[] = "Invalid ward_id: {$row['ward_id']}";
        }
        if (!empty($row['ownership_id']) && !DB::table('lst_ownerships')->where('id', $row['ownership_id'])->exists()) {
            $errors[] = "Invalid ownership_id: {$row['ownership_id']}";
        }
        if (!empty($row['facility_level_id']) && !DB::table('lst_level_of_care')->where('id', $row['facility_level_id'])->exists()) {
            $errors[] = "Invalid facility_level_id: {$row['facility_level_id']}";
        }

        // Numeric field validations
        $numericFields = [
            'doctors', 'dentist', 'pharmacists', 'pharmacy_technicians', 'nurses',
            'midwifes', 'nurse_midwife', 'lab_scientists', 'lab_technicians',
            'him_officers', 'community_health_officer', 'community_extension_workers',
            'jun_community_extension_worker', 'dental_technicians', 'env_health_officers',
            'attendants', 'beds',
        ];

        foreach ($numericFields as $field) {
            if (!empty($row[$field]) && !is_numeric($row[$field])) {
                $errors[] = "{$field} must be a number";
            }
        }

        // Coordinate validation
        if (!empty($row['latitude'])) {
            $lat = (float) $row['latitude'];
            if ($lat < 3.883 || $lat > 13.867) {
                $errors[] = "Latitude must be between 3.883 and 13.867 (Nigeria bounds)";
            }
        }
        if (!empty($row['longitude'])) {
            $lng = (float) $row['longitude'];
            if ($lng < 2.483 || $lng > 20) {
                $errors[] = "Longitude must be between 2.483 and 20 (Nigeria bounds)";
            }
        }

        // Email validation
        if (!empty($row['email_address']) && !filter_var($row['email_address'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email address";
        }

        return $errors;
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        // Handle Excel numeric date values
        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
        }

        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Auto-generate state_unique_id: {state_short_code}/{lga_code}/{serial_number}
     * Serial is per-LGA, picks up from the max existing in the DB.
     */
    private function generateStateUniqueId($stateId, $lgaId): ?string
    {
        if (empty($stateId) || empty($lgaId)) {
            return null;
        }

        $lgaId = (int) $lgaId;

        // Get state short_code
        $state = DB::table('ou_states')->where('id', $stateId)->first();
        if (!$state) {
            return null;
        }

        // Get LGA code
        $lga = DB::table('ou_lgas')->where('id', $lgaId)->first();
        if (!$lga) {
            return null;
        }

        // Initialize counter for this LGA if not yet tracked
        if (!isset($this->lgaSerialCounters[$lgaId])) {
            // Find the max serial for this LGA from existing state_unique_id values
            $prefix = $state->short_code . '/' . $lga->lga_code . '/';
            $maxSerial = DB::table('hs_hospitals_history')
                ->where('lga_id', $lgaId)
                ->where('state_unique_id', 'LIKE', $prefix . '%')
                ->selectRaw('MAX(CAST(SUBSTRING_INDEX(state_unique_id, "/", -1) AS UNSIGNED)) as max_sn')
                ->value('max_sn');

            // Also check the staging table for previously imported rows in this session
            $maxStagingSerial = DB::table('hospital_imports')
                ->where('lga_id', $lgaId)
                ->where('state_unique_id', 'LIKE', $prefix . '%')
                ->selectRaw('MAX(CAST(SUBSTRING_INDEX(state_unique_id, "/", -1) AS UNSIGNED)) as max_sn')
                ->value('max_sn');

            $this->lgaSerialCounters[$lgaId] = max((int) ($maxSerial ?? 0), (int) ($maxStagingSerial ?? 0));
        }

        // Increment
        $this->lgaSerialCounters[$lgaId]++;
        $serial = str_pad($this->lgaSerialCounters[$lgaId], 3, '0', STR_PAD_LEFT);

        return $state->short_code . '/' . $lga->lga_code . '/' . $serial;
    }
}
