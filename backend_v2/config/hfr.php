<?php

return [

    'dhis_url' => env('DHIS_BASE_URI'),

    'dhis_username' => env('DHIS_USERNAME'),

    'dhis_password' => env('DHIS_PASSWORD'),

    'notify_verifier' => env('SEND_EMAIL_NOTIFICATION_TO_VERIFER', true),

    'notify_validator' => env('SEND_EMAIL_NOTIFICATION_T0_VALIDATOR', true),

    'notify_publisher' => env('SEND_EMAIL_NOTIFICATION_TO_PUBLISHER', true),

    'notify_publication' => env('SEND_EMAIL_NOTIFICATION_AFTER_PUBLICATION', true),

    'integration_enabled' => env('DHIS_INTEGRATION', true),

    /*
    |--------------------------------------------------------------------------
    | FHIR R4 Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for the FHIR R4-compliant API layer that exposes HFR data
    | as standard FHIR resources (Organization, Location, HealthcareService).
    |
    */
    'fhir' => [
        'enabled' => env('FHIR_ENABLED', true),
        'version' => '4.0.1',
        'base_url' => env('FHIR_BASE_URL', '/api/fhir'),
        'publisher' => 'Federal Ministry of Health, Nigeria',
        'ig_url' => 'https://hfr.health.gov.ng/fhir/ImplementationGuide/hfr-ng',
        'page_size' => env('FHIR_PAGE_SIZE', 20),
        'max_page_size' => env('FHIR_MAX_PAGE_SIZE', 100),
        'validator_url' => env('FHIR_VALIDATOR_URL', 'https://validator.fhir.org'),
        'base_identifier_system' => 'https://hfr.health.gov.ng/fhir',
    ],

];
