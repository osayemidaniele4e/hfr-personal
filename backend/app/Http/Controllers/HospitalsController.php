<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Exports\HFExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Hospital;
use App\HospitalHistory;
use App\HospitalServiceHistory;
use App\StatusTracking;
use Carbon\Carbon;
use Auth;
use App\Notifications\CreateRequest;
use App\Notifications\UpdateRequest;
use App\Notifications\DeleteRequest;
use App\User;
use App\ApprovalNotifications;


class HospitalsController extends Controller
{

    public function index()
    {
        if (auth()->user()->hasPermissionTo('All LGAs')) {
            $facilities = DB::table('hs_hospitals_history')
                ->where('state_id', 'like', '%' .  Auth::user()->state_id . '%')
                ->where(function ($q) {
                    $q->whereIn('hs_hospitals_history.status_id', [0, 6, 13])
                      ->orWhereNull('hs_hospitals_history.status_id');
                })
                ->orderBy('state_id')
                ->orderBy('lga_id')
                ->orderBy('ward_id')
                ->orderBy('facility_name')
                ->paginate(15);
        } else {
            $facilities = DB::table('hs_hospitals_history')
                ->where('state_id', 'like', '%' .  Auth::user()->state_id . '%')
                ->whereIn('lga_id', auth()->user()->getDirectPermissions()->pluck('id')->toArray())
                ->where(function ($q) {
                    $q->whereIn('hs_hospitals_history.status_id', [0, 6, 13])
                      ->orWhereNull('hs_hospitals_history.status_id');
                })
                ->orderBy('state_id')
                ->orderBy('lga_id')
                ->orderBy('ward_id')
                ->orderBy('facility_name')
                ->paginate(15);
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

        \Log::info(json_encode($imagePaths));


        // dd($request->all());
        $hosp = new HospitalHistory;

        $hosp->fill($request->except(['images']));

        // $hosp->fill($request->all());
        $hosp->id = $hosp->generateUID();
        $hosp->unique_id = $hosp->generateFacilityCode($request->lga_id, '1', $request->facility_level_id, $request->ownership_id);
        $hosp->start_date = $start_date;
        $hosp->close_date = $close_date;
        $hosp->requested_at = Carbon::now()->format('Y-m-d H:i:s');
        $hosp->status_id = 1;
        $hosp->created_by = Auth::user()->id;
        $hosp->requested_by = Auth::user()->id;

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
            dd($ex->getMessage());
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
        $hosp_his = HospitalHistory::findorfail($id);
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

        \Log::info(json_encode($imagePaths));


        //update records in history with new changes
        $hosp = new HospitalHistory;
        $hosp = HospitalHistory::findOrFail($id);
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


        $hospH = new HospitalHistory;
        $hosp = new Hospital;
        $hospH = HospitalHistory::findOrFail($request->facility_id_x);
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

        $hosp = new HospitalHistory;
        $hosp = HospitalHistory::findOrFail($request->facility_id);
        $state_id = $hosp['state_id'];
        $hosp->status_id = '15';
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
            HospitalHistory::disableAuditing();
            $hs_tracking->save();
            $hosp->save();
            HospitalHistory::enableAuditing();

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


    public function search(Request $request)
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
            ->where('state_id', 'like', '%' . $state_id . '%')
            ->where('lga_id', 'like', '%' . $lga_id . '%')
            ->where(DB::Raw("IFNULL(ward_id, '')"), 'like', '%' . $ward_id . '%')
            ->where('facility_level_id', 'like', '%' . $facility_level_id . '%')
            ->where('ownership_id', 'like', '%' . $ownership_id . '%')
            ->where('operational_status_id', 'like', '%' . $operational_status_id . '%')
            ->where('registration_status_id', 'like', '%' . $registration_status_id . '%')
            ->where('license_status_id', 'like', '%' . $license_status_id . '%')
            ->Where('facility_name', 'like', '%' .  $facility_name . '%')
            ->where(DB::Raw("IFNULL(latitude, '')"), $cond, $value)
            ->orderBy('state_id')
            ->orderBy('lga_id')
            ->orderBy('ward_id')
            ->orderBy('facility_name')
            ->paginate(15)
            ->appends($request->all());

        $request->flash('request', $request);

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


    public function export(Request $request)
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
            ->select(
                'state',
                'lga',
                'ward',
                'id',
                'unique_id',
                'facility_name',
                'registration_no',
                'start_date',
                'close_date',
                'ownership',
                'ownership_type',
                'facility_level',
                'facility_level_option',
                'longitude',
                'latitude',
                'operation_status',
                'registration_status',
                'license_status',
                'created_at',
                'updated_at'
            )
            ->where('state_id', 'like', '%' . $state_id . '%')
            ->where('lga_id', 'like', '%' . $lga_id . '%')
            ->where(DB::Raw("IFNULL(ward_id, '')"), 'like', '%' . $ward_id . '%')
            ->where('facility_level_id', 'like', '%' . $facility_level_id . '%')
            ->where('ownership_id', 'like', '%' . $ownership_id . '%')
            ->where('operational_status_id', 'like', '%' . $operational_status_id . '%')
            ->where('registration_status_id', 'like', '%' . $registration_status_id . '%')
            ->where('license_status_id', 'like', '%' . $license_status_id . '%')
            ->Where('facility_name', 'like', '%' .  $facility_name . '%')
            ->where(DB::Raw("IFNULL(latitude, '')"), $cond, $value)
            ->orderBy('state')
            ->orderBy('lga')
            ->orderBy('facility_name')
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
}
