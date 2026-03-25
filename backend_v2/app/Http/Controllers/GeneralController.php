<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


/**
 * @Administration - General
 *
 * Provides AJAX endpoints for retrieving LGAs, wards, ownership types,
 * facility level options, and related dropdown values.
 *
 * **Note:**  
 * All endpoints return an HTML `<option>` list, not JSON.
 */
class GeneralController extends Controller
{

     /**
     * Get LGAs by State
     *
     * Returns a list of LGAs belonging to the selected state as HTML `<option>` elements.
     *
     * @bodyParam id integer required The ID of the state.
     *
     * @response text/html
     * <option value="">--Select LGA--</option>
     * <option value="1">Abeokuta North</option>
     * <option value="2">Abeokuta South</option>
     */
    public function getLgaList(Request $request)
    {
        $data = DB::table('ou_lgas')
            ->select('name', 'id')
            ->where('state_id', $request->id)
            ->orderByRaw('name')
            ->get();

        $output = '<option value="">--Select LGA--</option>';
        foreach ($data as $row) {
            $output .= '<option value="' . $row->id . '">' . $row->name . '</option>';
        }
        return $output;
    }


     /**
     * Get Wards by LGA
     *
     * Returns wards belonging to the selected LGA.
     *
     * @bodyParam lgaId integer required The ID of the LGA.
     *
     * @response text/html
     * <option value="">--Select Ward--</option>
     * <option value="10">Ward A</option>
     */
    public function getWardList(Request $request)
    {
        $data = DB::table('ou_wards')
            ->select('name', 'id')
            ->where('lga_id', $request->lgaId)
            ->orderByRaw('name')
            ->get();

        $output = '<option value="">--Select Ward--</option>';
        foreach ($data as $row) {
            $output .= '<option value="' . $row->id . '">' . $row->name . '</option>';
        }
        return $output;
    }


      /**
     * Get Ownership Types
     *
     * Retrieves ownership type options under a parent ownership category.
     *
     * @bodyParam ownership_id integer required The ownership group ID.
     *
     * @response text/html
     * <option value="">--Select Ownership Type--</option>
     * <option value="3">Private</option>
     */
    public function getOwnershipType(Request $request)
    {
        $data = DB::table('lst_ownership_types')
            ->select('id', 'type')
            ->where('ownership_id', $request->ownership_id)
            ->orderByRaw('id')
            ->get();

        $output = '<option value="">--Select Ownership Type--</option>';
        foreach ($data as $row) {
            $output .= '<option value="' . $row->id . '">' . $row->type . '</option>';
        }
        return $output;
    }


     /**
     * Get Facility Level Options
     *
     * Returns dropdown options for a selected level of care.
     *
     * @bodyParam id integer required The ID of the level of care.
     *
     * @response text/html
     * <option value="0">--Select Option--</option>
     * <option value="21">Primary Care Option</option>
     */
    public function getFacilityLevelOption(Request $request)
    {
        $data = DB::table('lst_level_of_care_options')
            ->select('id', 'description')
            ->where('level_of_care_id', $request->id)
            ->orderByRaw('id')
            ->get();

        $output = '<option value="0">--Select Option--</option>';
        foreach ($data as $row) {
            $output .= '<option value="' . $row->id . '">' . $row->description . '</option>';
        }
        return $output;
    }


     /**
     * Get Specialized Level Options
     *
     * Returns all specialized level-of-care options.
     *
     * @response text/html
     * <option value="0">--Select Option--</option>
     * <option value="5">Tertiary Care</option>
     */
    public function getSpecializedOptions()
    {
        $data = DB::table('lst_level_of_care_options_category')
            ->select('id', 'name')
            ->orderByRaw('id')
            ->get();

        $output = '<option value="0">--Select Option--</option>';
        foreach ($data as $row) {
            $output .= '<option value="' . $row->id . '">' . $row->name . '</option>';
        }
        return $output;
    }


     /**
     * Get Services by Category
     *
     * Returns hospital service options for a selected service category.
     *
     * @bodyParam id integer required The ID of the service category.
     *
     * @response text/html
     * <option value="0">--Select Services--</option>
     * <option value="15">Radiology</option>
     */
    public function getServices(Request $request)
    {
        $data = DB::table('lst_hosp_services')
            ->select('id', 'name')
            ->where('service_category_id', $request->id)
            ->orderByRaw('id')
            ->get();

        $output = '<option value="0">--Select Services--</option>';
        foreach ($data as $row) {
            $output .= '<option value="' . $row->id . '">' . $row->name . '</option>';
        }
        return $output;
    }
}
