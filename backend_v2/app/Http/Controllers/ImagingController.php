<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Hospital;
use App\Models\Imaging;
use Illuminate\Support\Facades\Log;


/**
 * @group Administration Imaging
 *
 * Endpoints for listing, creating, updating, deleting,
 * viewing and searching imaging diagnostic facilities.
 *
 * Note:
 * - Most endpoints return Blade views.
 * - Some endpoints return HTML fragments.
 * - JSON is returned only for exceptions and missing records.
 */
class ImagingController extends Controller
{

    public function indexold()
    {
        $imagings = DB::table('imaging_details')
            ->orderBy('state')
            ->orderBy('lga')
            ->orderBy('facility_name')
            ->paginate(20);

        list(
            $state_id,
            $lga_id,
            $facility_name,
            $geo_codes,
            $ward_id,
            $ownership_id,
            $operational_status_id,
            $registration_status_id,
            $license_status_id
        ) = [1, 1, "", 0, 0, 0, 0, 0, 0, 0, 0];

        return view('imaging.index', compact(
            'imagings',
            'state_id',
            'lga_id',
            'facility_name',
            'geo_codes',
            'ward_id',
            'ownership_id',
            'operational_status_id',
            'registration_status_id',
            'license_status_id'
        ));
    }

    public function index()
    {
        $imagings = DB::table('imaging_details')
            ->join('ou_states', 'imaging_details.state_id', '=', 'ou_states.id')
            ->join('ou_lgas', 'imaging_details.lga_id', '=', 'ou_lgas.id')
            ->join('ou_wards', 'imaging_details.ward_id', '=', 'ou_wards.id')
            ->join('lst_ownerships', 'imaging_details.ownership_id', '=', 'lst_ownerships.id')
            // ->join('lst_level_of_care', 'imaging_details.facility_level_id', '=', 'lst_level_of_care.id')
            ->select(
                'imaging_details.*',
                'ou_states.name as state',
                'ou_lgas.name as lga',
                'ou_wards.name as ward',
                // 'lst_level_of_care.name as facility_level',
                'lst_ownerships.name as ownership',
            )
            ->orderBy('ou_states.name')
            ->orderBy('ou_lgas.name')
            ->orderBy('imaging_details.facility_name')
            ->paginate(20);

        list(
            $state_id,
            $lga_id,
            $facility_name,
            $geo_codes,
            $ward_id,
            $ownership_id,
            $operational_status_id,
            $registration_status_id,
            // $facility_level_id,
            $license_status_id
        ) =
            [1, 1, "", 0, 0, 0, 0, 0, 0, 0];

        return view('imaging.index', compact(
            'imagings',
            'state_id',
            'lga_id',
            'facility_name',
            'geo_codes',
            'ward_id',
            'ownership_id',
            'operational_status_id',
            'registration_status_id',
            // 'facility_level_id',
            'license_status_id'
        ));
    }

    public function create()
    {
        return view('imaging.create');
    }


    public function store(Request $request)
    {

        $rules = [
            'unique_id' => 'unique',
            'registration_no' => 'nullable',
            'start_date' => 'nullable|date',
            'facility_name' => 'required',
            'alt_facility_name' => 'nullable',
            'state_id' => 'required',
            'lga_id' => 'required',
            'ward_id' => 'required',
            'email_address' => 'nullable|email',
            'website' => 'nullable|url',
            'operational_days' => 'nullable',
            'operational_hours' => 'nullable',
            'ownership_id' => 'required',
            'phone_number' => 'nullable',
            'medical_laboratory_number' => 'numeric|nullable',
            'house_no' => 'nullable',
            'street_name' => 'nullable',
            'operational_status_id' => 'required',
            'registration_status_id' => 'nullable',
            'license_status_id' => 'nullable',
            'radiography_tech' => 'nullable|numeric',
            'radiographers' => 'nullable|numeric',
            'radiologists' => 'nullable|numeric',
            'quality_assurance' => 'nullable',
            'premises_type_id' => 'nullable',
            'postal_address' => 'nullable',
            'longitude' => 'nullable|numeric|between:2.483,20',
            'latitude' => 'nullable|numeric|between:3.883,13.867',
        ];

        $customMessages = [
            'state_id.required' => 'The State field is required',
            'lga_id.required' => 'The LGA field is required',
            'ownership_id.required' => 'The Ownership field is required',
            'operational_status_id.required' => 'The Operation status field is required',
            'premises_type_id.required' => 'The Instituion/ Standalone field is required',
        ];

        $this->validate($request, $rules, $customMessages);

        $start_date = date('Y-m-d', strtotime(str_replace('-', '/', $request->start_date)));

        $hosp = new Hospital;
        $im = new Imaging;
        $im->fill($request->all());
        $im->unique_id = $hosp->generateFacilityCode($request->lga_id, '4', $request->facility_level_id, $request->ownership_id);
        $im->start_date = $start_date;
        $im->operational_days = $hosp->arrayValuesTostring($request->operational_days);

        DB::beginTransaction();
        try {
            $im->save();

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['error' => $ex->getMessage()], 500);
        }

