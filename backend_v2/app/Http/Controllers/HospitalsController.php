<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Exports\HFExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Hospital;
use App\Models\HospitalServiceHistory;
use App\Models\StatusTracking;
use Carbon\Carbon;
use Auth;
use App\Models\ApprovalNotifications;


/**
 * HospitalsController
 *
 * @group Administration - Hospitals
 * 
 * APIs for managing hospitals (create, read, update, delete, search, export)
 * 
 * @authenticated
 */
class HospitalsController extends Controller
{

    public function index()
    {
        $perPage = in_array(request('per_page'), [15, 25, 50, 100, 250, 500]) ? (int) request('per_page') : 15;

        if (auth()->user()->hasPermissionTo('lga_1000')) {
            $facilities = DB::table('hospital_details')
                ->leftJoin('ou_states', 'hospital_details.state_id', '=', 'ou_states.id')
                ->leftJoin('ou_lgas', 'hospital_details.lga_id', '=', 'ou_lgas.id')
                ->leftJoin('ou_wards', 'hospital_details.ward_id', '=', 'ou_wards.id')
                ->leftJoin('lst_facility_types', 'hospital_details.facility_type_id', '=', 'lst_facility_types.id')
                ->leftJoin('lst_level_of_care', 'hospital_details.facility_level_id', '=', 'lst_level_of_care.id')
                ->leftJoin('lst_ownerships', 'hospital_details.ownership_id', '=', 'lst_ownerships.id')
                ->select(
                    'hospital_details.*',
                    'ou_states.name as state',
                    'ou_lgas.name as lga',
                    'ou_wards.name as ward',
                    'lst_facility_types.name as facility_type_name',
                    'lst_level_of_care.name as facility_level',
                    'lst_ownerships.name as ownership',
                )
                ->where('hospital_details.state_id', 'like', '%' .  Auth::user()->state_id . '%')
                ->orderBy('hospital_details.state_id')
                ->orderBy('hospital_details.lga_id')
                ->orderBy('hospital_details.ward_id')
                ->orderBy('hospital_details.facility_name')
                ->paginate($perPage)->appends(['per_page' => $perPage]);
        } else {
            $facilities = DB::table('hospital_details')
                ->leftJoin('ou_states', 'hospital_details.state_id', '=', 'ou_states.id')
                ->leftJoin('ou_lgas', 'hospital_details.lga_id', '=', 'ou_lgas.id')
                ->leftJoin('ou_wards', 'hospital_details.ward_id', '=', 'ou_wards.id')
                ->leftJoin('lst_facility_types', 'hospital_details.facility_type_id', '=', 'lst_facility_types.id')
                ->leftJoin('lst_level_of_care', 'hospital_details.facility_level_id', '=', 'lst_level_of_care.id')
                ->leftJoin('lst_ownerships', 'hospital_details.ownership_id', '=', 'lst_ownerships.id')
                ->select(
                    'hospital_details.*',
                    'ou_states.name as state',
                    'ou_lgas.name as lga',
                    'ou_wards.name as ward',
                    'lst_facility_types.name as facility_type_name',
                    'lst_level_of_care.name as facility_level',
                    'lst_ownerships.name as ownership',
                )
                ->where('hospital_details.state_id', 'like', '%' .  Auth::user()->state_id . '%')
                ->whereIn('hospital_details.lga_id', auth()->user()->getDirectPermissions()->pluck('id')->toArray())
                ->orderBy('hospital_details.state_id')
                ->orderBy('hospital_details.lga_id')
                ->orderBy('hospital_details.ward_id')
                ->orderBy('hospital_details.facility_name')
                ->paginate($perPage)->appends(['per_page' => $perPage]);
        }


        // dd($facilities);
        return view('hospitals.index', compact('facilities'));
    }


    public function create()
    {
        $lst_services = DB::table('lst_hosp_services')
            ->get();

        return view('hospitals.create', compact('lst_services'));
    }

