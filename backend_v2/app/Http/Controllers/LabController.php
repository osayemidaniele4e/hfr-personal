<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Laboratory;
use App\Models\Hospital;
use Illuminate\Support\Facades\Log;


/**
 * @group Administration - Laboratory
 *
 * Endpoints for listing, creating, updating, viewing, and searching laboratory facilities.
 *
 * Note:
 * - Most methods return Blade views for the web UI.
 * - Some endpoints return an HTML partial (e.g., show()).
 * - API consumers should treat the responses as HTML unless explicitly JSON.
 */
class LabController extends Controller
{

    public function indexold()
    {
        $labs = DB::table('laboratory_details')
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
            $facility_level_id,
            $ownership_id,
            $operational_status_id,
            $registration_status_id,
            $license_status_id
        ) = [1, 1, "", 0, 0, 0, 0, 0, 0, 0, 0];

        return view('laboratory.index', compact(
            'labs',
            'state_id',
            'lga_id',
            'facility_name',
            'geo_codes',
            'ward_id',
            'facility_level_id',
            'ownership_id',
            'operational_status_id',
            'registration_status_id',
            'license_status_id'
        ));
    }


      /**
     * List Laboratories
     *
     * Fetches a paginated list of laboratory facilities with joined metadata:
     * - State  
     * - LGA  
     * - Ward  
     * - Ownership  
     * - Level of Care  
     *
     * @response view laboratory.index
     */
    public function index()
    {
        $labs = DB::table('laboratory_details')
            ->join('ou_states', 'laboratory_details.state_id', '=', 'ou_states.id')
            ->join('ou_lgas', 'laboratory_details.lga_id', '=', 'ou_lgas.id')
            ->join('ou_wards', 'laboratory_details.ward_id', '=', 'ou_wards.id')
            ->join('lst_ownerships', 'laboratory_details.ownership_id', '=', 'lst_ownerships.id')
            ->join('lst_level_of_care', 'laboratory_details.facility_level_id', '=', 'lst_level_of_care.id')
            ->select(
                'laboratory_details.*',
                'ou_states.name as state',
                'ou_lgas.name as lga',
                'ou_wards.name as ward',
                'lst_level_of_care.name as facility_level',
                'lst_ownerships.name as ownership',
            )
            ->orderBy('ou_states.name')
            ->orderBy('ou_lgas.name')
            ->orderBy('laboratory_details.facility_name')
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
            $facility_level_id,
            $license_status_id
        ) =
            [1, 1, "", 0, 0, 0, 0, 0, 0, 0];

        return view('laboratory.index', compact(
            'labs',
            'state_id',
            'lga_id',
            'facility_name',
            'geo_codes',
            'ward_id',
            'ownership_id',
            'operational_status_id',
            'registration_status_id',
            'facility_level_id',
            'license_status_id'
        ));
    }



     /**
     * Show Create Laboratory Form
     *
     * Displays a form for creating a new laboratory facility.
     *
     * @response view laboratory.create
     */
    public function create()
    {
        return view('laboratory.create');
    }



