<?php

/**
 * Dynamic profile completeness: every column on the facility table is checked
 * except those listed here (ids, timestamps, workflow / audit).
 *
 * missing_fields[] uses actual database column names (snake_case).
 *
 * Fill rules (see FacilityCompletenessService):
 * - null and blank strings → missing
 * - Columns whose name ends with _id → 0 or "0" or "" → missing (foreign-key style)
 * - Other numbers → 0 counts as filled
 * - image_url → null, "", "[]", "{}" → missing
 */
return [
    'table' => 'hs_hospitals_history',

    'exclude_columns' => [
        'id',
        'created_at',
        'updated_at',
        'created_by',
        'requested_by',
        'requested_at',
        'request_note',
        'verified_by',
        'verified_at',
        'verify_note',
        'validated_by',
        'validated_at',
        'validate_note',
        'published_by',
        'published_at',
        'publish_note',
        'action',
        'verified_id',
        'verified_email',
        'verified_mobile',
        'validated_email',
        'validated_mobile',
        'published_email',
        'published_mobile',
        'status_id',
    ],

    /**
     * Optional: friendly labels for API consumers / UI (column name => label).
     * Columns not listed are still scored; use snake_case → Title Case as fallback in UI.
     */
    'column_labels' => [
        'facility_name' => 'Facility name',
        'unique_id' => 'Unique ID',
        'state_unique_id' => 'State unique ID',
        'registration_no' => 'Registration number',
        'physical_location' => 'Physical location',
        'postal_address' => 'Postal address',
        'phone_number' => 'Phone',
        'alternate_number' => 'Alternate phone',
        'email_address' => 'Email',
        'facility_type_id' => 'Facility type',
        'ownership_id' => 'Ownership',
        'ownership_type_id' => 'Ownership type',
        'state_id' => 'State',
        'lga_id' => 'LGA',
        'ward_id' => 'Ward',
        'doctors' => 'No. of medical doctors',
        'beds' => 'No. of beds',
        'nurses' => 'No. of nurses',
        'nurse_midwife' => 'No. of midwives',
        'community_health_officer' => 'No. of health workers',
    ],
];
