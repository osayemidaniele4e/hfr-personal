<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

use App\Models\Pharmacy;
use App\Models\Hospital;
use Illuminate\Support\Facades\Log;



/**
 * @group Administration Pharmacy
 *
 * Endpoints for listing, creating, updating, viewing,
 * and searching pharmacy facilities.
 *
 * Note:
 * - Most endpoints return Blade views for the Web UI.
 * - Some endpoints (e.g., show()) return HTML partials.
 * - JSON is returned only for error responses.
 */
class PharmacyController extends Controller
{
    public function indexold()
    {
        $pharmacies = DB::table('pharmacy_details')
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

        return view('pharmacy.index', compact(
            'pharmacies',
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


     /**
     * List Pharmacies
     *
     * Fetches a paginated list of pharmacies with joined metadata:
     * - State  
     * - LGA  
     * - Ward  
     * - Ownership  
     *
     * Returned in a Blade view for UI browsing.
     *
     * @response view pharmacy.index
     */
    public function index()
    {
        $pharmacies = DB::table('pharmacy_details')
            ->join('ou_states', 'pharmacy_details.state_id', '=', 'ou_states.id')
            ->join('ou_lgas', 'pharmacy_details.lga_id', '=', 'ou_lgas.id')
            ->join('ou_wards', 'pharmacy_details.ward_id', '=', 'ou_wards.id')
            ->join('lst_ownerships', 'pharmacy_details.ownership_id', '=', 'lst_ownerships.id')
            ->select(
                'pharmacy_details.*',
                'ou_states.name as state',
                'ou_lgas.name as lga',
                'ou_wards.name as ward',
                'lst_ownerships.name as ownership',
            )
            ->orderBy('ou_states.name')
            ->orderBy('ou_lgas.name')
            ->orderBy('pharmacy_details.facility_name')
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
        ) =
            [1, 1, "", 0, 0, 0, 0, 0, 0];

        return view('pharmacy.index', compact(
            'pharmacies',
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


 /**
     * Show Create Pharmacy Form
     *
     * Displays a form for registering a new pharmacy.
     *
     * @response view pharmacy.create
     */
    public function create()
    {
        return view('pharmacy.create');
    }


 /**
     * Create a Pharmacy Facility
     *
     * Validates and stores a new pharmacy record.
     *
     * @bodyParam facility_name string required The pharmacy name.
     * @bodyParam state_id integer required The state ID.
     * @bodyParam lga_id integer required The LGA ID.
     * @bodyParam ward_id integer required The ward ID.
     * @bodyParam ownership_id integer required Ownership ID.
     * @bodyParam ownership_type_id integer required Ownership type ID.
     * @bodyParam start_date date Example: 2024-01-01
     * @bodyParam operational_days array[] Example: ["Mon", "Tue"]
     *
     * @response redirect 302 Redirects back with success message.
     *
     * @response 500 {
     *   "error": "Database error message"
     * }
     */
    public function store(Request $request)
    {
        $request->validate([
            'registration_no' => 'nullable',
            'start_date' => 'nullable|date',
            'pharmacists_reg_number' => 'nullable',
            'facility_name' => 'required|max:200',
            'alt_facility_name' => 'nullable|max:200',
            'state_id' => 'required',
            'lga_id' => 'required',
            'ward_id' => 'required',
            'ownership_id' => 'required',
            'ownership_type_id' => 'required',
            'ownership_details' => 'nullable',
            'house_no' => 'nullable',
            'street_name' => 'nullable',
            'longitude' => 'nullable',
            'latitude' => 'nullable',
            'postal_address' => 'nullable',
            'phone_number' => 'nullable',
            'email_address' => 'nullable|email',
            'website' => 'nullable',
            'operational_days' => 'nullable',
            'operational_hours' => 'nullable',
            'operational_status_id' => 'required',
            'regulatory_status_id' => 'nullable',
            'license_status_id' => 'nullable',
            'outlet_category_id' => 'nullable',
            'premises_type_id' => 'nullable',
            'pharmacists' => 'nullable|numeric',
            'pharmacy_technicians' => 'nullable|numeric',
        ]);

        $start_date = date('Y-m-d', strtotime(str_replace('-', '/', $request->start_date)));

        $hosp = new Hospital;
        $ph = new Pharmacy;
        $ph->fill($request->all());
        $ph->unique_id = $hosp->generateFacilityCode($request->lga_id, '2', '0', $request->ownership_id);
        $ph->start_date = $start_date;
        $ph->operational_days = $hosp->arrayValuesTostring($request->operational_days);

        DB::beginTransaction();
        try {
            $ph->save();

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['error' => $ex->getMessage()], 500);
        }

        session()->flash("alert-success", "Pharmacy Information Saved Successfully!");
        return redirect()->back();
    }


 /**
     * Get Pharmacy Details
     *
     * Fetches and returns a detailed HTML partial view of a pharmacy.
     *
     * @urlParam id integer required The pharmacy ID.
     *
     * @response text/html
     * <div class="pharmacy-details">
     *    <!-- Rendered Blade partial -->
     * </div>
     *
     * @response 404 {
     *   "error": "Pharmacy not found"
     * }
     */
    public function show($id)
    {
        $pharmacy = DB::table('pharmacy_details')
            ->join('ou_states', 'pharmacy_details.state_id', '=', 'ou_states.id')
            ->join('ou_lgas', 'pharmacy_details.lga_id', '=', 'ou_lgas.id')
            ->join('ou_wards', 'pharmacy_details.ward_id', '=', 'ou_wards.id')
            ->join('lst_ownerships', 'pharmacy_details.ownership_id', '=', 'lst_ownerships.id')
            ->join('lst_ownership_types', 'pharmacy_details.ownership_type_id', '=', 'lst_ownership_types.id')
            // ->join('lst_level_of_care', 'laboratory_details.facility_level_id', '=', 'lst_level_of_care.id')
            ->select(
                'pharmacy_details.*',
                'ou_states.name as state',
                'ou_lgas.name as lga',
                'ou_wards.name as ward',
                'lst_ownerships.name as ownership',
                // 'lst_level_of_care.name as facility_level',
                'lst_ownership_types.type as ownership_type'
            )
            ->where('pharmacy_details.id', $id)
            ->first();

        if (!$pharmacy) {
            return response()->json(['error' => 'Pharmacy not found'], 404);
        }

        // Log::info(json_encode($pharmacy));

        $html = view('pharmacy.partials.details', compact('pharmacy'));
        return $html;
    }



 /**
     * Show Edit Pharmacy Form
     *
     * Displays the edit screen for a single pharmacy entry.
     *
     * @urlParam id integer required The ID of the pharmacy.
     *
     * @response view pharmacy.edit
     */
    public function edit($id)
    {
        $pharmacy = Pharmacy::findorfail($id);
        return view('pharmacy.edit', compact("pharmacy"));
    }


     /**
     * Update a Pharmacy Facility
     *
     * Validates and updates a pharmacy record.
     *
     * @urlParam id integer required
     *
     * @bodyParam facility_name string required The updated pharmacy name.
     * @bodyParam state_id integer required
     * @bodyParam lga_id integer required
     * @bodyParam ward_id integer required
     * @bodyParam ownership_id integer required
     * @bodyParam ownership_type_id integer required
     * @bodyParam start_date date Example: 2024-01-01
     * @bodyParam operational_days array[] Example: ["Mon","Tue"]
     *
     * @response redirect 302 Redirects back with success message.
     *
     * @response 500 {
     *   "error": "Database error message"
     * }
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'registration_no' => 'nullable',
            'start_date' => 'nullable|date',
            'pharmacists_reg_number' => 'nullable',
            'facility_name' => 'required|max:200',
            'alt_facility_name' => 'nullable|max:200',
            'state_id' => 'required',
            'lga_id' => 'required',
            'ward_id' => 'required',
            'ownership_id' => 'required',
            'ownership_type_id' => 'required',
            'ownership_details' => 'nullable',
            'house_no' => 'nullable',
            'street_name' => 'nullable',
            'longitude' => 'nullable',
            'latitude' => 'nullable',
            'postal_address' => 'nullable',
            'phone_number' => 'nullable',
            'email_address' => 'nullable|email',
            'website' => 'nullable',
            'operational_days' => 'nullable',
            'operational_hours' => 'nullable',
            'operational_status_id' => 'required',
            'regulatory_status_id' => 'nullable',
            'license_status_id' => 'nullable',
            'outlet_category_id' => 'nullable',
            'premises_type_id' => 'nullable',
            'pharmacists' => 'nullable|numeric',
            'pharmacy_technicians' => 'nullable|numeric',
        ]);

        $hosp = new Hospital;
        $ph = Pharmacy::findorfail($id);
        $ph->fill($request->all());
        $ph->start_date = date('Y-m-d', strtotime(str_replace('-', '/', $request->start_date)));
        $ph->operational_days = $hosp->arrayValuesTostring($request->operational_days);

        DB::beginTransaction();
        try {
            $ph->save();

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['error' => $ex->getMessage()], 500);
        }

        session()->flash("alert-success", "Pharmacy Information Updated Successfully!");
        return redirect()->back();
    }


  /**
     * Delete Pharmacy
     *
     * Deletes a pharmacy facility.
     *
     * @bodyParam fac_id integer required The pharmacy ID to delete.
     *
     * @response redirect 302 Redirects back with a success message.
     */
    public function destroy(Request $request)
    {
        Pharmacy::destroy($request->fac_id);
        session()->flash("alert-success", "Pharmacy deleted successfully!");
        return back();
    }


 /**
     * Search Pharmacies
     *
     * Performs an advanced search with multiple filters:
     * - State, LGA, Ward  
     * - Facility name  
     * - Geo-code completeness  
     * - Ownership  
     * - Operational status  
     * - Registration status  
     * - License status  
     *
     * Returns paginated results in UI view.
     *
     * @queryParam state_id integer Example: 25
     * @queryParam lga_id integer Example: 108
     * @queryParam ward_id integer Example: 0
     * @queryParam facility_name string Example: "Pharm Care"
     * @queryParam geo_codes integer 0=All, 1=Missing, 2=Present
     * @queryParam ownership_id integer Example: 2
     * @queryParam operational_status_id integer Example: 1
     *
     * @response view pharmacy.index
     */
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


        $pharmacies = DB::table('pharmacy_details')
            ->join('ou_states', 'pharmacy_details.state_id', '=', 'ou_states.id')
            ->join('ou_lgas', 'pharmacy_details.lga_id', '=', 'ou_lgas.id')
            ->join('ou_wards', 'pharmacy_details.ward_id', '=', 'ou_wards.id')
            ->join('lst_ownerships', 'pharmacy_details.ownership_id', '=', 'lst_ownerships.id')
            ->select(
                'pharmacy_details.*',
                'ou_states.name as state',
                'ou_lgas.name as lga',
                'ou_wards.name as ward',
                'lst_ownerships.name as ownership',
            )
            ->where('pharmacy_details.state_id', 'like', '%' . $state_id . '%')
            ->where('pharmacy_details.lga_id', 'like', '%' . $lga_id . '%')
            ->where(DB::Raw("IFNULL(pharmacy_details.ward_id, '')"), 'like', '%' . $ward_id . '%')
            ->where('pharmacy_details.ownership_id', 'like', '%' . $ownership_id . '%')
            ->where(DB::Raw("IFNULL(pharmacy_details.operational_status_id, '')"), 'like', '%' . $operational_status_id . '%')
            ->where(DB::Raw("IFNULL(pharmacy_details.registration_status_id, '')"), 'like', '%' . $registration_status_id . '%')
            ->where(DB::Raw("IFNULL(pharmacy_details.license_status_id, '')"), 'like', '%' . $license_status_id . '%')
            ->Where('pharmacy_details.facility_name', 'like', '%' .  $facility_name . '%')
            ->where(DB::Raw("IFNULL(pharmacy_details.latitude, '')"), $cond, $value)
            ->orderBy('pharmacy_details.state_id')
            ->orderBy('pharmacy_details.lga_id')
            ->orderBy('pharmacy_details.facility_name')
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


        return view('pharmacy.index', compact(
            'pharmacies',
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
