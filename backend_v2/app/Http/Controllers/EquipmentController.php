<?php

namespace App\Http\Controllers;

use App\Models\LaboratoryEquipment;
use Illuminate\Http\Request;


/**
 * @group Administration - Equipment
 *
 * APIs for managing laboratory equipment. Includes listing, creating, updating, and deleting equipment records.
 */
class EquipmentController extends Controller
{


    /**
     * List all laboratory equipment.
     *
     * @response 200 View with list of laboratory equipment
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $equips =LaboratoryEquipment::orderBy('name','ASC')->get();
        return view('masters.lab_equipments.index',compact('equips'));
    }


     /**
     * Add new laboratory equipment.
     *
     * @bodyParam name string required Name of the equipment. Example: Microscope
     *
     * @response 302 Redirect back with success message
     * @response 422 Validation error if `name` is missing or too long
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);


        $eq = new LaboratoryEquipment;
        $eq->name = $request->name;
        $eq->save();

        session()->flash("alert-success", "Laboratory equipment added successfully!");
        return back();
    }


    /**
     * Update an existing laboratory equipment.
     *
     * @bodyParam id integer required ID of the equipment to update. Example: 1
     * @bodyParam name1 string required New name of the equipment. Example: Centrifuge
     *
     * @response 302 Redirect back with success message
     * @response 422 Validation error if `name1` is missing or too long
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'name1' => 'required|string|max:100',
        ]);

        $eq = LaboratoryEquipment::find($request->id);
        $eq->name = $request->name1;
        $eq->save();
        session()->flash("alert-success", "Laboratory equipment updated successfully!");
        return back();
    }


     /**
     * Delete a laboratory equipment.
     *
     * @bodyParam equip_id integer required ID of the equipment to delete. Example: 1
     *
     * @response 302 Redirect back with success message
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        LaboratoryEquipment::destroy($request->equip_id);
        session()->flash("alert-success", "Laboratory equipment deleted successfully!");
        return back();
    }


}
