<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LaboratoryCertificate;

class CertificationController extends Controller
{

    public function index()
    {
        $certificates =LaboratoryCertificate::orderBy('name','ASC')->get();
        return view('masters.lab_certificates.index',compact('certificates')); 
    }

    public function store(Request $request)
    {              
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required',
        ]);


        $cer = new LaboratoryCertificate;
        $cer->name = $request->name;
        $cer->type = $request->type;
        $cer->save();

        session()->flash("alert-success", "Laboratory certification added successfully!");        
        return back();
    }

    public function update(Request $request)
    {
        $request->validate([
            'name1' => 'required|string|max:100',
            'type1' => 'required',
        ]);

        $cer = LaboratoryCertificate::find($request->id);
        $cer->name = $request->name1;
        $cer->type = $request->type1;
        $cer->save();
        session()->flash("alert-success", "Laboratory certification updated successfully!");
        return back();
    }

    public function destroy(Request $request)
    {
        LaboratoryCertificate::destroy($request->certification_id);
        session()->flash("alert-success", "Laboratory certification deleted successfully!");
        return back();
    }

}
