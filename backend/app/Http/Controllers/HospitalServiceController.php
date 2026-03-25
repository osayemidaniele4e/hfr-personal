<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\HospitalServiceMaster;

class HospitalServiceController extends Controller
{
    public function index()
    {
        $services =HospitalServiceMaster::orderBy('name','ASC')->get();
      
        return view('masters.hospital_services.index',compact('services')); 
    }

    public function store(Request $request)
    {              
        $request->validate([
            'service_category_id' => 'required',
            'name' => 'required|string|max:100',
        ]);


        $service = new HospitalServiceMaster;
        $service->name = $request->name;
        $service->service_category_id= $request->service_category_id;
        $service->save();

        session()->flash("alert-success", "Hospital service added successfully!");        
        return back();
    }

    public function update(Request $request)
    {
        $request->validate([
            'service_category_id1' => 'required',
            'name1' => 'required|string|max:100',
        ]);

        $service = HospitalServiceMaster::find($request->id1);
        $service->name = $request->name1;
        $service->service_category_id= $request->service_category_id1;
        $service->save();

        session()->flash("alert-success", "Hospital service updated successfully!");
        return back();
    }

    public function destroy(Request $request)
    {
        HospitalServiceMaster::destroy($request->service_id);
        session()->flash("alert-success", "Hospital service deleted successfully!");
        return back();
    }
       
}
