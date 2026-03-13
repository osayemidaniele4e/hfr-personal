<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class HospitalImportTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'Hospital Import Template';
    }

    public function headings(): array
    {
        return [
            'unique_id',                            // A  — auto-generated (leave blank)
            'state_unique_id',                      // B
            'registration_no',                      // C
            'start_date *',                         // D
            'close_date',                           // E
            'facility_name *',                      // F
            'alt_facility_name',                    // G
            'state_id *',                           // H
            'lga_id *',                             // I
            'ward_id *',                            // J
            'ownership_id *',                       // K
            'ownership_type_id *',                  // L
            'facility_level_id *',                  // M
            'facility_level_option_id',             // N
            'facility_level_options_category_id',   // O
            'physical_location',                    // P
            'postal_address',                       // Q
            'longitude',                            // R
            'latitude',                             // S
            'phone_number',                         // T
            'alternate_number',                     // U
            'email_address',                        // V
            'website',                              // W
            'operational_days',                     // X
            'operational_hours',                    // Y
            'operational_status_id *',              // Z
            'registration_status_id',               // AA
            'license_status_id',                    // AB
            'outpatient',                           // AC
            'inpatient',                            // AD
            'doctors',                              // AE
            'pharmacists',                          // AF
            'dentist',                              // AG
            'pharmacy_technicians',                 // AH
            'nurses',                               // AI
            'lab_scientists',                       // AJ
            'midwifes',                             // AK
            'lab_technicians',                      // AL
            'nurse_midwife',                        // AM
            'him_officers',                         // AN
            'community_health_officer',             // AO
            'community_extension_workers',          // AP
            'jun_community_extension_worker',       // AQ
            'dental_technicians',                   // AR
            'env_health_officers',                  // AS
            'attendants',                           // AT
            'beds',                                 // AU
            'onsite_laboratory',                    // AV
            'onsite_imaging',                       // AW
            'onsite_pharmarcy',                     // AX
            'mortuary_services',                    // AY
            'ambulance_services',                   // AZ
            'status_id',                            // BA  — auto-set (leave blank)
            'created_by',                           // BB  — auto-set (leave blank)
            'created_at',                           // BC  — auto-set (leave blank)
            'updated_at',                           // BD  — auto-set (leave blank)
            'requested_by',                         // BE  — auto-set (leave blank)
            'requested_at',                         // BF  — auto-set (leave blank)
            'request_note',                         // BG  — auto-set (leave blank)
            'verified_by',                          // BH  — auto-set (leave blank)
            'verified_at',                          // BI  — auto-set (leave blank)
            'verify_note',                          // BJ  — auto-set (leave blank)
            'validated_by',                         // BK  — auto-set (leave blank)
            'validated_at',                         // BL  — auto-set (leave blank)
            'validate_note',                        // BM  — auto-set (leave blank)
            'published_by',                         // BN  — auto-set (leave blank)
            'published_at',                         // BO  — auto-set (leave blank)
            'publish_note',                         // BP  — auto-set (leave blank)
        ];
    }

    /**
     * Provide a sample row so users can understand expected data format
     */
    public function array(): array
    {
        return [
            [
                '',                             // unique_id  — auto-generated
                'LG-001',                       // state_unique_id
                'REG-2024-001',                 // registration_no
                '2024-01-15',                   // start_date  (YYYY-MM-DD)
                '',                             // close_date  (YYYY-MM-DD, blank if operational)
                'General Hospital Lagos',       // facility_name
                'GH Lagos',                     // alt_facility_name
                '25',                           // state_id  (numeric ID)
                '506',                          // lga_id    (numeric ID)
                '5061',                         // ward_id   (numeric ID)
                '1',                            // ownership_id
                '1',                            // ownership_type_id
                '2',                            // facility_level_id
                '',                             // facility_level_option_id
                '',                             // facility_level_options_category_id
                'Plot 5 Hospital Road',         // physical_location
                'P.O. Box 123',                 // postal_address
                '3.3792',                       // longitude
                '6.5244',                       // latitude
                '+2348012345678',               // phone_number
                '+2348098765432',               // alternate_number
                'info@hospital.com',            // email_address
                'https://www.hospital.com',     // website
                'Monday,Tuesday,Wednesday,Thursday,Friday', // operational_days
                '8am - 5pm',                    // operational_hours
                '1',                            // operational_status_id
                '1',                            // registration_status_id
                '1',                            // license_status_id
                'Yes',                          // outpatient
                'Yes',                          // inpatient
                '10',                           // doctors
                '5',                            // pharmacists
                '2',                            // dentist
                '3',                            // pharmacy_technicians
                '20',                           // nurses
                '4',                            // lab_scientists
                '8',                            // midwifes
                '3',                            // lab_technicians
                '5',                            // nurse_midwife
                '2',                            // him_officers
                '3',                            // community_health_officer
                '5',                            // community_extension_workers
                '4',                            // jun_community_extension_worker
                '1',                            // dental_technicians
                '2',                            // env_health_officers
                '6',                            // attendants
                '50',                           // beds
                'Yes',                          // onsite_laboratory
                'Yes',                          // onsite_imaging
                'Yes',                          // onsite_pharmarcy
                'No',                           // mortuary_services
                'Yes',                          // ambulance_services
                '',                             // status_id       — auto-set
                '',                             // created_by      — auto-set
                '',                             // created_at      — auto-set
                '',                             // updated_at      — auto-set
                '',                             // requested_by    — auto-set
                '',                             // requested_at    — auto-set
                '',                             // request_note    — auto-set
                '',                             // verified_by     — auto-set
                '',                             // verified_at     — auto-set
                '',                             // verify_note     — auto-set
                '',                             // validated_by    — auto-set
                '',                             // validated_at    — auto-set
                '',                             // validate_note   — auto-set
                '',                             // published_by    — auto-set
                '',                             // published_at    — auto-set
                '',                             // publish_note    — auto-set
            ],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Green header for user-editable columns (A-AZ, cols 1-52)
        // Grey header for system-managed columns (BA-BP, cols 53-68)
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2E7D32'],
                ],
            ],
        ];
    }

    /**
     * Apply grey fill to system-managed header cells after the sheet is built
     */
    public function registerEvents(): array
    {
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,  // unique_id
            'B' => 18,  // state_unique_id
            'C' => 18,  // registration_no
            'D' => 15,  // start_date
            'E' => 15,  // close_date
            'F' => 30,  // facility_name
            'G' => 25,  // alt_facility_name
            'H' => 12,  // state_id
            'I' => 12,  // lga_id
            'J' => 12,  // ward_id
            'K' => 15,  // ownership_id
            'L' => 18,  // ownership_type_id
            'M' => 18,  // facility_level_id
            'N' => 22,  // facility_level_option_id
            'O' => 30,  // facility_level_options_category_id
            'P' => 25,  // physical_location
            'Q' => 20,  // postal_address
            'R' => 12,  // longitude
            'S' => 12,  // latitude
            'T' => 18,  // phone_number
            'U' => 18,  // alternate_number
            'V' => 25,  // email_address
            'W' => 25,  // website
            'X' => 35,  // operational_days
            'Y' => 18,  // operational_hours
            'Z' => 22,  // operational_status_id
            'AA' => 22, // registration_status_id
            'AB' => 18, // license_status_id
        ];
    }
}
