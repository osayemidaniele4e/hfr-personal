<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FacilityListingController extends Controller
{

    public function getHospitals()
    {

        $facilities = DB::table('hospital_details')
            ->orderBy('state_id')
            ->orderBy('lga_id')
            ->orderBy('ward_id')
            ->orderBy('facility_name')
            ->paginate(20);

        //set values facility list when no filter
        $data['state_id'] = 1;
        $data['lga_id'] = 1;
        $data['ward_id'] = 0;
        $data['facility_name'] = "";
        $data['geo_codes'] = 0;
        $data['facility_level_id'] = 0;
        $data['ownership_id'] = 0;
        $data['operational_status_id'] = 0;
        $data['registration_status_id'] = 0;
        $data['license_status_id'] = 0;
        $data['service_type'] = 0;
        $data['service_category_id'] = 0;
        $data['searched'] = 0;

        return view('public.list_hospitals', compact('facilities', 'data'));
    }

    public function searchHospitals(Request $request)
    {
        // dd($request->all());
        $ward_id = $request->ward_id;
        $facility_level_id = $request->facility_level_id;
        $ownership_id = $request->ownership_id;
        $operational_status_id = $request->operational_status_id;
        $registration_status_id = $request->registration_status_id;
        $license_status_id = $request->license_status_id;

        if ($request->geo_codes == 0) {
            $cond = "<>";
            $value = 'XXX';
        }
        if ($request->geo_codes == 1) {
            $cond = "<>";
            $value = '';
        }
        if ($request->geo_codes == 2) {
            $cond = "=";
            $value = '';
        }

        if ($request->service_type == 1) {
            $outpatient = 'Yes';
            $inpatient = '';
        } elseif ($request->service_type == 2) {
            $outpatient = '';
            $inpatient = 'Yes';
        } else {
            $outpatient = '';
            $inpatient = '';
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

        if (!empty($request->services)) {
            $hospital = DB::select("SELECT DISTINCT hospital_id FROM hs_hospital_services 
            WHERE service_id IN (" . implode(",", $request->services) . ")");

            $hospital_with_services = [];
            foreach ($hospital as $h) {
                $hospital_with_services[] = $h->hospital_id;
            }
        } else {
            $hospital = DB::select("SELECT id FROM hospital_details");

            $hospital_with_services = [];
            foreach ($hospital as $h) {
                $hospital_with_services[] = $h->id;
            }
        }

        $facilities = DB::table('hospital_details')
            ->where('state_id', 'like', '%' . $request->state_id . '%')
            ->where('lga_id', 'like', '%' . $request->lga_id . '%')
            ->where(DB::Raw("IFNULL(ward_id, '')"), 'like', '%' . $ward_id . '%')
            ->where('facility_level_id', 'like', '%' . $facility_level_id . '%')
            ->where('ownership_id', 'like', '%' . $ownership_id . '%')
            ->where('operational_status_id', 'like', '%' . $operational_status_id . '%')
            ->where('registration_status_id', 'like', '%' . $registration_status_id . '%')
            ->where('license_status_id', 'like', '%' . $license_status_id . '%')
            ->where(DB::Raw("IFNULL(outpatient, '')"), 'like', '%' . $outpatient . '%')
            ->where(DB::Raw("IFNULL(inpatient, '')"), 'like', '%' . $inpatient . '%')
            ->Where('facility_name', 'like', '%' .  $request->facility_name . '%')
            ->where(DB::Raw("IFNULL(latitude, '')"), $cond, $value)
            ->whereIn('id', $hospital_with_services)
            ->orderBy('state')
            ->orderBy('lga')
            ->orderBy('ward')
            ->orderBy('facility_name')
            ->paginate(20)
            ->appends($request->all());


        //return original values from request
        $data['state_id'] = $request->state_id;
        $data['lga_id'] = $request->lga_id;
        $data['ward_id'] = $request->ward_id;
        $data['facility_name'] = $request->facility_name;
        $data['geo_codes'] = $request->geo_codes;
        $data['facility_level_id'] = $request->facility_level_id;
        $data['ownership_id'] = $request->ownership_id;
        $data['operational_status_id'] = $request->operational_status_id;
        $data['registration_status_id'] = $request->registration_status_id;
        $data['license_status_id'] = $request->license_status_id;
        $data['service_type'] = $request->service_type;
        $data['service_category_id'] = $request->service_category_id;
        $data['searched'] = 1;


        return view('public.list_hospitals', compact('facilities', 'data'));
    }


    public function getPharmacy()
    {

        $facilities = DB::table('pharmacy_details')
            ->orderBy('state')
            ->orderBy('lga')
            ->orderBy('facility_name')
            ->paginate(20);

        //set values facility list when no filter
        $data['state_id'] = 1;
        $data['lga_id'] = 1;
        $data['ward_id'] = 1;
        $data['facility_name'] = "";
        $data['geo_codes'] = 0;
        $data['ownership_id'] = 0;
        $data['operational_status_id'] = 0;
        $data['registration_status_id'] = 0;
        $data['license_status_id'] = 0;
        $data['searched'] = 0;

        return view('public.list_pharmacy', compact('facilities', 'data'));
    }

    public function searchPharmacy(Request $request)
    {
        $state_id = $request->state_id;
        $lga_id = $request->lga_id;
        $ward_id = $request->ward_id;
        $facility_name = $request->facility_name;
        $ownership_id = $request->ownership_id;
        $operational_status_id = $request->operational_status_id;
        $registration_status_id = $request->registration_status_id;
        $license_status_id = $request->license_status_id;

        if ($request->geo_codes == 0) {
            $cond = "<>";
            $value = 'XXX';
        }
        if ($request->geo_codes == 1) {
            $cond = "<>";
            $value = '';
        }
        if ($request->geo_codes == 2) {
            $cond = "=";
            $value = '';
        }


        if ($ward_id == 0) {
            $ward_id = '';
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



        $facilities = DB::table('pharmacy_details')
            ->where('state_id', 'like', '%' . $state_id . '%')
            ->where('lga_id', 'like', '%' . $lga_id . '%')
            ->where(DB::Raw("IFNULL(ward_id, '')"), 'like', '%' . $ward_id . '%')
            ->where('ownership_id', 'like', '%' . $ownership_id . '%')
            ->where('operational_status_id', 'like', '%' . $operational_status_id . '%')
            ->where('registration_status_id', 'like', '%' . $registration_status_id . '%')
            ->where('license_status_id', 'like', '%' . $license_status_id . '%')
            ->Where('facility_name', 'like', '%' .  $facility_name . '%')
            ->where('latitude', $cond, $value)
            ->orderBy('state')
            ->orderBy('lga')
            ->orderBy('facility_name')
            ->paginate(20)
            ->appends($request->all());


        //return original values from request
        $data['state_id'] = $request->state_id;
        $data['lga_id'] = $request->lga_id;
        $data['ward_id'] = $request->ward_id;
        $data['facility_name'] = $request->facility_name;
        $data['geo_codes'] = $request->geo_codes;
        $data['ownership_id'] = $request->ownership_id;
        $data['operational_status_id'] = $request->operational_status_id;
        $data['registration_status_id'] = $request->registration_status_id;
        $data['license_status_id'] = $request->license_status_id;
        $data['searched'] = 1;


        return view('public.list_pharmacy', compact('facilities', 'data'));
    }

    public function getLab()
    {

        $facilities = DB::table('laboratory_details')
            ->orderBy('state')
            ->orderBy('lga')
            ->orderBy('facility_name')
            ->paginate(20);

        //set values facility list when no filter
        $data['state_id'] = 1;
        $data['lga_id'] = 1;
        $data['ward_id'] = 1;
        $data['facility_name'] = "";
        $data['geo_codes'] = 0;
        $data['facility_level_id'] = 0;
        $data['ownership_id'] = 0;
        $data['operational_status_id'] = 0;
        $data['registration_status_id'] = 0;
        $data['license_status_id'] = 0;
        $data['accreditation_status_id'] = 0;
        $data['searched'] = 0;

        return view('public.list_labs', compact('facilities', 'data'));
    }

    public function searchLab(Request $request)
    {
        $state_id = $request->state_id;
        $lga_id = $request->lga_id;
        $ward_id = $request->ward_id;
        $facility_name = $request->facility_name;
        $facility_level_id = $request->facility_level_id;
        $ownership_id = $request->ownership_id;
        $operational_status_id = $request->operational_status_id;
        $registration_status_id = $request->registration_status_id;
        $license_status_id = $request->license_status_id;
        $accreditation_status_id = $request->accreditation_status_id;


        if ($request->geo_codes == 0) {
            $cond = "<>";
            $value = 'XXX';
        }
        if ($request->geo_codes == 1) {
            $cond = "<>";
            $value = '';
        }
        if ($request->geo_codes == 2) {
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
        if ($accreditation_status_id == 0) {
            $accreditation_status_id = '';
        }

        $facilities = DB::table('laboratory_details')
            ->where('state_id', 'like', '%' . $state_id . '%')
            ->where('lga_id', 'like', '%' . $lga_id . '%')
            ->where(DB::Raw("IFNULL(ward_id, '')"), 'like', '%' . $ward_id . '%')
            ->where('facility_level_id', 'like', '%' . $facility_level_id . '%')
            ->where('ownership_id', 'like', '%' . $ownership_id . '%')
            ->where('operational_status_id', 'like', '%' . $operational_status_id . '%')
            ->where('registration_status_id', 'like', '%' . $registration_status_id . '%')
            ->where('license_status_id', 'like', '%' . $license_status_id . '%')
            ->where('accreditation_status_id', 'like', '%' . $accreditation_status_id . '%')
            ->Where('facility_name', 'like', '%' .  $facility_name . '%')
            ->where('latitude', $cond, $value)
            ->orderBy('state')
            ->orderBy('lga')
            ->orderBy('facility_name')
            ->paginate(20)
            ->appends($request->all());

        //return original values from request
        $data['state_id'] = $request->state_id;
        $data['lga_id'] = $request->lga_id;
        $data['ward_id'] = $request->ward_id;
        $data['facility_name'] = $request->facility_name;
        $data['geo_codes'] = $request->geo_codes;
        $data['facility_level_id'] = $request->facility_level_id;
        $data['ownership_id'] = $request->ownership_id;
        $data['operational_status_id'] = $request->operational_status_id;
        $data['registration_status_id'] = $request->registration_status_id;
        $data['license_status_id'] = $request->license_status_id;
        $data['accreditation_status_id'] = $request->accreditation_status_id;
        $data['searched'] = 1;

        return view('public.list_labs', compact('facilities', 'data'));
    }


    public function getImaging()
    {

        $facilities = DB::table('imaging_details')
            ->orderBy('state')
            ->orderBy('lga')
            ->orderBy('facility_name')
            ->paginate(20);

        //set values facility list when no filter
        $data['state_id'] = 1;
        $data['lga_id'] = 1;
        $data['ward_id'] = 1;
        $data['facility_name'] = "";
        $data['geo_codes'] = 0;
        $data['ownership_id'] = 0;
        $data['operational_status_id'] = 0;
        $data['registration_status_id'] = 0;
        $data['license_status_id'] = 0;
        $data['searched'] = 0;

        return view('public.list_imaging', compact('facilities', 'data'));
    }

    public function searchImaging(Request $request)
    {
        $state_id = $request->state_id;
        $lga_id = $request->lga_id;
        $ward_id = $request->ward_id;
        $facility_name = $request->facility_name;
        $ownership_id = $request->ownership_id;
        $operational_status_id = $request->operational_status_id;
        $registration_status_id = $request->registration_status_id;
        $license_status_id = $request->license_status_id;

        if ($request->geo_codes == 0) {
            $cond = "<>";
            $value = 'XXX';
        }
        if ($request->geo_codes == 1) {
            $cond = "<>";
            $value = '';
        }
        if ($request->geo_codes == 2) {
            $cond = "=";
            $value = '';
        }


        if ($ward_id == 0) {
            $ward_id = '';
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



        $facilities = DB::table('imaging_details')
            ->where('state_id', 'like', '%' . $state_id . '%')
            ->where('lga_id', 'like', '%' . $lga_id . '%')
            ->where(DB::Raw("IFNULL(ward_id, '')"), 'like', '%' . $ward_id . '%')
            ->where('ownership_id', 'like', '%' . $ownership_id . '%')
            ->where('operational_status_id', 'like', '%' . $operational_status_id . '%')
            ->where('registration_status_id', 'like', '%' . $registration_status_id . '%')
            ->where('license_status_id', 'like', '%' . $license_status_id . '%')
            ->Where('facility_name', 'like', '%' .  $facility_name . '%')
            ->where('latitude', $cond, $value)
            ->orderBy('state')
            ->orderBy('lga')
            ->orderBy('facility_name')
            ->paginate(20)
            ->appends($request->all());


        //return original values from request
        $data['state_id'] = $request->state_id;
        $data['lga_id'] = $request->lga_id;
        $data['ward_id'] = $request->ward_id;
        $data['facility_name'] = $request->facility_name;
        $data['geo_codes'] = $request->geo_codes;
        $data['ownership_id'] = $request->ownership_id;
        $data['operational_status_id'] = $request->operational_status_id;
        $data['registration_status_id'] = $request->registration_status_id;
        $data['license_status_id'] = $request->license_status_id;
        $data['searched'] = 1;


        return view('public.list_imaging', compact('facilities', 'data'));
    }


    public function getUpdates(Request $request)
    {

        //New Facilities Created This Month
        if ($request->report == 1) {
            $facilities = DB::table('hospital_details')
                ->where('created_at', '>=', Carbon::now()->startOfMonth())
                ->orderBy('state')
                ->orderBy('lga')
                ->orderBy('ward')
                ->orderBy('facility_name')
                ->get();
            $report = $facilities->count() . " Facilities were created this Month";
        }

        //New Facilities Created Last Month
        if ($request->report == 2) {
            $facilities = DB::table('hospital_details')
                ->whereBetween('created_at', [Carbon::now()->startOfMonth()->subMonth(), Carbon::now()->startOfMonth()])
                ->orderBy('state')
                ->orderBy('lga')
                ->orderBy('ward')
                ->orderBy('facility_name')
                ->get();
            $report = $facilities->count() . " Facilities were created in the last Month";
        }

        //New Facilities Created Last 3 Months
        if ($request->report == 3) {
            $facilities = DB::table('hospital_details')
                ->whereBetween('created_at', [Carbon::now()->subMonth(4), Carbon::now()->subMonth(1)])
                ->orderBy('state')
                ->orderBy('lga')
                ->orderBy('ward')
                ->orderBy('facility_name')
                ->get();
            $report = $facilities->count() . " Facilities were created in the last 3 Month";
        }

        //Facilities Updated This Month 
        if ($request->report == 4) {
            $facilities = DB::table('hospital_details')
                ->where('updated_at', '>=', Carbon::now()->startOfMonth())
                ->orderBy('state')
                ->orderBy('lga')
                ->orderBy('ward')
                ->orderBy('facility_name')
                ->get();

            $report = $facilities->count() . " Facilities were updated this Month";
        }

        //Facilities Updated Last Month
        if ($request->report == 5) {
            $facilities = DB::table('hospital_details')
                ->whereBetween('updated_at', [Carbon::now()->startOfMonth()->subMonth(), Carbon::now()->startOfMonth()])
                ->orderBy('state')
                ->orderBy('lga')
                ->orderBy('ward')
                ->orderBy('facility_name')
                ->get();
            $report = $facilities->count() . " Facilities were updated in the last Month";
        }

        //Facilities Updated Last 3 Month
        if ($request->report == 6) {
            $facilities = DB::table('hospital_details')
                ->whereBetween('updated_at', [Carbon::now()->subMonth(4), Carbon::now()->subMonth(1)])
                ->orderBy('state')
                ->orderBy('lga')
                ->orderBy('ward')
                ->orderBy('facility_name')
                ->get();
            $report = $facilities->count() . " Facilities were updated in the last 3 Month";
        }


        return view('public.facilities_updates', compact('facilities', 'report'));
    }

    public function updates()
    {
        $facilities = "none";

        return view('public.facilities_updates', compact('facilities'));
    }
}
