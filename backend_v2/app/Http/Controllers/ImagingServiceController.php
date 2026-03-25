<?php

namespace App\Http\Controllers;

use App\Models\ImagingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImagingServiceController extends Controller
{

    public function index()
    {
        $services = ImagingService::orderBy('name')->get();
        return view('masters.imaging_services.index', compact("services"));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);


        $service = new ImagingService;
        $service->name = $request->name;
        $service->save();

        session()->flash("alert-success", "Imaging service added successfully!");
        return redirect()->back();
    }

    public function update(Request $request)
    {
        $request->validate([
            'service_name1' => 'required|max:100',
        ]);

        $service = ImagingService::findOrFail($request->id);
        $service->name = $request->service_name1;
        $service->save();

        session()->flash("alert-success", "Service updated successfully!");
        return back();
    }

    public function destroy(Request $request)
    {
        ImagingService::destroy($request->service_id);
        session()->flash("alert-success", "Service deleted successfully!");
        return back();
    }
}