    public function store(Request $request)
    {


        $request->validate([
            'registration_no' => 'nullable|max:20',
            'start_date' => 'required|date',
            'facility_name' => 'required|max:200',
            'alt_facility_name' => 'nullable|max:200',
            'state_id' => 'required',
            'lga_id' => 'required',
            'ward_id' => 'required',
            'state_unique_id' => 'nullable|max:50',
            'ownership_id' => 'required',
            'ownership_type_id' => 'required',
            'facility_level_id' => 'required',
            'facility_level_option_id' => 'nullable',
            'facility_type_id' => 'nullable',
            'longitude' => 'nullable|numeric|between:2.483,20',
            'latitude' => 'nullable|numeric|between:3.883,13.867',
            'physical_location' => 'nullable|max:100',
            'postal_address' => 'nullable|max:100',
            'phone_number' => 'nullable|min:13',
            'alternate_number' => 'nullable|min:13',
            'email_address' => 'nullable|email',
            'website' => 'nullable|url|max:100',
            'operational_days' => 'nullable',
            'operational_hours' => 'nullable',
            'operational_status_id' => 'required',
            'registration_status_id' => 'nullable',
            'license_status_id' => 'nullable',
            'doctors' => 'nullable|numeric',
            'dentist' => 'nullable|numeric',
            'pharmacists' => 'nullable|numeric',
            'pharmacy_technicians' => 'nullable|numeric',
            'nurses' => 'nullable|numeric',
            'lab_scientists' => 'nullable|numeric',
            'midwifes' => 'nullable|numeric',
            'lab_technicians' => 'nullable|numeric',
            'nurse_midwife' => 'nullable|numeric',
            'him_officers' => 'nullable|numeric',
            'community_health_officer' => 'nullable|numeric',
            'community_extension_workers' => 'nullable|numeric',
            'jun_community_extension_worker' => 'nullable|numeric',
            'attendants' => 'nullable|numeric',
            'dental_technicians' => 'nullable|numeric',
            'env_health_officers' => 'nullable|numeric',
            'onsite_laboratory' => 'nullable',
            'onsite_imaging' => 'nullable',
            'onsite_pharmarcy' => 'nullable',
            'mortuary_services' => 'nullable',
            'ambulance' => 'nullable',
            'beds' => 'nullable|numeric',
            'outpatient' => 'nullable',
            'inpatient' => 'nullable',

            // 'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);


        $start_date = date('Y-m-d', strtotime(str_replace('-', '/', $request->start_date)));

        if ($request->operational_status_id > 4) {
            $close_date = date('Y-m-d', strtotime(str_replace('-', '/', $request->close_date)));
        } else {
            $close_date = null;
        }


        $imagePaths = [];

        // Check if there are files to upload
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = env('APP_URL') . "/storage/" . $image->store('hospitals', 'public');
            }
        }

        //Log::info(json_encode($request->all()));


        // dd($request->all());
        $hosp = new Hospital();

        $hosp->fill($request->except(['images']));

        // $hosp->fill($request->all());
        $hosp->id = $hosp->generateUID();
        $hosp->unique_id = $hosp->generateFacilityCode($request->lga_id, '1', $request->facility_level_id, $request->ownership_id);
        $hosp->start_date = $start_date;
        $hosp->close_date = $close_date;
        $hosp->requested_at = Carbon::now()->format('Y-m-d H:i:s');
        $hosp->status_id = 1;
        $hosp->action = 'CREATE FACILITY';
        $hosp->created_by = Auth::user()->id;
        $hosp->requested_by = Auth::user()->id;
        $hosp->facility_type_id = $request->facility_level_id;


        $hosp->image_url = json_encode($imagePaths);

        $hosp->operational_days = $hosp->arrayValuesTostring($request->operational_days);


        DB::beginTransaction();
        try {
            $hosp->save();


            //get id of inserted record
            $hosp_id = $hosp->id;

            //insert in status tracking table
            $status = new StatusTracking;
            $status->hospital_id = $hosp_id;
            $status->user_id = Auth::user()->id;
            $status->status_id = 1;
            $status->created_at = Carbon::now()->format('Y-m-d H:i:s');
            $status->save();

            //insert services
            $services = $request->services;

            if (!empty($services)) {
                foreach ($services as $id) {
                    $hosp_services = new HospitalServiceHistory;
                    $hosp_services->service_id = $id;
                    $hosp_services->hospital_id = $hosp_id;
                    $hosp_services->save();
                }
            }
            DB::commit();
        } catch (\Exception $ex) {
            // dd($ex->getMessage());
            DB::rollback();
            return response()->json(['error' => $ex->getMessage()], 500);
        }


        //****** Send Notifications *********
        if (config('hfr.notify_verifier')) {
            $notify = new ApprovalNotifications;
            $notify->sendFacilityCreateRequestNotification($request->state_id);
        }


        session()->flash("alert-success", "Request Sent Successfully!");
        return redirect()->route('hospitals.index');
    }



