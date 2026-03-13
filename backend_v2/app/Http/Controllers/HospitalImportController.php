<?php

namespace App\Http\Controllers;

use App\Exports\HospitalImportTemplate;
use App\Imports\HospitalImportReader;
use App\Models\HospitalImport;
use App\Models\Hospital;
use App\Models\HospitalServiceHistory;
use App\Models\StatusTracking;
use App\Models\HospitalHistory;
use App\Models\ApprovalNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Auth;

class HospitalImportController extends Controller
{
    /**
     * Show the import form
     */
    public function index()
    {
        // Get user's active batches
        $batches = HospitalImport::where('uploaded_by', Auth::user()->id)
            ->select('batch_id', DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN is_valid = 1 THEN 1 ELSE 0 END) as valid_count'), DB::raw('MIN(created_at) as uploaded_at'))
            ->groupBy('batch_id')
            ->orderBy('uploaded_at', 'desc')
            ->get();

        return view('hospitals.import.index', compact('batches'));
    }

    /**
     * Download the import template
     */
    public function downloadTemplate()
    {
        return Excel::download(new HospitalImportTemplate, 'hospital_import_template.xlsx');
    }

    /**
     * Upload and parse the file into staging table
     */
    public function upload(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $batchId = Str::uuid()->toString();
        $reader  = new HospitalImportReader($batchId, Auth::user()->id);

        try {
            Excel::import($reader, $request->file('import_file'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['import_file' => 'Error reading file: ' . $e->getMessage()]);
        }

        $imported  = $reader->getImportedCount();
        $rowErrors = $reader->getRowErrors();

        if ($imported === 0 && empty($rowErrors)) {
            return redirect()->back()->withErrors(['import_file' => 'No records found in the uploaded file. Please ensure the file follows the template format.']);
        }

        $message = "{$imported} record(s) uploaded successfully.";
        if (!empty($rowErrors)) {
            $message .= ' ' . count($rowErrors) . ' row(s) could not be read and were skipped.';
        }
        $message .= ' Please review before submitting.';

        session()->flash('alert-success', $message);

        return redirect()->route('hospitals.import.preview', $batchId);
    }

    /**
     * Preview staged data for a batch
     */
    public function preview(Request $request, string $batchId)
    {
        $search = $request->input('search');
        $filterStatus = $request->input('filter_status'); // all, valid, errors

        $query = HospitalImport::where('batch_id', $batchId)
            ->where('uploaded_by', Auth::user()->id);

        // Get lookup data for displaying names instead of IDs
        $states = DB::table('ou_states')->pluck('name', 'id')->toArray();
        $lgas = DB::table('ou_lgas')->pluck('name', 'id')->toArray();
        $wards = DB::table('ou_wards')->pluck('name', 'id')->toArray();
        $ownerships = DB::table('lst_ownerships')->pluck('name', 'id')->toArray();
        $facilityLevels = DB::table('lst_level_of_care')->pluck('name', 'id')->toArray();

        // Totals are always against the full batch (unfiltered)
        $totalCount = (clone $query)->count();
        $validCount = (clone $query)->where('is_valid', true)->count();
        $errorCount = $totalCount - $validCount;

        if ($totalCount === 0) {
            return redirect()->route('hospitals.import.index')->withErrors(['batch' => 'No records found for this batch.']);
        }

        // Apply search filter
        if ($search) {
            // Reverse-lookup: find IDs whose name matches the search term
            $stateIds = array_keys(array_filter($states, fn($n) => stripos($n, $search) !== false));
            $lgaIds   = array_keys(array_filter($lgas, fn($n) => stripos($n, $search) !== false));
            $wardIds  = array_keys(array_filter($wards, fn($n) => stripos($n, $search) !== false));

            $query->where(function ($q) use ($search, $stateIds, $lgaIds, $wardIds) {
                $q->where('facility_name', 'like', "%{$search}%")
                  ->orWhere('physical_location', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('email_address', 'like', "%{$search}%")
                  ->orWhere('registration_no', 'like', "%{$search}%")
                  ->orWhere('state_unique_id', 'like', "%{$search}%");

                if ($stateIds) $q->orWhereIn('state_id', $stateIds);
                if ($lgaIds)   $q->orWhereIn('lga_id', $lgaIds);
                if ($wardIds)  $q->orWhereIn('ward_id', $wardIds);
            });
        }

        // Apply status filter
        if ($filterStatus === 'valid') {
            $query->where('is_valid', true);
        } elseif ($filterStatus === 'errors') {
            $query->where('is_valid', false);
        }

        $records = $query->paginate(20)->appends($request->only('search', 'filter_status'));

        return view('hospitals.import.preview', compact(
            'records', 'batchId', 'states', 'lgas', 'wards',
            'ownerships', 'facilityLevels', 'totalCount', 'validCount', 'errorCount',
            'search', 'filterStatus'
        ));
    }

    /**
     * Edit a single staged record
     */
    public function edit(string $batchId, int $id)
    {
        $record = HospitalImport::where('batch_id', $batchId)
            ->where('uploaded_by', Auth::user()->id)
            ->findOrFail($id);

        return view('hospitals.import.edit', compact('record', 'batchId'));
    }

    /**
     * Update a single staged record
     */
    public function update(Request $request, string $batchId, int $id)
    {
        $record = HospitalImport::where('batch_id', $batchId)
            ->where('uploaded_by', Auth::user()->id)
            ->findOrFail($id);

        $request->validate([
            'facility_name' => 'required|max:200',
            'start_date' => 'nullable|date',
            'state_id' => 'required',
            'lga_id' => 'required',
            'ward_id' => 'nullable',
            'ownership_id' => 'required',
            'ownership_type_id' => 'required',
            'facility_level_id' => 'required',
            'operational_status_id' => 'required',
            'latitude' => 'nullable|numeric|between:3.883,13.867',
            'longitude' => 'nullable|numeric|between:2.483,20',
            'email_address' => 'nullable|email',
        ]);

        $record->fill($request->except(['_token', '_method']));

        // Re-validate the record
        $errors = $this->validateRecord($record);
        $record->validation_errors = !empty($errors) ? json_encode($errors) : null;
        $record->is_valid = empty($errors);

        $record->save();

        session()->flash('alert-success', 'Record updated successfully.');
        return redirect()->route('hospitals.import.preview', $batchId);
    }

    /**
     * Delete a single staged record
     */
    public function destroy(string $batchId, int $id)
    {
        $record = HospitalImport::where('batch_id', $batchId)
            ->where('uploaded_by', Auth::user()->id)
            ->findOrFail($id);

        $record->delete();

        $remaining = HospitalImport::where('batch_id', $batchId)->count();

        if ($remaining === 0) {
            session()->flash('alert-success', 'All records removed. Batch deleted.');
            return redirect()->route('hospitals.import.index');
        }

        session()->flash('alert-success', 'Record removed from import batch.');
        return redirect()->route('hospitals.import.preview', $batchId);
    }

    /**
     * Delete entire batch
     */
    public function destroyBatch(string $batchId)
    {
        HospitalImport::where('batch_id', $batchId)
            ->where('uploaded_by', Auth::user()->id)
            ->delete();

        session()->flash('alert-success', 'Import batch deleted.');
        return redirect()->route('hospitals.import.index');
    }

    /**
     * Submit the batch — create actual hospital records (published directly, no approval needed)
     */
    public function submit(string $batchId)
    {
        $records = HospitalImport::where('batch_id', $batchId)
            ->where('uploaded_by', Auth::user()->id)
            ->get();

        if ($records->isEmpty()) {
            return redirect()->route('hospitals.import.index')
                ->withErrors(['batch' => 'No records found for this batch.']);
        }

        // Check if all records are valid
        $invalidCount = $records->where('is_valid', false)->count();
        if ($invalidCount > 0) {
            session()->flash('alert-danger', "{$invalidCount} record(s) have validation errors. Please fix or remove them before submitting.");
            return redirect()->route('hospitals.import.preview', $batchId);
        }

        $successCount = 0;
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $userId = Auth::user()->id;

        DB::beginTransaction();
        try {
            foreach ($records as $record) {
                $hosp = new HospitalHistory();

                // Fill hospital data
                $hosp->facility_name = $record->facility_name;
                $hosp->alt_facility_name = $record->alt_facility_name;
                $hosp->state_unique_id = $record->state_unique_id;
                $hosp->registration_no = $record->registration_no;
                $hosp->start_date = $record->start_date;
                $hosp->close_date = $record->close_date;
                $hosp->state_id = $record->state_id;
                $hosp->lga_id = $record->lga_id;
                $hosp->ward_id = $record->ward_id;
                $hosp->ownership_id = $record->ownership_id;
                $hosp->ownership_type_id = $record->ownership_type_id;
                $hosp->facility_level_id = $record->facility_level_id;
                $hosp->facility_level_option_id = $record->facility_level_option_id;
                $hosp->facility_level_options_category_id = $record->facility_level_options_category_id;
                $hosp->facility_type_id = $record->facility_level_id;
                $hosp->physical_location = $record->physical_location;
                $hosp->postal_address = $record->postal_address;
                $hosp->longitude = $record->longitude;
                $hosp->latitude = $record->latitude;
                $hosp->phone_number = $record->phone_number;
                $hosp->alternate_number = $record->alternate_number;
                $hosp->email_address = $record->email_address;
                $hosp->website = $record->website;
                $hosp->operational_days = $record->operational_days ?? '';
                $hosp->operational_hours = $record->operational_hours;
                $hosp->operational_status_id = $record->operational_status_id;
                $hosp->registration_status_id = $record->registration_status_id;
                $hosp->license_status_id = $record->license_status_id;
                $hosp->doctors = $record->doctors;
                $hosp->dentist = $record->dentist;
                $hosp->pharmacists = $record->pharmacists;
                $hosp->pharmacy_technicians = $record->pharmacy_technicians;
                $hosp->nurses = $record->nurses;
                $hosp->midwifes = $record->midwifes;
                $hosp->nurse_midwife = $record->nurse_midwife;
                $hosp->lab_scientists = $record->lab_scientists;
                $hosp->lab_technicians = $record->lab_technicians;
                $hosp->him_officers = $record->him_officers;
                $hosp->community_health_officer = $record->community_health_officer;
                $hosp->community_extension_workers = $record->community_extension_workers;
                $hosp->jun_community_extension_worker = $record->jun_community_extension_worker;
                $hosp->dental_technicians = $record->dental_technicians;
                $hosp->env_health_officers = $record->env_health_officers;
                $hosp->attendants = $record->attendants;
                $hosp->outpatient = $record->outpatient;
                $hosp->inpatient = $record->inpatient;
                $hosp->beds = $record->beds;
                $hosp->onsite_laboratory = $record->onsite_laboratory;
                $hosp->onsite_imaging = $record->onsite_imaging;
                $hosp->onsite_pharmarcy = $record->onsite_pharmarcy;
                $hosp->mortuary_services = $record->mortuary_services;
                $hosp->ambulance_services = $record->ambulance_services;

                // Generate unique ID with collision retry
                $maxAttempts = 20;
                $generatedId = null;
                for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
                    $candidateId = $hosp->generateUID();
                    // Check it doesn't already exist in the table
                    if (!DB::table('hs_hospitals_history')->where('id', $candidateId)->exists()) {
                        $generatedId = $candidateId;
                        break;
                    }
                }
                if ($generatedId === null) {
                    throw new \Exception("Could not generate a unique ID for '{$record->facility_name}' after {$maxAttempts} attempts.");
                }
                $hosp->id = $generatedId;

                $hosp->unique_id = $hosp->generateFacilityCode(
                    $record->lga_id, '1', $record->facility_level_id, $record->ownership_id
                );

                // Published directly — status 6 = "Create Published"
                $hosp->status_id = 6;
                $hosp->action = 'CREATE FACILITY (BULK IMPORT)';
                $hosp->image_url = json_encode([]);

                // Request info
                $hosp->created_by = $userId;
                $hosp->requested_by = $userId;
                $hosp->requested_at = $now;

                // Auto-approved: verified
                $hosp->verified_by = $userId;
                $hosp->verified_at = $now;
                $hosp->verify_note = 'Auto-approved via bulk import';

                // Auto-approved: validated
                $hosp->validated_by = $userId;
                $hosp->validated_at = $now;
                $hosp->validate_note = 'Auto-approved via bulk import';

                // Auto-approved: published
                $hosp->published_by = $userId;
                $hosp->published_at = $now;
                $hosp->publish_note = 'Auto-published via bulk import';

                $hosp->save();

                // Status tracking — record as published
                $status = new StatusTracking;
                $status->hospital_id = $hosp->id;
                $status->user_id = $userId;
                $status->status_id = 6;
                $status->note = 'Bulk import - auto-published';
                $status->created_at = $now;
                $status->save();

                $successCount++;
            }

            // Remove imported staging records
            HospitalImport::where('batch_id', $batchId)->delete();

            DB::commit();

            session()->flash('alert-success', "{$successCount} hospital(s) imported and published successfully!");
            return redirect()->route('hospitals.index');

        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('alert-danger', 'Import failed: ' . $e->getMessage());
            return redirect()->route('hospitals.import.preview', $batchId);
        }
    }

    /**
     * Validate a single record (used during update)
     */
    private function validateRecord(HospitalImport $record): array
    {
        $errors = [];

        if (empty($record->facility_name)) {
            $errors[] = 'Facility name is required';
        }
        if (empty($record->state_id)) {
            $errors[] = 'State ID is required';
        }
        if (empty($record->lga_id)) {
            $errors[] = 'LGA ID is required';
        }
        if (empty($record->ownership_id)) {
            $errors[] = 'Ownership ID is required';
        }
        if (empty($record->ownership_type_id)) {
            $errors[] = 'Ownership Type ID is required';
        }
        if (empty($record->facility_level_id)) {
            $errors[] = 'Facility Level ID is required';
        }
        if (empty($record->operational_status_id)) {
            $errors[] = 'Operational Status ID is required';
        }

        // FK checks
        if (!empty($record->state_id) && !DB::table('ou_states')->where('id', $record->state_id)->exists()) {
            $errors[] = "Invalid state_id: {$record->state_id}";
        }
        if (!empty($record->lga_id) && !DB::table('ou_lgas')->where('id', $record->lga_id)->exists()) {
            $errors[] = "Invalid lga_id: {$record->lga_id}";
        }
        if (!empty($record->ward_id) && !DB::table('ou_wards')->where('id', $record->ward_id)->exists()) {
            $errors[] = "Invalid ward_id: {$record->ward_id}";
        }

        return $errors;
    }
}