        session()->flash("alert-success", "Information Saved Successfully!");
        return redirect()->route('imaging.index');
    }


    public function show($id)
    {
        $imagings = DB::table('imaging_details')
            ->join('ou_states', 'imaging_details.state_id', '=', 'ou_states.id')
            ->join('ou_lgas', 'imaging_details.lga_id', '=', 'ou_lgas.id')
            ->join('ou_wards', 'imaging_details.ward_id', '=', 'ou_wards.id')
            ->join('lst_ownership_types', 'imaging_details.ownership_type_id', '=', 'lst_ownership_types.id')
            ->join('lst_ownerships', 'imaging_details.ownership_id', '=', 'lst_ownerships.id')
            // ->join('lst_level_of_care', 'imaging_details.facility_level_id', '=', 'lst_level_of_care.id')
            ->select(
                'imaging_details.*',
                'ou_states.name as state',
                'ou_lgas.name as lga',
                'ou_wards.name as ward',
                'lst_ownership_types.type as ownership_type',
                // 'lst_level_of_care.name as facility_level',
                'lst_ownerships.name as ownership',
            )
            ->where('imaging_details.id', $id)
            ->first();

        if (!$imagings) {
            return response()->json(['error' => 'Imagings not found'], 404);
        }

        // Log::info(json_encode($imagings));

        $html = view('imaging.partials.details', compact('imagings'));
        return $html;
    }

    public function edit($id)
    {
        $imagings = Imaging::findorfail($id);
        return view('imaging.edit', compact("imagings"));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'unique_id' => 'unique',
            'registration_no' => 'nullable',
            'start_date' => 'nullable|date',
            'facility_name' => 'required',
            'alt_facility_name' => 'nullable',
            'state_id' => 'required',
            'lga_id' => 'required',
            'ward_id' => 'required',
            'email_address' => 'nullable|email',
            'website' => 'nullable|url',
            'operational_days' => 'nullable',
            'operational_hours' => 'nullable',
            'ownership_id' => 'required',
            'phone_number' => 'nullable',
            'medical_laboratory_number' => 'numeric|nullable',
            'house_no' => 'nullable',
            'street_name' => 'nullable',
            'operational_status_id' => 'required',
            'registration_status_id' => 'nullable',
            'license_status_id' => 'nullable',
            'radiography_tech' => 'nullable|numeric',
            'radiographers' => 'nullable|numeric',
            'radiologists' => 'nullable|numeric',
            'quality_assurance' => 'nullable',
            'premises_type_id' => 'nullable',
            'postal_address' => 'nullable',
            'longitude' => 'nullable|numeric|between:2.483,20',
            'latitude' => 'nullable|numeric|between:3.883,13.867',
        ];

        $customMessages = [
            'state_id.required' => 'The State field is required',
            'lga_id.required' => 'The LGA field is required',
            'ownership_id.required' => 'The Ownership field is required',
            'operational_status_id.required' => 'The Operation status field is required',
            'premises_type_id.required' => 'The Instituion/ Standalone field is required',
        ];

        $this->validate($request, $rules, $customMessages);

        $start_date = date('Y-m-d', strtotime(str_replace('-', '/', $request->start_date)));

        $hosp = new Hospital;
        $im = Imaging::find($id);
        $im->fill($request->all());
        $im->start_date = $start_date;
        $im->operational_days = $hosp->arrayValuesTostring($request->operational_days);

        DB::beginTransaction();
        try {
            $im->save();

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['error' => $ex->getMessage()], 500);
        }

        session()->flash("alert-success", "Record Updated Successfully!");
        return redirect()->back();
    }


    public function destroy(Request $request)
    {
        Imaging::destroy($request->fac_id);
        session()->flash("alert-success", "Imaging facility deleted successfully!");
        return back();
    }

    public function search(Request $request)
    {
        $state_id = $request->state_id;
        $lga_id = $request->lga_id;
        $ward_id = $request->ward_id;
        $facility_name = $request->facility_name;
        $geo_codes = $request->geo_codes;
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


        $imagings = DB::table('imaging_details')
            ->join('ou_states', 'imaging_details.state_id', '=', 'ou_states.id')
            ->join('ou_lgas', 'imaging_details.lga_id', '=', 'ou_lgas.id')
            ->join('ou_wards', 'imaging_details.ward_id', '=', 'ou_wards.id')
            ->join('lst_ownership_types', 'imaging_details.ownership_type_id', '=', 'lst_ownership_types.id')
            ->join('lst_ownerships', 'imaging_details.ownership_id', '=', 'lst_ownerships.id')
            // ->join('lst_level_of_care', 'imaging_details.facility_level_id', '=', 'lst_level_of_care.id')
            ->select(
                'imaging_details.*',
                'ou_states.name as state',
                'ou_lgas.name as lga',
                'ou_wards.name as ward',
                'lst_ownership_types.type as ownership_type',
                // 'lst_level_of_care.name as facility_level',
                'lst_ownerships.name as ownership',
            )

            ->where('imaging_details.state_id', 'like', '%' . $state_id . '%')
            ->where('imaging_details.lga_id', 'like', '%' . $lga_id . '%')
            ->where(DB::Raw("IFNULL(imaging_details.ward_id, '')"), 'like', '%' . $ward_id . '%')
            ->where('imaging_details.ownership_id', 'like', '%' . $ownership_id . '%')
            ->where(DB::Raw("IFNULL(imaging_details.operational_status_id, '')"), 'like', '%' . $operational_status_id . '%')
            ->where(DB::Raw("IFNULL(imaging_details.registration_status_id, '')"), 'like', '%' . $registration_status_id . '%')
            ->where(DB::Raw("IFNULL(imaging_details.license_status_id, '')"), 'like', '%' . $license_status_id . '%')
            ->Where('imaging_details.facility_name', 'like', '%' .  $facility_name . '%')
            ->where(DB::Raw("IFNULL(imaging_details.latitude, '')"), $cond, $value)
            ->orderBy('imaging_details.state_id')
            ->orderBy('imaging_details.lga_id')
            ->orderBy('imaging_details.facility_name')
            ->paginate(20)
            ->appends($request->all());


        //return original values from request
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


        return view('imaging.index', compact(
            'imagings',
            'state_id',
            'lga_id',
            'facility_name',
            'geo_codes',
            'ward_id',
            'ownership_id',
            'operational_status_id',
            'registration_status_id',
            'license_status_id'
        ));
    }
}