    public function edit($id)
    {
        $hosp = Hospital::findorfail($id);
        $hosp_his = Hospital::findorfail($id);
        $status = $hosp_his['status_id'];

        $services = DB::table('hs_hospital_services')
            ->select('service_id')
            ->where('hospital_id', '=', $id)
            ->get();

        $current_services = [];
        foreach ($services as $s) {
            $current_services[] = $s->service_id;
        }

        //get hospital services
        $lst_services = DB::table('lst_hosp_services')->get();


        return view('hospitals.edit', compact('hosp', 'current_services', 'lst_services', 'status'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'registration_no' => 'nullable|max:20',
            'start_date' => 'required|date',
            'facility_name' => 'required|max:200',
            'alt_facility_name' => 'nullable|max:200',
            'state_id' => 'required',
            'lga_id' => 'required',
            'ward_id' => 'required',
            'state_unique_id' => 'nullable|max:50',
            'ownership_id' => 'required',
            'ownership_type_id' => 'required',
            'facility_level_id' => 'required',
            'facility_level_option_id' => 'nullable',
            'longitude' => 'nullable|numeric|between:2.483,20',
            'latitude' => 'nullable|numeric|between:3.883,13.867',
            'physical_location' => 'nullable|max:100',
            'postal_address' => 'nullable|max:100',
            'phone_number' => 'nullable|max:20',
            'alternate_number' => 'nullable|max:20',
            'email_address' => 'nullable|email',
            'website' => 'nullable|url|max:100',
            'operational_days' => 'nullable',
            'operational_hours' => 'nullable',
            'operational_status_id' => 'required',
            'registration_status_id' => 'nullable',
            'license_status_id' => 'nullable',
            'doctors' => 'nullable|numeric',
            'pharmacists' => 'nullable|numeric',
            'pharmacy_technicians' => 'nullable|numeric',
            'nurses' => 'nullable|numeric',
            'lab_scientists' => 'nullable|numeric',
            'midwifes' => 'nullable|numeric',
            'lab_technicians' => 'nullable|numeric',
            'nurse_midwife' => 'nullable|numeric',
            'him_officers' => 'nullable|numeric',
            'community_health_officer' => 'nullable|numeric',
            'community_extension_workers' => 'nullable|numeric',
            'jun_community_extension_worker' => 'nullable|numeric',
            'attendants' => 'nullable|numeric',
            'dental_technicians' => 'nullable|numeric',
            'env_health_officers' => 'nullable|numeric',
            'onsite_laboratory' => 'nullable',
            'onsite_imaging' => 'nullable',
            'onsite_pharmarcy' => 'nullable',
            'mortuary_services' => 'nullable',
            'ambulance' => 'nullable',
            'beds' => 'nullable|numeric',
            'outpatient' => 'nullable',
            'inpatient' => 'nullable',
        ]);

        if ($request->operational_status_id > 4) {
            $close_date = date('Y-m-d', strtotime(str_replace('-', '/', $request->close_date)));
        } else {
            $close_date = null;
        }


        $imagePaths = [];

        // Check if there are files to upload
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = env('APP_URL') . "/storage/" . $image->store('hospitals', 'public');
            }
        }

        // \Log::info(json_encode($imagePaths));


        //update records in history with new changes
        $hosp = new Hospital;
        $hosp = Hospital::findOrFail($id);
        $state_id = $hosp['state_id'];

        // $hosp->fill($request->all());
        $hosp->fill($request->except(['images']));

        $hosp->status_id = 8;
        $hosp->requested_by = Auth::user()->id;
        $hosp->requested_at = Carbon::now()->format('Y-m-d H:i:s');
        $hosp->request_note = '';
        $hosp->verified_by = $request->verified_by;
        $hosp->verified_at = $request->verified_at;
        $hosp->verify_note = '';
        $hosp->validated_by = $request->validated_by;
        $hosp->validated_at = $request->validated_at;
        $hosp->validate_note = '';
        $hosp->published_by = $request->published_by;
        $hosp->published_at = $request->published_at;
        $hosp->publish_note = '';
        $hosp->start_date = date('Y-m-d', strtotime(str_replace('-', '/', $request->start_date)));
        $hosp->close_date = $close_date;
        $hosp->operational_days = $hosp->arrayValuesTostring($request->operational_days);

        //insert in status tracking
        $status = new StatusTracking;
        $status->hospital_id = $id;
        $status->user_id = Auth::user()->id;
        $status->status_id = 8;
        $status->created_at = Carbon::now()->format('Y-m-d H:i:s');

        $hosp->image_url = json_encode($imagePaths);

        //get services before update
        $services = DB::table('hs_hospital_services')
            ->select('service_id')
            ->where('hospital_id', '=', $id)
            ->get();

        $services_before = [];
        foreach ($services as $s) {
            $services_before[] = $s->service_id;
        }

        if (empty($request->services)) {
            $services_update = [];
        } else {
            $services_update = $request->services;
        }


        DB::beginTransaction();
        try {
            $hosp->save();
            $status->save();

            //update hospital services history if services are updated
            $services_equal = $hosp->array_equal($services_before, $services_update);

            if (!$services_equal) {
                HospitalServiceHistory::where('hospital_id', $id)->delete();

                if (!empty($services_update)) {
                    foreach ($services_update as $service_id) {
                        $hosp_services = new HospitalServiceHistory;
                        $hosp_services->service_id = $service_id;
                        $hosp_services->hospital_id = $id;
                        $hosp_services->save();
                    }
                }
            }

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['error' => $ex->getMessage()], 500);
        }

        //****** send notifications *********
        if (config('hfr.notify_verifier')) {
            $notify = new ApprovalNotifications;
            $notify->sendFacilityUpdateRequestNotification($state_id);
        }


        session()->flash("alert-success", "Request Sent Successfully!");
        return redirect()->route('hospitals.index');
    }

    public function adminUpdate(Request $request)
    {
        $request->validate([
            'facility_name_x' => 'required|max:200',
            'alt_facility_name_x' => 'nullable|max:200',
            'longitude_x' => 'nullable|numeric|between:2.483,20',
            'latitude_x' => 'nullable|numeric|between:3.883,13.867',
            'physical_location_x' => 'nullable|max:100',
            'postal_address_x' => 'nullable|max:100',
        ]);


        $hospH = new Hospital;
        $hosp = new Hospital;
        $hospH = Hospital::findOrFail($request->facility_id_x);
        $hosp = Hospital::findOrFail($request->facility_id_x);


        DB::beginTransaction();
        try {
            $hosp->facility_name = $request->facility_name_x;
            $hosp->alt_facility_name = $request->alt_facility_name_x;
            $hosp->longitude = $request->longitude_x;
            $hosp->latitude = $request->latitude_x;
            $hosp->physical_location = $request->physical_location_x;
            $hosp->postal_address = $request->postal_address_x;
            $hosp->save();

            $hospH->facility_name = $request->facility_name_x;
            $hospH->alt_facility_name = $request->alt_facility_name_x;
            $hospH->longitude = $request->longitude_x;
            $hospH->latitude = $request->latitude_x;
            $hospH->physical_location = $request->physical_location_x;
            $hospH->postal_address = $request->postal_address_x;
            $hospH->save();

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['error' => $ex->getMessage()], 500);
        }

        session()->flash("alert-success", "Facility Updated Successfully!");
        return redirect()->route('hospitals.index');
    }


    public function InitiateDelete(Request $request)
    {
        $hs_tracking = new StatusTracking;
        $hs_tracking->hospital_id = $request->facility_id;
        $hs_tracking->user_id = Auth::user()->id;
        $hs_tracking->status_id = '15';
        $hs_tracking->note = $request->reason;
        $hs_tracking->created_at = Carbon::now()->format('Y-m-d H:i:s');

        $hosp = new Hospital;
        $hosp = Hospital::findOrFail($request->facility_id);
        $state_id = $hosp['state_id'];
        $hosp->status_id = '15';
        $hosp->action = 'DELETE FACILITY';
        $hosp->requested_at = Carbon::now()->format('Y-m-d H:i:s');
        $hosp->requested_by = Auth::user()->id;
        $hosp->request_note = $request->reason;
        $hosp->verified_by = $request->null;
        $hosp->verified_at = $request->null;
        $hosp->verify_note = $request->null;
        $hosp->validated_by = $request->null;
        $hosp->validated_at = $request->null;
        $hosp->validate_note = $request->null;
        $hosp->published_by = $request->null;
        $hosp->published_at = $request->null;
        $hosp->publish_note = $request->null;

        DB::beginTransaction();
        try {
            Hospital::disableAuditing();
            $hs_tracking->save();
            $hosp->save();
            Hospital::enableAuditing();

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['error' => $ex->getMessage()], 500);
        }

        //****** send notifications *********
        if (config('hfr.notify_verifier')) {
            $notify = new ApprovalNotifications;
            $notify->sendFacilityDeleteRequestNotification($state_id);
        }


        session()->flash("alert-success", "Delete request initiated successfully!");
        return redirect()->route('hospitals.index');
    }


    /**
     * Build a filtered query for hospital_details based on search parameters.
     * Mirrors the same filter logic used in search().
     */
    private function buildFilteredHospitalQuery(Request $request)
    {
        $query = DB::table('hospital_details');

        if (!empty($request->state_id)) {
            $query->where('hospital_details.state_id', $request->state_id);
        }
        if (!empty($request->lga_id) && $request->lga_id != 1) {
            $query->where('hospital_details.lga_id', $request->lga_id);
        }
        if (!empty($request->ward_id) && $request->ward_id != 0) {
            $query->where('hospital_details.ward_id', $request->ward_id);
        }
        if (!empty($request->facility_level_id) && $request->facility_level_id != 0) {
            $query->where('hospital_details.facility_level_id', $request->facility_level_id);
        }
        if (!empty($request->ownership_id) && $request->ownership_id != 0) {
            $query->where('hospital_details.ownership_id', $request->ownership_id);
        }
        if (!empty($request->filter_operational_status_id) && $request->filter_operational_status_id != 0) {
            $query->where('hospital_details.operational_status_id', $request->filter_operational_status_id);
        }
        if (!empty($request->registration_status_id) && $request->registration_status_id != 0) {
            $query->where('hospital_details.registration_status_id', $request->registration_status_id);
        }
        if (!empty($request->license_status_id) && $request->license_status_id != 0) {
            $query->where('hospital_details.license_status_id', $request->license_status_id);
        }
        if (!empty($request->facility_name)) {
            $query->where('hospital_details.facility_name', 'like', '%' . $request->facility_name . '%');
        }
        if ($request->geo_codes !== null) {
            switch ($request->geo_codes) {
                case 0:
                    $query->where(DB::raw("IFNULL(latitude, '')"), '<>', 'XXX');
                    break;
                case 1:
                    $query->where(DB::raw("IFNULL(latitude, '')"), '<>', '');
                    break;
                case 2:
                    $query->where(DB::raw("IFNULL(latitude, '')"), '=', '');
                    break;
            }
        }

        return $query;
    }


    /**
     * Batch update operational status for selected hospitals.
     */
    public function batchUpdateStatus(Request $request)
    {
        $selectAllFiltered = $request->boolean('select_all_filtered', false);

        if (!$selectAllFiltered) {
            $request->validate([
                'state_id' => 'required|integer',
                'hospital_ids' => 'required|array|min:1',
                'hospital_ids.*' => 'integer',
                'operational_status_id' => 'required|integer',
            ]);
            $hospitalIds = $request->input('hospital_ids');
        } else {
            $request->validate([
                'state_id' => 'required|integer',
                'operational_status_id' => 'required|integer',
            ]);
            $hospitalIds = $this->buildFilteredHospitalQuery($request)
                ->pluck('hospital_details.id')
                ->toArray();
        }

        $newStatusId = $request->input('operational_status_id');
        $stateId = $request->input('state_id');

        DB::beginTransaction();
        try {
            $count = Hospital::whereIn('id', $hospitalIds)
                ->where('state_id', $stateId)
                ->count();

            if ($count === 0) {
                session()->flash('alert-warning', 'No hospitals matched the selection.');
                return redirect()->back();
            }

            Hospital::disableAuditing();

            Hospital::whereIn('id', $hospitalIds)
                ->where('state_id', $stateId)
                ->update([
                    'operational_status_id' => $newStatusId,
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                ]);

            // Insert status tracking for each hospital
            foreach ($hospitalIds as $id) {
                $tracking = new StatusTracking;
                $tracking->hospital_id = $id;
                $tracking->user_id = Auth::user()->id;
                $tracking->status_id = 8;
                $tracking->note = 'Batch operational status update';
                $tracking->created_at = Carbon::now()->format('Y-m-d H:i:s');
                $tracking->save();
            }

            Hospital::enableAuditing();
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            session()->flash('alert-danger', 'Batch update failed: ' . $ex->getMessage());
            return redirect()->back();
        }

        session()->flash('alert-success', "{$count} hospital(s) operational status updated successfully.");
        return redirect()->back();
    }


    /**
     * Batch export selected hospitals to Excel.
     */
    public function batchExport(Request $request)
    {
        $request->validate([
            'state_id' => 'required|integer',
            'hospital_ids' => 'nullable|array',
            'hospital_ids.*' => 'integer',
            'select_all' => 'nullable|boolean',
            'select_all_filtered' => 'nullable|boolean',
        ]);

        $stateId = $request->input('state_id');
        $selectAll = $request->boolean('select_all', false);
        $selectAllFiltered = $request->boolean('select_all_filtered', false);
        $hospitalIds = $request->input('hospital_ids', []);

        $query = DB::table('hospital_details')
            ->leftJoin('ou_states', 'hospital_details.state_id', '=', 'ou_states.id')
            ->leftJoin('ou_lgas', 'hospital_details.lga_id', '=', 'ou_lgas.id')
            ->leftJoin('ou_wards', 'hospital_details.ward_id', '=', 'ou_wards.id')
            ->leftJoin('lst_facility_types', 'hospital_details.facility_type_id', '=', 'lst_facility_types.id')
            ->leftJoin('lst_level_of_care', 'hospital_details.facility_level_id', '=', 'lst_level_of_care.id')
            ->leftJoin('lst_ownerships', 'hospital_details.ownership_id', '=', 'lst_ownerships.id')
            ->leftJoin('lst_ownership_types', 'hospital_details.ownership_type_id', '=', 'lst_ownership_types.id')
            ->leftJoin('lst_level_of_care_options', 'hospital_details.facility_level_option_id', '=', 'lst_level_of_care_options.id')
            ->leftJoin('lst_oparational_status', 'hospital_details.operational_status_id', '=', 'lst_oparational_status.id')
            ->leftJoin('lst_registration_status', 'hospital_details.registration_status_id', '=', 'lst_registration_status.id')
            ->leftJoin('lst_license_status', 'hospital_details.license_status_id', '=', 'lst_license_status.id')
            ->select(
                'ou_states.name as state',
                'ou_lgas.name as lga',
                'ou_wards.name as ward',
                'hospital_details.id',
                'hospital_details.unique_id',
                'hospital_details.facility_name',
                'hospital_details.registration_no',
                'hospital_details.start_date',
                'hospital_details.close_date',
                'lst_ownerships.name as ownership',
                'lst_ownership_types.type as ownership_type',
                'lst_level_of_care.name as facility_level',
                'lst_level_of_care_options.description as facility_level_option',
                'hospital_details.longitude',
                'hospital_details.latitude',
                'lst_oparational_status.status as operation_status',
                'lst_registration_status.status as registration_status',
                'lst_license_status.status as license_status',
                'hospital_details.created_at',
                'hospital_details.updated_at'
            )
            ->where('hospital_details.state_id', $stateId);

        // Apply additional filters when select_all_filtered is on
        if ($selectAllFiltered || $selectAll) {
            if (!empty($request->lga_id) && $request->lga_id != 1) {
                $query->where('hospital_details.lga_id', $request->lga_id);
            }
            if (!empty($request->ward_id) && $request->ward_id != 0) {
                $query->where('hospital_details.ward_id', $request->ward_id);
            }
            if (!empty($request->facility_level_id) && $request->facility_level_id != 0) {
                $query->where('hospital_details.facility_level_id', $request->facility_level_id);
            }
            if (!empty($request->ownership_id) && $request->ownership_id != 0) {
                $query->where('hospital_details.ownership_id', $request->ownership_id);
            }
            if (!empty($request->filter_operational_status_id) && $request->filter_operational_status_id != 0) {
                $query->where('hospital_details.operational_status_id', $request->filter_operational_status_id);
            }
            if (!empty($request->registration_status_id) && $request->registration_status_id != 0) {
                $query->where('hospital_details.registration_status_id', $request->registration_status_id);
            }
            if (!empty($request->license_status_id) && $request->license_status_id != 0) {
                $query->where('hospital_details.license_status_id', $request->license_status_id);
            }
            if (!empty($request->facility_name)) {
                $query->where('hospital_details.facility_name', 'like', '%' . $request->facility_name . '%');
            }
            if ($request->geo_codes !== null) {
                switch ($request->geo_codes) {
                    case 0:
                        $query->where(DB::raw("IFNULL(latitude, '')"), '<>', 'XXX');
                        break;
                    case 1:
                        $query->where(DB::raw("IFNULL(latitude, '')"), '<>', '');
                        break;
                    case 2:
                        $query->where(DB::raw("IFNULL(latitude, '')"), '=', '');
                        break;
                }
            }
        } elseif (!empty($hospitalIds)) {
            $query->whereIn('hospital_details.id', $hospitalIds);
        }

        $facilities = $query->orderBy('ou_states.name')
            ->orderBy('ou_lgas.name')
            ->orderBy('hospital_details.facility_name')
            ->get();

        if ($facilities->isEmpty()) {
            session()->flash('alert-warning', 'No hospitals to export.');
            return redirect()->back();
        }

        $column_header = [
            "state", "lga", "ward", "uid", "facility_code", "facility_name",
            "reg_number", "start_date", "close_date", "ownership", "ownership_type",
            "facility_level", "facility_level_option", "longitude", "latitude",
            "operation_status", "registration_status", "license_status", "created", "last_updated"
        ];

        $stateName = DB::table('ou_states')->where('id', $stateId)->value('name') ?? 'selected';
        $filename = 'hospitals_' . str_replace(' ', '_', $stateName) . '_' . date('Y-m-d') . '.xlsx';

        return Excel::download(new HFExport($facilities->all(), $column_header), $filename);
    }


    /**
     * Batch delete (initiate delete request) for selected hospitals.
     */
    public function batchDelete(Request $request)
    {
        $selectAllFiltered = $request->boolean('select_all_filtered', false);

        if (!$selectAllFiltered) {
            $request->validate([
                'state_id' => 'required|integer',
                'hospital_ids' => 'required|array|min:1',
                'hospital_ids.*' => 'integer',
            ]);
            $hospitalIds = $request->input('hospital_ids');
        } else {
            $request->validate([
                'state_id' => 'required|integer',
            ]);
            $hospitalIds = $this->buildFilteredHospitalQuery($request)
                ->pluck('hospital_details.id')
                ->toArray();
        }

        $stateId = $request->input('state_id');

        $hospitals = Hospital::where('state_id', $stateId)
            ->whereIn('id', $hospitalIds)
            ->get();

        if ($hospitals->isEmpty()) {
            session()->flash('alert-warning', 'No hospitals found matching the selection.');
            return redirect()->back();
        }

        DB::beginTransaction();
        try {
            Hospital::disableAuditing();

            foreach ($hospitals as $hosp) {
                // Remove related status tracking records
                StatusTracking::where('hospital_id', $hosp->id)->delete();

                // Remove related hospital services
                DB::table('hs_hospital_services')->where('hospital_id', $hosp->id)->delete();
                DB::table('hs_hospital_services_history')->where('hospital_id', $hosp->id)->delete();

                // Delete the hospital record
                $hosp->delete();
            }

            Hospital::enableAuditing();
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            session()->flash('alert-danger', 'Batch delete failed: ' . $ex->getMessage());
            return redirect()->back();
        }

        $count = $hospitals->count();

        session()->flash('alert-success', "{$count} hospital(s) deleted successfully.");
        return redirect()->back();
    }


    public function searchold(Request $request)
    {
        $state_id = $request->state_id;
        $lga_id = $request->lga_id;
        $ward_id = $request->ward_id;
        $facility_name = $request->facility_name;
        $geo_codes = $request->geo_codes;
        $facility_level_id = $request->facility_level_id;
        $ownership_id = $request->ownership_id;
        $operational_status_id = $request->operational_status_id;
        $registration_status_id = $request->registration_status_id;
        $license_status_id = $request->license_status_id;


        if ($geo_codes == 0) {
            $cond = "<>";
            $value = 'XXX';
        }
        if ($geo_codes == 1) {
            $cond = "<>";
            $value = '';
        }
        if ($geo_codes == 2) {
            $cond = "=";
            $value = '';
        }


        if ($ward_id == 0) {
            $ward_id = '';
        }
        if ($facility_level_id == 0) {
            $facility_level_id = '';
        }
        if ($ownership_id == 0) {
            $ownership_id = '';
        }
        if ($operational_status_id == 0) {
            $operational_status_id = '';
        }
        if ($registration_status_id == 0) {
            $registration_status_id = '';
        }
        if ($license_status_id == 0) {
            $license_status_id = '';
        }


        // $facilities = DB::table('hospital_details')
        $facilities = DB::table('hospital_details')
            ->leftJoin('ou_states', 'hospital_details.state_id', '=', 'ou_states.id')
            ->leftJoin('ou_lgas', 'hospital_details.lga_id', '=', 'ou_lgas.id')
            ->leftJoin('ou_wards', 'hospital_details.ward_id', '=', 'ou_wards.id')
            ->leftJoin('lst_facility_types', 'hospital_details.facility_type_id', '=', 'lst_facility_types.id')
            ->leftJoin('lst_level_of_care', 'hospital_details.facility_level_id', '=', 'lst_level_of_care.id')
            ->leftJoin('lst_ownerships', 'hospital_details.ownership_id', '=', 'lst_ownerships.id')
            ->select(
                'hospital_details.*',
                'ou_states.name as state_name',
                'ou_lgas.name as lga_name',
                'ou_wards.name as ward_name',
                'lst_facility_types.name as facility_type_name',
                'lst_level_of_care.name as facility_level_name',
                'lst_ownerships.name as ownership_name',
            )
            ->where('hospital_details.state_id', 'like', '%' . $state_id . '%')
            ->where('hospital_details.lga_id', 'like', '%' . $lga_id . '%')
            ->where(DB::raw("IFNULL(hospital_details.ward_id, '')"), 'like', '%' . $ward_id . '%')
            ->where('hospital_details.facility_level_id', 'like', '%' . $facility_level_id . '%')
            ->where('hospital_details.ownership_id', 'like', '%' . $ownership_id . '%')
            ->where('hospital_details.operational_status_id', 'like', '%' . $operational_status_id . '%')
            ->where('hospital_details.registration_status_id', 'like', '%' . $registration_status_id . '%')
            ->where('hospital_details.license_status_id', 'like', '%' . $license_status_id . '%')
            ->where('hospital_details.facility_name', 'like', '%' .  $facility_name . '%')
            ->where(DB::raw("IFNULL(latitude, '')"), $cond, $value)

            ->orderBy('hospital_details.state_id')
            ->orderBy('hospital_details.lga_id')
            ->orderBy('hospital_details.ward_id')
            ->orderBy('hospital_details.facility_name')
            ->paginate(15)
            ->appends($request->all());

        $request->flash('request', $request);

        return view('hospitals.index', compact('facilities'));
    }


    public function search(Request $request)
    {
        $perPage = in_array($request->per_page, [15, 25, 50, 100, 250, 500]) ? (int) $request->per_page : 15;
        $query = DB::table('hospital_details')
            ->leftJoin('ou_states', 'hospital_details.state_id', '=', 'ou_states.id')
            ->leftJoin('ou_lgas', 'hospital_details.lga_id', '=', 'ou_lgas.id')
            ->leftJoin('ou_wards', 'hospital_details.ward_id', '=', 'ou_wards.id')
            ->leftJoin('lst_facility_types', 'hospital_details.facility_type_id', '=', 'lst_facility_types.id')
            ->leftJoin('lst_level_of_care', 'hospital_details.facility_level_id', '=', 'lst_level_of_care.id')
            ->leftJoin('lst_ownerships', 'hospital_details.ownership_id', '=', 'lst_ownerships.id')
            ->select(
                'hospital_details.*',
                'ou_states.name as state',
                'ou_lgas.name as lga',
                'ou_wards.name as ward',
                'lst_ownerships.name as ownership',
                'lst_facility_types.name as facility_type_name',
                'lst_level_of_care.name as facility_level'
            );

        // Apply filters dynamically
        if (!empty($request->state_id)) {
            $query->where('hospital_details.state_id', $request->state_id);
        }

        if (!empty($request->lga_id)) {
            $query->where('hospital_details.lga_id', $request->lga_id);
        }

        if (!empty($request->ward_id) && $request->ward_id != 0) {
            $query->where('hospital_details.ward_id', $request->ward_id);
        }

        if (!empty($request->facility_level_id) && $request->facility_level_id != 0) {
            $query->where('hospital_details.facility_level_id', $request->facility_level_id);
        }

        if (!empty($request->ownership_id) && $request->ownership_id != 0) {
            $query->where('hospital_details.ownership_id', $request->ownership_id);
        }

        if (!empty($request->operational_status_id) && $request->operational_status_id != 0) {
            $query->where('hospital_details.operational_status_id', $request->operational_status_id);
        }

        if (!empty($request->registration_status_id) && $request->registration_status_id != 0) {
            $query->where('hospital_details.registration_status_id', $request->registration_status_id);
        }

        if (!empty($request->license_status_id) && $request->license_status_id != 0) {
            $query->where('hospital_details.license_status_id', $request->license_status_id);
        }

        if (!empty($request->facility_name)) {
            $query->where('hospital_details.facility_name', 'like', '%' . $request->facility_name . '%');
        }

        // Handle geo codes condition
        if ($request->geo_codes !== null) {
            switch ($request->geo_codes) {
                case 0:
                    $query->where(DB::raw("IFNULL(latitude, '')"), '<>', 'XXX');
                    break;
                case 1:
                    $query->where(DB::raw("IFNULL(latitude, '')"), '<>', '');
                    break;
                case 2:
                    $query->where(DB::raw("IFNULL(latitude, '')"), '=', '');
                    break;
            }
        }

        $facilities = $query->orderBy('hospital_details.state_id')
            ->orderBy('hospital_details.lga_id')
            ->orderBy('hospital_details.ward_id')
            ->orderBy('hospital_details.facility_name')
            ->paginate($perPage)
            ->appends($request->all());

        $request->flash();

        return view('hospitals.index', compact('facilities'));
    }


    public function getServices(Request $request)
    {
        $services = DB::select("select s.service_category_id category_id,s.name from hs_hospital_services hs
            join lst_hosp_services s on hs.service_id=s.id
            where hospital_id='" . $request->hosp_id . "'");

        return $services;
    }

    public function getServicesHistory(Request $request)
    {
        $services = DB::select("select s.service_category_id category_id,s.name from hs_hospital_services_history hs
            join lst_hosp_services s on hs.service_id=s.id
            where hospital_id='" . $request->hosp_id . "'");

        return $services;
    }


    public function exportOLD(Request $request)
    {

        $state_id = $request->state_id;
        $lga_id = $request->lga_id;
        $ward_id = $request->ward_id;
        $facility_name = $request->facility_name;
        $geo_codes = $request->geo_codes;
        $facility_level_id = $request->facility_level_id;
        $ownership_id = $request->ownership_id;
        $operational_status_id = $request->operational_status_id;
        $registration_status_id = $request->registration_status_id;
        $license_status_id = $request->license_status_id;


        if ($geo_codes == 0) {
            $cond = "<>";
            $value = 'XXX';
        }
        if ($geo_codes == 1) {
            $cond = "<>";
            $value = '';
        }
        if ($geo_codes == 2) {
            $cond = "=";
            $value = '';
        }


        if ($ward_id == 0) {
            $ward_id = '';
        }
        if ($facility_level_id == 0) {
            $facility_level_id = '';
        }
        if ($ownership_id == 0) {
            $ownership_id = '';
        }
        if ($operational_status_id == 0) {
            $operational_status_id = '';
        }
        if ($registration_status_id == 0) {
            $registration_status_id = '';
        }
        if ($license_status_id == 0) {
            $license_status_id = '';
        }

        $facilities = DB::table('hospital_details')
            ->leftJoin('ou_states', 'hospital_details.state_id', '=', 'ou_states.id')
            ->leftJoin('ou_lgas', 'hospital_details.lga_id', '=', 'ou_lgas.id')
            ->leftJoin('ou_wards', 'hospital_details.ward_id', '=', 'ou_wards.id')
            ->leftJoin('lst_facility_types', 'hospital_details.facility_type_id', '=', 'lst_facility_types.id')
            ->leftJoin('lst_level_of_care', 'hospital_details.facility_level_id', '=', 'lst_level_of_care.id')
            ->leftJoin('lst_ownerships', 'hospital_details.ownership_id', '=', 'lst_ownerships.id')
            ->leftJoin('lst_ownership_types', 'hospital_details.ownership_type_id', '=', 'lst_ownership_types.id')
            ->leftJoin('lst_level_of_care_options', 'hospital_details.facility_level_option_id', '=', 'lst_level_of_care_options.id')
            ->leftJoin('lst_oparational_status', 'hospital_details.operational_status_id', '=', 'lst_oparational_status.id')
            ->leftJoin('lst_registration_status', 'hospital_details.registration_status_id', '=', 'lst_registration_status.id')
            ->leftJoin('lst_license_status', 'hospital_details.license_status_id', '=', 'lst_license_status.id')
            ->select(
                'ou_states.name as state',
                'ou_lgas.name as lga',
                'ou_wards.name as ward',
                'hospital_details.id',
                'hospital_details.unique_id',
                'hospital_details.facility_name',
                'hospital_details.registration_no',
                'hospital_details.start_date',
                'hospital_details.close_date',
                'lst_ownerships.name as ownership',
                'lst_ownership_types.type as ownership_type',
                'lst_level_of_care.name as facility_level',
                'lst_level_of_care_options.description as facility_level_option',
                'hospital_details.longitude',
                'hospital_details.latitude',
                'lst_oparational_status.status as operation_status',
                'lst_registration_status.status as registration_status',
                'lst_license_status.status as license_status',
                'hospital_details.created_at',
                'hospital_details.updated_at'
            )
            ->where('hospital_details.state_id', 'like', '%' . $state_id . '%')
            ->where('hospital_details.lga_id', 'like', '%' . $lga_id . '%')
            ->where(DB::raw("IFNULL(hospital_details.ward_id, '')"), 'like', '%' . $ward_id . '%')
            ->where('hospital_details.facility_level_id', 'like', '%' . $facility_level_id . '%')
            ->where('hospital_details.ownership_id', 'like', '%' . $ownership_id . '%')
            ->where('hospital_details.operational_status_id', 'like', '%' . $operational_status_id . '%')
            ->where('hospital_details.registration_status_id', 'like', '%' . $registration_status_id . '%')
            ->where('hospital_details.license_status_id', 'like', '%' . $license_status_id . '%')
            ->where('hospital_details.facility_name', 'like', '%' . $facility_name . '%')
            ->where(DB::raw("IFNULL(hospital_details.latitude, '')"), $cond, $value)
            ->orderBy('ou_states.name')
            ->orderBy('ou_lgas.name')
            ->orderBy('hospital_details.facility_name')
            ->get();


        $column_header = array(
            "state",
            "lga",
            "ward",
            "uid",
            "facility_code",
            "facility_name",
            "reg_number",
            "start_date",
            "close_date",
            "ownership",
            "ownership_type",
            "facility_level",
            "facility_level_option",
            "longitude",
            "latitude",
            "operation_status",
            "registration_status",
            "license_status",
            "created",
            "last_updated"
        );

        return Excel::download(new HFExport($facilities->all(), $column_header), 'data.xlsx');
    }


    public function export(Request $request)
    {
        $query = DB::table('hospital_details')
            ->leftJoin('ou_states', 'hospital_details.state_id', '=', 'ou_states.id')
            ->leftJoin('ou_lgas', 'hospital_details.lga_id', '=', 'ou_lgas.id')
            ->leftJoin('ou_wards', 'hospital_details.ward_id', '=', 'ou_wards.id')
            ->leftJoin('lst_facility_types', 'hospital_details.facility_type_id', '=', 'lst_facility_types.id')
            ->leftJoin('lst_level_of_care', 'hospital_details.facility_level_id', '=', 'lst_level_of_care.id')
            ->leftJoin('lst_ownerships', 'hospital_details.ownership_id', '=', 'lst_ownerships.id')
            ->leftJoin('lst_ownership_types', 'hospital_details.ownership_type_id', '=', 'lst_ownership_types.id')
            ->leftJoin('lst_level_of_care_options', 'hospital_details.facility_level_option_id', '=', 'lst_level_of_care_options.id')
            ->leftJoin('lst_oparational_status', 'hospital_details.operational_status_id', '=', 'lst_oparational_status.id')
            ->leftJoin('lst_registration_status', 'hospital_details.registration_status_id', '=', 'lst_registration_status.id')
            ->leftJoin('lst_license_status', 'hospital_details.license_status_id', '=', 'lst_license_status.id')
            ->select(
                'ou_states.name as state',
                'ou_lgas.name as lga',
                'ou_wards.name as ward',
                'hospital_details.id',
                'hospital_details.unique_id',
                'hospital_details.facility_name',
                'hospital_details.registration_no',
                'hospital_details.start_date',
                'hospital_details.close_date',
                'lst_ownerships.name as ownership',
                'lst_ownership_types.type as ownership_type',
                'lst_level_of_care.name as facility_level',
                'lst_level_of_care_options.description as facility_level_option',
                'hospital_details.longitude',
                'hospital_details.latitude',
                'lst_oparational_status.status as operation_status',
                'lst_registration_status.status as registration_status',
                'lst_license_status.status as license_status',
                'hospital_details.created_at',
                'hospital_details.updated_at'
            );

        // Dynamically add filters if present
        if (!empty($request->state_id)) {
            $query->where('hospital_details.state_id', $request->state_id);
        }
        if (!empty($request->lga_id)) {
            $query->where('hospital_details.lga_id', $request->lga_id);
        }
        if (!empty($request->ward_id) && $request->ward_id != 0) {
            $query->where('hospital_details.ward_id', $request->ward_id);
        }
        if (!empty($request->facility_level_id) && $request->facility_level_id != 0) {
            $query->where('hospital_details.facility_level_id', $request->facility_level_id);
        }
        if (!empty($request->ownership_id) && $request->ownership_id != 0) {
            $query->where('hospital_details.ownership_id', $request->ownership_id);
        }
        if (!empty($request->operational_status_id) && $request->operational_status_id != 0) {
            $query->where('hospital_details.operational_status_id', $request->operational_status_id);
        }
        if (!empty($request->registration_status_id) && $request->registration_status_id != 0) {
            $query->where('hospital_details.registration_status_id', $request->registration_status_id);
        }
        if (!empty($request->license_status_id) && $request->license_status_id != 0) {
            $query->where('hospital_details.license_status_id', $request->license_status_id);
        }
        if (!empty($request->facility_name)) {
            $query->where('hospital_details.facility_name', 'like', '%' . $request->facility_name . '%');
        }

        // Geocode filter
        if ($request->geo_codes !== null) {
            switch ($request->geo_codes) {
                case 0:
                    $query->where(DB::raw("IFNULL(latitude, '')"), '<>', 'XXX');
                    break;
                case 1:
                    $query->where(DB::raw("IFNULL(latitude, '')"), '<>', '');
                    break;
                case 2:
                    $query->where(DB::raw("IFNULL(latitude, '')"), '=', '');
                    break;
            }
        }

        $facilities = $query->orderBy('ou_states.name')
            ->orderBy('ou_lgas.name')
            ->orderBy('hospital_details.facility_name')
            ->get();

        $column_header = [
            "state",
            "lga",
            "ward",
            "uid",
            "facility_code",
            "facility_name",
            "reg_number",
            "start_date",
            "close_date",
            "ownership",
            "ownership_type",
            "facility_level",
            "facility_level_option",
            "longitude",
            "latitude",
            "operation_status",
            "registration_status",
            "license_status",
            "created",
            "last_updated"
        ];

        return Excel::download(new HFExport($facilities->all(), $column_header), 'data.xlsx');
    }
}
