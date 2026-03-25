<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LaboratoryEquipment;

class EquipmentController extends Controller
{
 
    public function index()
    { 
        $equips =LaboratoryEquipment::orderBy('name','ASC')->get();
        return view('masters.lab_equipments.index',compact('equips')); 
    }

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

    public function destroy(Request $request)
    {
        LaboratoryEquipment::destroy($request->equip_id);
        session()->flash("alert-success", "Laboratory equipment deleted successfully!");
        return back();
    }

   
}