/**
     * Create a Laboratory Facility
     *
     * Validates and saves a new laboratory record.
     *
     * @bodyParam facility_name string required The facility name.
     * @bodyParam state_id integer required The state ID.
     * @bodyParam lga_id integer required The LGA ID.
     * @bodyParam ward_id integer required The ward ID.
     * @bodyParam facility_level_id integer required Level of care ID.
     * @bodyParam ownership_id integer required Ownership ID.
     * @bodyParam start_date date The starting operation date.
     *
     * @response redirect 302 Redirects to laboratory.index with success message.
     */
    public function store(Request $request)
    {
        // Log::info('Laboratory Store Request Data: ', $request->all());
        // dd($request->all());
        $rules = [
            'unique_id' => 'nullable',
            'registration_no' => 'nullable',
            'start_date' => 'nullable|date',
            'facility_name' => 'required',
            'alt_facility_name' => 'nullable',
            'state_id' => 'required',
            'lga_id' => 'required',
            'ward_id' => 'required',
            'email_address' => 'nullable|email',
            'website' => 'nullable',
            'operational_days' => 'nullable',
            'operational_hours' => 'nullable',
            'facility_level_id' => 'required',
            'ownership_id' => 'required',
            'phone_number' => 'nullable',
            'medical_laboratory_number' => 'numeric|nullable',
            'house_no' => 'nullable',
            'street_name' => 'nullable',
            'operational_status_id' => 'required',
            'registration_status_id' => 'nullable',
            'accreditation_status_id' => 'nullable',
            'license_status_id' => 'nullable',
            'laboratory_scientists' => 'nullable|numeric',
            'laboratory_technicians' => 'nullable|numeric',
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
        $lab = new Laboratory;
        $lab->fill($request->all());
        $lab->unique_id = $hosp->generateFacilityCode($request->lga_id, '3', $request->facility_level_id, $request->ownership_id);
        $lab->start_date = $start_date;
        $lab->operational_days = $hosp->arrayValuesTostring($request->operational_days);

        DB::beginTransaction();
        try {
            $lab->save();

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['error' => $ex->getMessage()], 500);
        }

        session()->flash("alert-success", "Laboratory Information Saved Successfully!");
        return redirect()->route('laboratory.index');
    }




 /**
     * Get Laboratory Details
     *
     * Returns a detailed summary of a laboratory facility.
     * The response is rendered as an HTML partial.
     *
     * @urlParam id integer required The laboratory ID.
     *
     * @response text/html
     * <div class="lab-details">
     *   <!-- Rendered Blade partial -->
     * </div>
     *
     * @response 404 {"error": "Laboratory not found"}
     */
    public function show($id)
    {
        // Log::info('Laboratory Show ID: ' . $id);
        $laboratory = DB::table('laboratory_details')
            ->join('ou_states', 'laboratory_details.state_id', '=', 'ou_states.id')
            ->join('ou_lgas', 'laboratory_details.lga_id', '=', 'ou_lgas.id')
            ->join('ou_wards', 'laboratory_details.ward_id', '=', 'ou_wards.id')
            ->join('lst_ownerships', 'laboratory_details.ownership_id', '=', 'lst_ownerships.id')
            ->join('lst_ownership_types', 'laboratory_details.ownership_type_id', '=', 'lst_ownership_types.id')
            ->join('lst_level_of_care', 'laboratory_details.facility_level_id', '=', 'lst_level_of_care.id')
            ->select(
                'laboratory_details.*',
                'ou_states.name as state',
                'ou_lgas.name as lga',
                'ou_wards.name as ward',
                'lst_ownerships.name as ownership',
                'lst_level_of_care.name as facility_level',
                'lst_ownership_types.type as ownership_type'
            )
            ->where('laboratory_details.id', $id)
            ->first();

        if (!$laboratory) {
            return response()->json(['error' => 'Laboratory not found'], 404);
        }

        // Log::info(json_encode($laboratory));
        $html = view('laboratory.partials.details', compact('laboratory'));
        return $html;
    }


  /**
     * Show Edit Laboratory Form
     *
     * Displays the edit screen for a laboratory record.
     *
     * @urlParam id integer required The laboratory ID.
     *
     * @response view laboratory.edit
     */
    public function edit($id)
    {
        $labs = Laboratory::findorfail($id);
        // Log::info('Laboratory Edit ID: ' . $id);
        return view('laboratory.edit', compact('labs'));
    }


 /**
     * Update a Laboratory Facility
     *
     * Validates and updates an existing laboratory record.
     *
     * @urlParam id integer required The laboratory ID.
     *
     * @bodyParam facility_name string required
     * @bodyParam facility_level_id integer required
     * @bodyParam ownership_id integer required
     *
     * @response redirect 302 Redirects to laboratory.index with success message.
     */
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
            'website' => 'nullable',
            'operational_days' => 'nullable',
            'operational_hours' => 'nullable',
            'facility_level_id' => 'required',
            'ownership_id' => 'required',
            'phone_number' => 'nullable',
            'medical_laboratory_number' => 'numeric|nullable',
            'house_no' => 'nullable',
            'street_name' => 'nullable',
            'operational_status_id' => 'required',
            'registration_status_id' => 'nullable',
            'accreditation_status_id' => 'nullable',
            'license_status_id' => 'nullable',
            'laboratory_scientists' => 'nullable|numeric',
            'laboratory_technicians' => 'nullable|numeric',
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
        $lab = Laboratory::findOrFail($id);
        $lab->fill($request->all());
        $lab->start_date = $start_date;
        $lab->operational_days = $hosp->arrayValuesTostring($request->operational_days);

        DB::beginTransaction();
        try {
            $lab->save();

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['error' => $ex->getMessage()], 500);
        }

        session()->flash("alert-success", "Laboratory Information Updated Successfully!");
        return redirect()->route('laboratory.index');
    }


     /**
     * Delete Laboratory Facility
     *
     * Deletes a laboratory from the system.
     *
     * @bodyParam fac_id integer required The facility ID.
     *
     * @response redirect 302 Redirects back with "deleted successfully" message.
     */
    public function destroy(Request $request)
    {
        Laboratory::destroy($request->fac_id);
        session()->flash("alert-success", "Laboratory facility deleted successfully!");
        return back();
    }



 /**
     * Search Laboratories
     *
     * Performs an advanced search using multiple filters:
     * - State, LGA, Ward  
     * - Facility name  
     * - Level of care  
     * - Ownership  
     * - Operational status  
     * - Registration & license status  
     * - Geo-code completeness  
     *
     * Returns paginated filtered results.
     *
     * @queryParam state_id integer Example: 25
     * @queryParam lga_id integer Example: 7
     * @queryParam ward_id integer Example: 0
     * @queryParam facility_name string Example: "General Hospital"
     * @queryParam geo_codes integer 0=All, 1=Missing, 2=Present
     *
     * @response view laboratory.index
     */
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


        $labs = DB::table('laboratory_details')
            ->join('ou_states', 'laboratory_details.state_id', '=', 'ou_states.id')
            ->join('ou_lgas', 'laboratory_details.lga_id', '=', 'ou_lgas.id')
            ->join('ou_wards', 'laboratory_details.ward_id', '=', 'ou_wards.id')
            ->join('lst_ownerships', 'laboratory_details.ownership_id', '=', 'lst_ownerships.id')
            ->join('lst_ownership_types', 'laboratory_details.ownership_type_id', '=', 'lst_ownership_types.id')
            ->join('lst_level_of_care', 'laboratory_details.facility_level_id', '=', 'lst_level_of_care.id')
            ->select(
                'laboratory_details.*',
                'ou_states.name as state',
                'ou_lgas.name as lga',
                'ou_wards.name as ward',
                'lst_ownerships.name as ownership',
                'lst_level_of_care.name as facility_level',
                'lst_ownership_types.type as ownership_type'
            )
            ->where('laboratory_details.state_id', 'like', '%' . $state_id . '%')
            ->where('laboratory_details.lga_id', 'like', '%' . $lga_id . '%')
            ->where(DB::Raw("IFNULL(laboratory_details.ward_id, '')"), 'like', '%' . $ward_id . '%')
            ->where('laboratory_details.facility_level_id', 'like', '%' . $facility_level_id . '%')
            ->where('laboratory_details.ownership_id', 'like', '%' . $ownership_id . '%')
            ->where('laboratory_details.operational_status_id', 'like', '%' . $operational_status_id . '%')
            ->where('laboratory_details.registration_status_id', 'like', '%' . $registration_status_id . '%')
            ->where('laboratory_details.license_status_id', 'like', '%' . $license_status_id . '%')
            ->Where('laboratory_details.facility_name', 'like', '%' .  $facility_name . '%')
            ->where(DB::Raw("IFNULL(laboratory_details.latitude, '')"), $cond, $value)
            ->orderBy('laboratory_details.state_id')
            ->orderBy('laboratory_details.lga_id')
            ->orderBy('laboratory_details.facility_name')
            ->paginate(20)
            ->appends($request->all());

        // dd($request->all());


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


        return view('laboratory.index', compact(
            'labs',
            'state_id',
            'lga_id',
            'facility_name',
            'geo_codes',
            'ward_id',
            'facility_level_id',
            'ownership_id',
            'operational_status_id',
            'registration_status_id',
            'license_status_id'
        ));
    }
}
